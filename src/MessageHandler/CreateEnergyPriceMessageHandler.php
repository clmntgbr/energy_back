<?php

namespace App\MessageHandler;

use App\Entity\EnergyPrice;
use App\Entity\EnergyStation;
use App\Entity\EnergyType;
use App\Lists\CurrencyReference;
use App\Message\CreateEnergyPriceMessage;
use App\Repository\CurrencyRepository;
use App\Repository\EnergyPriceRepository;
use App\Repository\EnergyStationRepository;
use App\Repository\EnergyTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Safe\DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler]
final class CreateEnergyPriceMessageHandler
{
    public function __construct(
        private EntityManagerInterface           $em,
        private readonly EnergyStationRepository $energyStationRepository,
        private readonly EnergyTypeRepository    $energyTypeRepository,
        private readonly EnergyPriceRepository   $energyPriceRepository,
        private readonly CurrencyRepository      $currencyRepository
    )
    {
    }

    public function __invoke(CreateEnergyPriceMessage $message): void
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $energyStation = $this->energyStationRepository->findOneBy(['energyStationId' => $message->getEnergyStationId()->getId()]);

        if (null === $energyStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Energy Station is null (id: %s)', $message->getEnergyStationId()->getId()));
        }

        $energyType = $this->energyTypeRepository->findOneBy(['id' => $message->getEnergyTypeId()->getId()]);

        if (null === $energyType) {
            throw new UnrecoverableMessageHandlingException(sprintf('Energy Type is null (id: %s, EnergyStationId: %s)', $message->getEnergyTypeId()->getId(), $message->getEnergyStationId()->getId()));
        }

        $currency = $this->currencyRepository->findOneBy(['reference' => CurrencyReference::EUR]);

        if (null === $currency) {
            throw new UnrecoverableMessageHandlingException('Currency is null (reference: eur)');
        }

        if (null == $message->getValue()) {
            throw new UnrecoverableMessageHandlingException(sprintf('Value is null (id: %s)', $message->getValue()));
        }

        if (null == $message->getDate()) {
            throw new UnrecoverableMessageHandlingException(sprintf('Date is null (id: %s)', $message->getDate()));
        }

        $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', str_replace('T', ' ', substr($message->getDate(), 0, 19)), new \DateTimeZone('Europe/Paris'));

        $energyPrice = $this->energyPriceRepository->findOneBy(['dateTimestamp' => $date->getTimestamp(), 'energyType' => $energyType, 'energyStation' => $energyStation]);
        if ($energyPrice instanceof EnergyPrice) {
            return;
        }

        $energyPriceValue = (int)str_replace([',', '.'], '', $message->getValue());
        $lastEnergyPrices = $energyStation->getLastEnergyPrices();

        if (!$this->hasToBeCreated($lastEnergyPrices, $energyPriceValue, $energyType, $date)) {
            return;
        }

        $energyPrice = new EnergyPrice();
        $energyPrice
            ->setCurrency($currency)
            ->setEnergyType($energyType)
            ->setEnergyStation($energyStation)
            ->setDate($date)
            ->setDateTimestamp($date->getTimestamp())
            ->setValue($energyPriceValue);

        $this->em->persist($energyPrice);
        $this->em->flush();

        $this->updateLastEnergyPrices($energyStation, $energyPrice, $energyType);

        $this->em->persist($energyStation);
        $this->em->flush();
    }

    private function hasToBeCreated(array $lastEnergyPrices, int $energyPriceValue, EnergyType $energyType, DateTimeImmutable $date): bool
    {
        if (!array_key_exists($energyType->getId(), $lastEnergyPrices)) {
            return true;
        }

        $lastEnergyPrice = $lastEnergyPrices[$energyType->getId()];
        $lastEnergyPriceDate = (new \DateTime())->setTimestamp($lastEnergyPrice['energyPriceDatetimestamp']);

        if ($lastEnergyPrice['energyPriceValue'] !== $energyPriceValue && $date->getTimestamp() > $lastEnergyPriceDate->getTimestamp()) {
            return true;
        }

        if ($date->format('W') <= $lastEnergyPriceDate->format('W')) {
            return false;
        }

        if ($date->format('W') > $lastEnergyPriceDate->format('W')) {
            return true;
        }

        return false;
    }

    private function updateLastEnergyPrices(EnergyStation $energyStation, EnergyPrice $energyPrice, EnergyType $energyType): void
    {
        $energyStation->setLastEnergyPrices($energyPrice);
        $this->updateEnergyStationIsClosed($energyStation);
    }

    private function updateEnergyStationIsClosed(EnergyStation $energyStation)
    {
        if (null === $energyStation->getClosedAt()) {
            return;
        }

        $energyStation->setClosedAt(null);
        $energyStation->setStatus($energyStation->getPreviousStatus());
    }
}
