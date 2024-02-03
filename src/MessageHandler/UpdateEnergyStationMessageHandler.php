<?php

namespace App\MessageHandler;

use App\Entity\EnergyStation;
use App\Message\UpdateEnergyStationMessage;
use App\Repository\EnergyStationRepository;
use App\Service\EnergyStationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler]
final class UpdateEnergyStationMessageHandler
{
    public function __construct(
        private EntityManagerInterface           $em,
        private readonly EnergyStationRepository $energyStationRepository,
        private readonly EnergyStationService    $energyStationService,
    )
    {
    }

    public function __invoke(UpdateEnergyStationMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $energyStation = $this->energyStationRepository->findOneBy(['energyStationId' => $message->getEnergyStationId()->getId()]);

        if (!$energyStation instanceof EnergyStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Energy Station doesn\'t exist (energyStationId : %s)', $message->getEnergyStationId()->getId()));
        }

        $element = $message->getElement();

        $energyStation
            ->setHash($message->getHash())
            ->initServices();

        $this->energyStationService->createEnergyStationServices($energyStation, $element);

        $this->em->persist($energyStation);
        $this->em->flush();
    }
}
