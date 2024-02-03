<?php

namespace App\MessageHandler;

use App\Message\UpdateEnergyStationClosedMessage;
use App\Repository\EnergyStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler()]
final class UpdateEnergyStationClosedMessageHandler
{
    public function __construct(
        private EntityManagerInterface           $em,
        private readonly EnergyStationRepository $energyStationRepository
    )
    {
    }

    public function __invoke(UpdateEnergyStationClosedMessage $message): void
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $energyStation = $this->energyStationRepository->findOneBy(['energyStationId' => $message->getEnergyStationId()->getId()]);

        if (null === $energyStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Energy Station is null (id: %s)', $message->getEnergyStationId()->getId()));
        }

        $this->em->persist($energyStation);
        $this->em->flush();
    }
}
