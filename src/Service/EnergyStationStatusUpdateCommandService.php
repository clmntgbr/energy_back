<?php

namespace App\Service;

use App\Entity\EnergyStation;
use App\Entity\EntityId\AddressId;
use App\Entity\EntityId\EnergyStationId;
use App\Lists\EnergyStationStatusReference;
use App\Message\CreateGooglePlaceDetailsMessage;
use App\Message\CreateGooglePlaceTextsearchMessage;
use App\Message\GeocodingAddressMessage;
use App\Repository\EnergyStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class EnergyStationStatusUpdateCommandService
{
    public const MAX_RETRY_POSITION_STACK = 5;
    public const MAX_RETRY_TEXT_SEARCH = 5;
    public const MAX_RETRY_PLACE_DETAILS = 5;

    public function __construct(
        private readonly EnergyStationRepository $energyStationRepository,
        private readonly MessageBusInterface     $messageBus,
        private readonly EntityManagerInterface  $em
    )
    {
    }

    public function invoke(array $energyStationIds): void
    {
        $energyStations = $this->getEnergyStations($energyStationIds);

        foreach ($energyStations as $energyStation) {
            match ($energyStation->getStatus()) {
                EnergyStationStatusReference::CREATED => $this->created($energyStation),
                EnergyStationStatusReference::UPDATED_TO_ADDRESS_FORMATED => $this->created($energyStation),
                EnergyStationStatusReference::ADDRESS_ERROR_FORMATED => $this->created($energyStation),

                EnergyStationStatusReference::ADDRESS_FORMATED => $this->textSearch($energyStation),
                EnergyStationStatusReference::UPDATED_TO_FOUND_IN_TEXTSEARCH => $this->textSearch($energyStation),
                EnergyStationStatusReference::NOT_FOUND_IN_TEXTSEARCH => $this->textSearch($energyStation),

                EnergyStationStatusReference::FOUND_IN_TEXTSEARCH => $this->detailsSearch($energyStation),
                EnergyStationStatusReference::UPDATED_TO_FOUND_IN_DETAILS => $this->detailsSearch($energyStation),
                EnergyStationStatusReference::NOT_FOUND_IN_DETAILS => $this->detailsSearch($energyStation),

                default => '',
            };
        }
    }

    private function getEnergyStations(array $energyStationIds)
    {
        if (empty($energyStationIds)) {
            return $this->energyStationRepository->findAll();
        }

        return $this->energyStationRepository->findEnergyStationByIds($energyStationIds);
    }

    private function created(EnergyStation $energyStation): void
    {
        if ($energyStation->getMaxRetryPositionStack() > self::MAX_RETRY_POSITION_STACK) {
            return;
        }

        $energyStation->addMaxRetryPositionStack();
        $this->em->persist($energyStation);
        $this->em->flush();
        $this->messageBus->dispatch(
            new GeocodingAddressMessage(new AddressId($energyStation->getAddress()->getId()), new EnergyStationId($energyStation->getEnergyStationId()))
        );
    }

    private function textSearch(EnergyStation $energyStation): void
    {
        if ($energyStation->getMaxRetryTextSearch() > self::MAX_RETRY_TEXT_SEARCH) {
            return;
        }

        $energyStation->addMaxRetryTextSearch();
        $this->em->persist($energyStation);
        $this->em->flush();
        $this->messageBus->dispatch(
            new CreateGooglePlaceTextsearchMessage(new EnergyStationId($energyStation->getEnergyStationId()))
        );
    }

    private function detailsSearch(EnergyStation $energyStation): void
    {
        if ($energyStation->getMaxRetryPlaceDetails() > self::MAX_RETRY_PLACE_DETAILS) {
            return;
        }

        $energyStation->addMaxRetryPlaceDetails();
        $this->em->persist($energyStation);
        $this->em->flush();
        $this->messageBus->dispatch(
            new CreateGooglePlaceDetailsMessage(new EnergyStationId($energyStation->getEnergyStationId()))
        );
    }
}
