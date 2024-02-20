<?php

namespace App\MessageHandler;

use App\Entity\EnergyStation;
use App\Lists\EnergyStationStatusReference;
use App\Message\CreateGooglePlaceTextsearchMessage;
use App\Message\ErrorGeocodingAddressMessage;
use App\Message\GeocodingAddressMessage;
use App\Repository\AddressRepository;
use App\Repository\EnergyStationRepository;
use App\Service\AddressService;
use App\Service\EnergyStationService;
use App\Service\PositionStackApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler()]
final class GeocodingAddressMessageHandler
{
    public const CONFIDENCE_ERROR = 0.8;

    public function __construct(
        private EntityManagerInterface           $em,
        private readonly AddressRepository       $addressRepository,
        private readonly PositionStackApiService $positionStackApiService,
        private readonly MessageBusInterface     $messageBus,
        private readonly AddressService          $addressService,
        private readonly EnergyStationService    $energyStationService,
        private readonly EnergyStationRepository $energyStationRepository
    )
    {
    }

    public function __invoke(GeocodingAddressMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $address = $this->addressRepository->findOneBy(['id' => $message->getAddressId()->getId()]);

        if (null === $address) {
            throw new UnrecoverableMessageHandlingException(sprintf('Address is null (id: %s)', $message->getAddressId()->getId()));
        }

        $energyStation = $this->energyStationRepository->findOneBy(['energyStationId' => $message->getEnergyStationId()->getId()]);

        if (!$energyStation instanceof EnergyStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Energy Station doesn\'t exist (energyStationId : %s)', $message->getEnergyStationId()->getId()));
        }

        if (!in_array($energyStation->getStatus(), [
            EnergyStationStatusReference::CREATED,
            EnergyStationStatusReference::UPDATED_TO_ADDRESS_FORMATED,
            EnergyStationStatusReference::ADDRESS_ERROR_FORMATED,
        ])) {
            throw new UnrecoverableMessageHandlingException(sprintf('Wrong status for Energy Station (energyStationId : %s)', $message->getEnergyStationId()->getId()));
        }

        $data = $this->positionStackApiService->forward($address);

        if (null === $data) {
            return $this->messageBus->dispatch(new ErrorGeocodingAddressMessage($message->getAddressId(), $message->getEnergyStationId()));
        }

        $positionStackApiResult = $address->getPositionStackApiResult();
        $positionStackApiResult['forward_api'] = $data;
        $address->setPositionStackApiResult($positionStackApiResult);

        $this->em->persist($address);
        $this->em->flush();

        if (!array_key_exists('confidence', $data)) {
            return $this->messageBus->dispatch(new ErrorGeocodingAddressMessage($message->getAddressId(), $message->getEnergyStationId()));
        }

        if ($data['confidence'] < self::CONFIDENCE_ERROR) {
            return $this->messageBus->dispatch(new ErrorGeocodingAddressMessage($message->getAddressId(), $message->getEnergyStationId()));
        }

        $this->addressService->hydrate($address, $data);
        $this->energyStationService->setEnergyStationStatus($energyStation, EnergyStationStatusReference::ADDRESS_FORMATED);

//        return $this->messageBus->dispatch(new CreateGooglePlaceTextsearchMessage($message->getEnergyStationId()));
    }
}
