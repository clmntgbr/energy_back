<?php

namespace App\MessageHandler;

use App\Entity\Address;
use App\Entity\EnergyStation;
use App\Entity\EntityId\AddressId;
use App\Entity\EntityId\EnergyStationId;
use App\Entity\GooglePlace;
use App\Lists\EnergyStationReference;
use App\Lists\EnergyStationStatusReference;
use App\Message\CreateEnergyStationMessage;
use App\Message\CreateEvInformationMessage;
use App\Message\GeocodingAddressMessage;
use App\Repository\EnergyStationBrandRepository;
use App\Repository\EnergyStationRepository;
use App\Repository\UserRepository;
use App\Service\EnergyStationService;
use App\Service\FileSystemService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class CreateEnergyStationMessageHandler
{
    public function __construct(
        private EntityManagerInterface                $em,
        private readonly MessageBusInterface          $messageBus,
        private readonly EnergyStationRepository      $energyStationRepository,
        private readonly EnergyStationBrandRepository $energyStationBrandRepository,
        private readonly UserRepository               $userRepository,
        private readonly EnergyStationService         $energyStationService
    )
    {
    }

    public function __invoke(CreateEnergyStationMessage $message): void
    {
        $energyStation = $this->energyStationRepository->findOneBy(['energyStationId' => $message->getEnergyStationId()->getId()]);

        if ($energyStation instanceof EnergyStation) {
            return;
        }

        if ('' === $message->getLatitude() || '' === $message->getLongitude()) {
            return;
        }

        $user = $this->userRepository->findOneBy(['email' => 'clement@gmail.com']);
        $energyStationBrand = $this->energyStationBrandRepository->findOneBy(['reference' => 'total']);

        $address = new Address();
        $address
            ->setCreatedBy($user)
            ->setUpdatedBy($user)
            ->setCity($message->getCity())
            ->setPostalCode($message->getCp())
            ->setLongitude($message->getLongitude() ? strval(floatval($message->getLongitude()) / 100000) : null)
            ->setLatitude($message->getLatitude() ? strval(floatval($message->getLatitude()) / 100000) : null)
            ->setStreet($message->getStreet())
            ->setVicinity(sprintf('%s, %s %s', $message->getStreet(), $message->getCp(), $message->getCity()));

        // if ($message->getType() === EnergyStationReference::EV) {
        //     $address->setVicinity(sprintf('%s', $message->getStreet()));
        // }

        $element = $message->getElement();

        $energyStation = new EnergyStation();
        $energyStation
            ->setName($message->getName())
            ->setCreatedBy($user)
            ->setUpdatedBy($user)
            ->setHasEnergyStationBrandVerified(false)
            ->setEnergyStationBrand($energyStationBrand)
            ->setEnergyStationId($message->getEnergyStationId()->getId())
            ->setPop($message->getPop())
            ->setType($message->getType())
            ->setAddress($address)
            ->setGooglePlace(new GooglePlace())
            ->setHash($message->getHash())
            ->setStatus(EnergyStationStatusReference::CREATED);

        FileSystemService::createDirectoryIfDontExist('public/images/energy_stations');
        $filename = sprintf('%s.jpg', str_replace('.', '', \uniqid('', true)));
        copy('public/images/energy_stations/000.jpg', sprintf('public/images/energy_stations/%s', $filename));

        $energyStation->getImage()->setName($filename);
        $energyStation->getImage()->setOriginalName($filename);
        $energyStation->getImage()->setDimensions([660, 440]);
        $energyStation->getImage()->setMimeType('jpg');
        $energyStation->getImage()->setSize(86110);

        if (EnergyStationReference::GAS === $energyStation->getType()) {
            unset($element['prix']);
            $this->isEnergyStationClosed($element, $energyStation);
            $this->energyStationService->createEnergyStationServices($energyStation, $element);
        }

        if (EnergyStationReference::EV === $energyStation->getType()) {
            $this->messageBus->dispatch(
                new CreateEvInformationMessage(new EnergyStationId($energyStation->getEnergyStationId()), $element)
            );
        }

        $energyStation->setElement($element);

        if (null !== $energyStation->getClosedAt()) {
            $energyStation->setStatus(EnergyStationStatusReference::CLOSED);
        }

        $this->em->persist($energyStation);
        $this->em->flush();

        // $this->messageBus->dispatch(
        //     new GeocodingAddressMessage(new AddressId($energyStation->getAddress()->getId()), new EnergyStationId($energyStation->getEnergyStationId()))
        // );
    }

    /**
     * @param array<mixed> $element
     */
    public function isEnergyStationClosed(array $element, EnergyStation $energyStation): void
    {
        if (isset($element['fermeture']['attributes']['type']) && 'D' == $element['fermeture']['attributes']['type']) {
            $energyStation
                ->setClosedAt(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s', str_replace('T', ' ', substr($element['fermeture']['attributes']['debut'], 0, 19))));
        }
    }
}
