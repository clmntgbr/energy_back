<?php

namespace App\MessageHandler;

use App\Entity\Address;
use App\Entity\EnergyStation;
use App\Entity\GooglePlace;
use App\Lists\EnergyStationStatusReference;
use App\Message\CreateEnergyStationMessage;
use App\Repository\EnergyStationBrandRepository;
use App\Repository\EnergyStationRepository;
use App\Repository\UserRepository;
use App\Service\EnergyStationService;
use App\Service\FileSystemService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
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

    public function __invoke(CreateEnergyStationMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $energyStation = $this->energyStationRepository->findOneBy(['energyStationId' => $message->getEnergyStationId()->getId()]);

        if ($energyStation instanceof EnergyStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Energy Station already exist (energyStationId : %s)', $message->getEnergyStationId()->getId()));
        }

        if ('' === $message->getLatitude() || '' === $message->getLongitude()) {
            throw new UnrecoverableMessageHandlingException(sprintf('Energy Station longitude/latitude is empty (energyStationId : %s)', $message->getEnergyStationId()->getId()));
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
            ->setCountry($message->getCountry())
            ->setStreet($message->getStreet())
            ->setVicinity(sprintf('%s, %s %s, %s', $message->getStreet(), $message->getCp(), $message->getCity(), $message->getCountry()));

        $element = $message->getElement();
        unset($element['prix']);

        $energyStation = new EnergyStation();
        $energyStation
            ->setCreatedBy($user)
            ->setUpdatedBy($user)
            ->setHasEnergyStationBrandVerified(false)
            ->setEnergyStationBrand($energyStationBrand)
            ->setEnergyStationId($message->getEnergyStationId()->getId())
            ->setPop($message->getPop())
            ->setElement($element)
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

        $this->isEnergyStationClosed($element, $energyStation);

        if (null !== $energyStation->getClosedAt()) {
            $energyStation->setStatus(EnergyStationStatusReference::CLOSED);
        }

        $this->energyStationService->createEnergyStationServices($energyStation, $element);

        $this->em->persist($energyStation);
        $this->em->flush();

//        $this->messageBus->dispatch(
//            new GeocodingAddressMessage(new AddressId($energyStation->getAddress()->getId()), new EnergyStationId($energyStation->getEnergyStationId()))
//        );
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
