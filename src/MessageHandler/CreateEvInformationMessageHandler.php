<?php

namespace App\MessageHandler;

use App\Entity\EnergyStation;
use App\Entity\EvInformation;
use App\Message\CreateEvInformationMessage;
use App\Repository\EnergyStationBrandRepository;
use App\Repository\EnergyStationRepository;
use App\Repository\UserRepository;
use App\Service\EnergyStationService;
use App\Service\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class CreateEvInformationMessageHandler
{
    public function __construct(
        private EntityManagerInterface                $em,
        private readonly EnergyStationRepository      $energyStationRepository
    )
    {
    }

    public function __invoke(CreateEvInformationMessage $message): void
    {
        $energyStation = $this->energyStationRepository->findOneBy(['energyStationId' => $message->getEnergyStationId()->getId()]);

        if (!$energyStation instanceof EnergyStation) {
            return;
        }

        $element = $message->getElement();

        $evInformation = new EvInformation();
        $evInformation
            ->setUuid(Uuid::v4())
            ->setAccessibility($element['accessibilite'] ?? null)
            ->setChargingAccess($element['acces_recharge'] ?? null)
            ->setNumberRechargePoint($element['nbre_pdc'] ?? null)
            ->setMaximumPower($element['puiss_max'] ?? null)
            ->setTypeOfCharging($element['type_prise'] ?? null)
            ->setObservations($element['observations'] ?? null)
        ;

        $energyStation->setEvInformation($evInformation);
        
        $this->em->persist($evInformation);
        $this->em->persist($energyStation);
        $this->em->flush();
    }
}
