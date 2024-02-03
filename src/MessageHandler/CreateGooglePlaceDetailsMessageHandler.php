<?php

namespace App\MessageHandler;

use App\Entity\EnergyStation;
use App\Lists\EnergyStationStatusReference;
use App\Message\CreateGooglePlaceDetailsMessage;
use App\Repository\EnergyStationRepository;
use App\Service\EnergyStationService;
use App\Service\GooglePlaceApiService;
use App\Service\GooglePlaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler()]
final class CreateGooglePlaceDetailsMessageHandler
{
    public function __construct(
        private EntityManagerInterface           $em,
        private readonly EnergyStationRepository $energyStationRepository,
        private readonly GooglePlaceApiService   $googlePlaceApiService,
        private readonly GooglePlaceService      $googlePlaceService,
        private readonly EnergyStationService    $energyStationService
    )
    {
    }

    public function __invoke(CreateGooglePlaceDetailsMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $energyStation = $this->energyStationRepository->findOneBy(['energyStationId' => $message->getEnergyStationId()->getId()]);

        if (null === $energyStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Energy Station doesnt exist (id : %s)', $message->getEnergyStationId()->getId()));
        }

        if (!in_array($energyStation->getStatus(), [
            EnergyStationStatusReference::FOUND_IN_TEXTSEARCH,
            EnergyStationStatusReference::UPDATED_TO_FOUND_IN_DETAILS,
            EnergyStationStatusReference::NOT_FOUND_IN_DETAILS,
            EnergyStationStatusReference::VALIDATION_REJECTED,
        ])) {
            throw new UnrecoverableMessageHandlingException(sprintf('Wrong status for Energy Station (energyStationId : %s)', $message->getEnergyStationId()->getId()));
        }

        if (EnergyStationStatusReference::PLACE_ID_ANOMALY === $energyStation->getStatus()) {
            return true;
        }

        $response = $this->googlePlaceApiService->placeDetails($energyStation);

        if (null === $response) {
            return $this->energyStationService->setEnergyStationStatus($energyStation, EnergyStationStatusReference::NOT_FOUND_IN_DETAILS);
        }

        $energyStationPlaceId = $this->energyStationRepository->findEnergyStationByPlaceIdAndStatus($response['place_id'], EnergyStationStatusReference::OPEN);

        if ($energyStationPlaceId instanceof EnergyStation) {
            return $this->energyStationService->setEnergyStationStatus($energyStation, EnergyStationStatusReference::PLACE_ID_ALREADY_FOUND);
        }

        $energyStation->setName(htmlspecialchars_decode(ucwords(strtolower(trim($response['name'] ?? null)))));
        $this->googlePlaceService->updateEnergyStationGooglePlace($energyStation, $response);
        $this->googlePlaceService->updateEnergyStationAddress($energyStation, $response);

        $this->energyStationService->setEnergyStationStatus($energyStation, EnergyStationStatusReference::FOUND_IN_DETAILS);

        return $this->energyStationService->setEnergyStationStatus($energyStation, EnergyStationStatusReference::WAITING_VALIDATION);
    }
}
