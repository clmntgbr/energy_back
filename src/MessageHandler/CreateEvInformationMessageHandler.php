<?php

namespace App\MessageHandler;

use App\Entity\EnergyStation;
use App\Entity\EvInformation;
use App\Entity\EvRechargePoint;
use App\Message\CreateEvInformationMessage;
use App\Repository\EnergyStationBrandRepository;
use App\Repository\EnergyStationRepository;
use App\Repository\UserRepository;
use App\Service\EnergyStationService;
use App\Service\Uuid;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class CreateEvInformationMessageHandler
{
    public function __construct(
        private EntityManagerInterface                $em,
        private readonly EnergyStationRepository      $energyStationRepository
    ) {
    }

    public function __invoke(CreateEvInformationMessage $message): void
    {
        $energyStation = $this->energyStationRepository->findOneBy(['energyStationId' => $message->getEnergyStationId()->getId()]);

        if (!$energyStation instanceof EnergyStation) {
            return;
        }

        $element = $message->getElement();

        $evInformation = new EvInformation();

        $maxPower = 0;
        foreach ($element['Connections'] as $key => $value) {
            $evRechargePoint = new EvRechargePoint();
            $evRechargePoint
                ->setUuid(Uuid::v4())
                ->setEvinformation($evInformation)
                ->setIsFastChargeCapable($value['Level']['IsFastChargeCapable'] ?? null)
                ->setLevel($value['Level']['Title'] ?? null)
                ->setPowerKW($value['PowerKW'] ?? null)
                ->setTypeOfCharging($value['ConnectionType']['Title'] ?? null)
                ->setQuantity($value['Quantity'] ?? null);

            $evInformation->addEvRechargePoint($evRechargePoint);

            if ($evRechargePoint->getPowerKW() > $maxPower) {
                $maxPower = $evRechargePoint->getPowerKW();
            }

            $this->em->persist($evRechargePoint);
        }

        $date = DateTime::createFromFormat(DateTime::ATOM, $element['DateCreated']);

        $evInformation
            ->setUuid(Uuid::v4())
            ->setNumberRechargePoint(count($element['Connections']))
            ->setDateCreated($date ?? null)
            ->setMaximumPower($maxPower);

        $energyStation->setEvInformation($evInformation);

        $this->em->persist($evInformation);
        $this->em->persist($energyStation);
        $this->em->flush();
    }
}
