<?php

namespace App\Service;

use App\Lists\EnergyStationReference;
use App\Repository\EnergyStationRepository;
use GuzzleHttp\Client;

class EvUpdateCommandService
{
    public function __construct(
        private readonly string                  $evUrl,
        private readonly string                  $randomLocationUrl,
        private readonly EnergyStationRepository $energyStationRepository,
        private readonly EnergyStationService    $energyStationService
    )
    {
    }

    public function invoke($loop): void
    {
        $client = new Client();

        for($i=0;$i< $loop;$i++) {

            $energyStations = $this->energyStationRepository->findEnergyStationsById(EnergyStationReference::EV);

            $response = $client->request('GET', $this->randomLocationUrl);
            $data = \Safe\json_decode($response->getBody()->getContents(), true);

            // $longitude = $data['nearest']['longt'] ?? null;
            // $latitude = $data['nearest']['latt'] ?? null;
            $latitude = 48.75668914602619;
            $longitude = 2.3722821757290546;

            dump(sprintf('latitude: %s; longitude: %s, region: %s, city: %s', $latitude, $longitude, $data['nearest']['region'], $data['nearest']['name'] ?? ''));

            $evUrlresponse = $client->request('GET', sprintf($this->evUrl, $latitude, $longitude));
            $evUrlData = \Safe\json_decode($evUrlresponse->getBody()->getContents(), true);
            
            $count = 0;

            foreach($evUrlData as $datum) {

                if ($datum['UUID'] === null) {
                    continue;
                }

                $energyStationId = $this->energyStationService->getEnergyStationId($datum['UUID']);
                $hash = $this->energyStationService->getHash($datum);
                $datum = $this->hydrate($datum);

                if (!array_key_exists($energyStationId->getId(), $energyStations)) {
                    $this->energyStationService->createEnergyStationMessage($energyStationId, $hash, $this->hydrate($datum), EnergyStationReference::EV);
                }

                if (array_key_exists($energyStationId->getId(), $energyStations) && $energyStations[$energyStationId->getId()]['hash'] !== $hash) {
                    $this->energyStationService->updateEnergyStationMessage($energyStationId, $hash, $this->hydrate($datum));
                }
                $count++;

                if ($count == 25) {
                    return;
                }
            }
            
            dump('sleeping for 10 sec ...');
            sleep(10);
        }
    }

    private function hydrate(array $datum)
    {
        $datum['name'] = $datum['name'] ?? $datum['AddressInfo']['Title'];
        $datum['@attributes'] = [
            'pop' => 'R',
            'cp' => $datum['AddressInfo']['Postcode'] ?? '',
            'longitude' => $datum['AddressInfo']['Longitude'] ?? '',
            'latitude' => $datum['AddressInfo']['Latitude'] ?? '',
        ];
        $datum['adresse'] = $datum['AddressInfo']['AddressLine1'] ?? '';
        $datum['ville'] = $datum['AddressInfo']['Town'] ?? '';

        return $datum;
    }
}
