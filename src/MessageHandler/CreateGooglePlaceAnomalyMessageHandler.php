<?php

namespace App\MessageHandler;

use App\Lists\EnergyStationStatusReference;
use App\Message\CreateGooglePlaceAnomalyMessage;
use App\Repository\EnergyStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler()]
final class CreateGooglePlaceAnomalyMessageHandler
{
    public function __construct(
        private EntityManagerInterface  $em,
        private EnergyStationRepository $energyStationRepository
    )
    {
    }

    public function __invoke(CreateGooglePlaceAnomalyMessage $message): void
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $energyStation = $this->energyStationRepository->findOneBy(['energyStationId' => $message->getEnergyStationId()->getId()]);

        if (null === $energyStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Energy Station doesnt exist (id : %s)', $message->getEnergyStationId()->getId()));
        }

        if (EnergyStationStatusReference::PLACE_ID_ANOMALY === $energyStation->getStatus()) {
            throw new UnrecoverableMessageHandlingException(sprintf('Energy Station has already PLACE_ID_ANOMALY status (id : %s)', $message->getEnergyStationId()->getId()));
        }

        $energyStation->setStatus(EnergyStationStatusReference::PLACE_ID_ANOMALY);

        $this->em->persist($energyStation);
        $this->em->flush();
    }
}
