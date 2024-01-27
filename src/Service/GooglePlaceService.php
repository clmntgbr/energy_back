<?php

namespace App\Service;

use App\Entity\EnergyStation;
use App\Entity\EntityId\EnergyStationId;
use App\Message\CreateGooglePlaceAnomalyMessage;
use Symfony\Component\Messenger\MessageBusInterface;

final class GooglePlaceService
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    /**
     * @param array<int, EnergyStation> $energyStations
     */
    public function createAnomalies(array $energyStations): bool
    {
        foreach ($energyStations as $energyStationAnomaly) {
            $this->messageBus->dispatch(
                new CreateGooglePlaceAnomalyMessage(
                    new EnergyStationId($energyStationAnomaly->getEnergyStationId())
                )
            );
        }

        return true;
    }

    /**
     * @param array<mixed> $details
     */
    public function updateEnergyStationGooglePlace(EnergyStation $energyStation, array $details): void
    {
        $energyStation
            ->getGooglePlace()
            ->setGoogleId($details['id'] ?? null)
            ->setPlaceId($details['place_id'] ?? null)
            ->setBusinessStatus($details['business_status'] ?? null)
            ->setIcon($details['icon'] ?? null)
            ->setPhoneNumber($details['international_phone_number'] ?? null)
            ->setCompoundCode($details['plus_code']['compound_code'] ?? null)
            ->setGlobalCode($details['plus_code']['global_code'] ?? null)
            ->setGoogleRating($details['rating'] ?? null)
            ->setRating($details['rating'] ?? null)
            ->setReference($details['reference'] ?? null)
            ->setOpeningHours($details['opening_hours']['weekday_text'] ?? [])
            ->setUserRatingsTotal($details['user_ratings_total'] ?? null)
            ->setUrl($details['url'] ?? null)
            ->setWebsite($details['website'] ?? null)
            ->setWheelchairAccessibleEntrance($details['wheelchair_accessible_entrance'] ?? false);
    }

    /**
     * @param array<mixed> $details
     */
    public function updateEnergyStationAddress(EnergyStation $energyStation, array $details): void
    {
        $address = $energyStation->getAddress();

        foreach ($details['address_components'] as $component) {
            foreach ($component['types'] as $type) {
                switch ($type) {
                    case 'street_number':
                        $address->setNumber($component['long_name']);
                        break;
                    case 'route':
                        $address->setStreet($component['long_name']);
                        break;
                    case 'locality':
                        $address->setCity($component['long_name']);
                        break;
                    case 'administrative_area_level_1':
                        $address->setRegion($component['long_name']);
                        break;
                    case 'country':
                        $address->setCountry($component['long_name']);
                        break;
                    case 'postal_code':
                        $address->setPostalCode($component['long_name']);
                        break;
                }
            }
        }

        $address
            ->setVicinity(sprintf('%s %s, %s %s', $address->getNumber(), $address->getStreet(), $address->getPostalCode(), $address->getCity()))
            ->setLongitude($details['geometry']['location']['lng'] ?? null)
            ->setLatitude($details['geometry']['location']['lat'] ?? null);
    }
}
