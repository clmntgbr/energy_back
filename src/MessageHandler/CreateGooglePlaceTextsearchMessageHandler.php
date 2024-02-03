<?php

namespace App\MessageHandler;

use App\Entity\EnergyStation;
use App\Entity\EntityId\EnergyStationId;
use App\Lists\EnergyStationStatusReference;
use App\Message\CreateGooglePlaceDetailsMessage;
use App\Message\CreateGooglePlaceTextsearchMessage;
use App\Repository\EnergyStationRepository;
use App\Service\EnergyStationService;
use App\Service\GooglePlaceApiService;
use App\Service\GooglePlaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler()]
final class CreateGooglePlaceTextsearchMessageHandler
{
    public function __construct(
        private EntityManagerInterface           $em,
        private readonly EnergyStationRepository $energyStationRepository,
        private readonly EnergyStationService    $energyStationService,
        private readonly GooglePlaceService      $googlePlaceService,
        private readonly MessageBusInterface     $messageBus,
        private readonly GooglePlaceApiService   $googlePlaceApiService
    )
    {
    }

    public function __invoke(CreateGooglePlaceTextsearchMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $energyStation = $this->energyStationRepository->findOneBy(['energyStationId' => $message->getEnergyStationId()->getId()]);

        if (null === $energyStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Energy Station doesnt exist (id : %s)', $message->getEnergyStationId()->getId()));
        }

        if (!in_array($energyStation->getStatus(), [
            EnergyStationStatusReference::ADDRESS_FORMATED,
            EnergyStationStatusReference::UPDATED_TO_FOUND_IN_TEXTSEARCH,
            EnergyStationStatusReference::NOT_FOUND_IN_TEXTSEARCH,
            EnergyStationStatusReference::VALIDATION_REJECTED,
        ])) {
            throw new UnrecoverableMessageHandlingException(sprintf('Wrong status for Energy Station (energyStationId : %s)', $message->getEnergyStationId()->getId()));
        }

        if (EnergyStationStatusReference::PLACE_ID_ANOMALY === $energyStation->getStatus()) {
            return true;
        }

        $response = $this->googlePlaceApiService->placeTextsearch($energyStation);

        if (null === $response) {
            return $this->energyStationService->setEnergyStationStatus($energyStation, EnergyStationStatusReference::NOT_FOUND_IN_TEXTSEARCH);
        }

        $energyStationPlaceId = $this->energyStationRepository->findEnergyStationByPlaceIdAndStatus($response, EnergyStationStatusReference::OPEN);

        if ($energyStationPlaceId instanceof EnergyStation) {
            return $this->energyStationService->setEnergyStationStatus($energyStation, EnergyStationStatusReference::PLACE_ID_ALREADY_FOUND);
        }

        $energyStation->getGooglePlace()->setPlaceId($response);
        $this->energyStationService->setEnergyStationStatus($energyStation, EnergyStationStatusReference::FOUND_IN_TEXTSEARCH);

        $energyStationsAnomalies = $this->energyStationRepository->getEnergyStationGooglePlaceByPlaceId($energyStation);

        if (count($energyStationsAnomalies) > 0) {
            return $this->googlePlaceService->createAnomalies(array_merge($energyStationsAnomalies, [$energyStation]));
        }

        return $this->messageBus->dispatch(
            new CreateGooglePlaceDetailsMessage(
                new EnergyStationId($energyStation->getEnergyStationId())
            )
        );
    }
}
