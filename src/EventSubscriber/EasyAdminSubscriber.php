<?php

namespace App\EventSubscriber;

use App\Entity\BlogPost;
use App\Entity\EnergyStation;
use App\Entity\GooglePlace;
use App\Repository\EnergyStationRepository;
use App\Repository\GooglePlaceRepository;
use App\Service\GooglePlaceService;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Error;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private readonly EnergyStationRepository $energyStationRepository,
        private GooglePlaceService $googlePlaceService
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityUpdatedEvent::class => ['checkPlaceIdAnomaly'],
        ];
    }

    public function checkPlaceIdAnomaly(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof EnergyStation) {
            return;
        }

        if (!$entity->getGooglePlace()->getPlaceId()) {
            return;
        }

        $energyStationsAnomalies = $this->energyStationRepository->getEnergyStationGooglePlaceByPlaceId($entity);

        if (count($energyStationsAnomalies) > 0) {
            throw new Error('Annomaly for place id');
        }
    }
}
