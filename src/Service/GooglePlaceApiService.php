<?php

namespace App\Service;

use App\Entity\EnergyStation;
use GuzzleHttp\Client;

class GooglePlaceApiService
{
    public function __construct(
        private string $googleApiKey,
        private string $placeTextsearchUrl,
        private string $placeDetailsUrl
    ) {
    }

    public function placeTextsearch(EnergyStation $energyStation): ?string
    {
        $url = rawurlencode($this->stripAccents(sprintf('%s %s %s', $energyStation->getAddress()->getNumber(), $energyStation->getAddress()->getStreet(), $energyStation->getAddress()->getCity())));
        $url = sprintf($this->placeTextsearchUrl, $url, $this->googleApiKey);

        $client = new Client();
        $response = $client->request('GET', $url);

        $data = \Safe\json_decode($response->getBody()->getContents(), true);
        $data['placeTextsearchUrl'] = $url;

        $energyStation->getGooglePlace()->setTextsearchApiResult($data);

        if (array_key_exists('status', $data) && in_array($data['status'], ['OK']) && array_key_exists('results', $data) && count($data['results']) > 0 && array_key_exists('place_id', $data['results'][0])) {
            return $data['results'][0]['place_id'];
        }

        return null;
    }

    public function placeDetails(EnergyStation $energyStation)
    {
        $url = sprintf($this->placeDetailsUrl, $energyStation->getGooglePlace()->getPlaceId(), $this->googleApiKey);

        $client = new Client();
        $response = $client->request('GET', $url);

        $data = \Safe\json_decode($response->getBody()->getContents(), true);
        $data['placeDetailsUrl'] = $url;

        $energyStation->getGooglePlace()->setPlaceDetailsApiResult($data);

        if (array_key_exists('status', $data) && 'OK' == $data['status'] && array_key_exists('result', $data)) {
            return $data['result'];
        }

        return null;
    }

    public function stripAccents($str)
    {
        return strtr(mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8'), mb_convert_encoding('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ', 'ISO-8859-1', 'UTF-8'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }
}
