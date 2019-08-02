<?php

namespace common\components;

use Yii;

/**
 * Class Location
 *
 * @package common\components
 */
class Location
{
    /** @const string IP-API URL address */
    const URL_ADDRESS = 'http://ip-api.com/json/';

    /** @const integer CURL timeout in seconds */
    const TIMEOUT = 4; // seconds

    /** @const string CURL method */
    const METHOD = 'GET';

    /** @var string Location IP address */
    private $ip;

    /** @var array Information about location */
    private $location;

    /**
     * Finds information about location
     *
     * @param null|string $ip Location IP address
     * @return Location
     */
    public static function find($ip = null)
    {
        $self = new self(['ip' => is_null($ip) ? Yii::$app->request->userIP : $ip]);
        $location = $self->getLocationInfo();
        $self->setLocation($location);
        return $self;
    }

    /**
     * Sets location information
     *
     * @param array $location Information about location
     */
    private function setLocation($location)
    {
        $this->location = ($location['status'] === 'success') ? $location : [];
    }

    /**
     * Returns information about location
     *
     * @see http://stackoverflow.com/a/33303776/5747867
     * @return array
     */
    private function getLocationInfo()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => self::URL_ADDRESS . $this->ip,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => self::METHOD,
            CURLOPT_HTTPHEADER => [
                'cache-control: no-cache',
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
		
		if (isEmpty($response)) {
           $response = '{"timezone":"Europe/Paris"}';
       }

        return json_decode($response, true);
    }

    /**
     * Returns location timezone
     *
     * @return null|string
     */
    public function timezone()
    {
        if (isset($this->location['timezone'])) {
            return $this->location['timezone'];
        }

        return null;
    }
    
    /**
     * Returns information about geographical location of user by IP address
     * 
     * @return GeoIP
     */
    public static function getGeoLocation()
    {
        $serverIp = Yii::$app->params['serverIp'];
        $ip = Yii::$app->request->userIp == ('127.0.0.1' || '::1') ?  $serverIp : null;
        return Yii::$app->geoip->lookupLocation($ip);
    }
}