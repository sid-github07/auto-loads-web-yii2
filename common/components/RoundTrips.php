<?php

namespace common\components;

use common\components\ElasticSearch\Cities;
use common\components\ElasticSearch\Loads;

/**
 * Class RoundTrips
 *
 * @package common\components
 */
class RoundTrips
{
    /** @const integer Minimum number of round trips */
    const MIN_ROUND_TRIPS = 2;

    /** @const integer Maximum number of round trips */
    const MAX_ROUND_TRIPS = 5;

    /** @const integer Maximum number of kilometers that transporter can drive empty */
    const EMPTY_TRANSPORTER_DISTANCE = 300;

    /** @const integer Maximum number of kilometers that final unload city can be from home city */
    const FINAL_UNLOAD_DISTANCE = 500;

    /**
     * Returns round trips
     *
     * @param integer $homeCityId Round trips home city ID
     * @return array
     */
    public static function getRoundTrips($homeCityId = 0)
    {
        $trips = [];
        self::constructTrips($homeCityId, $trips, $homeCityId);
        $tripsInStrings = self::sortOutTrips(true, $trips);
        $tripsInArray = self::convertPathToArray($tripsInStrings);

        return $tripsInArray;
    }

    /**
     * Recursively constructs round trips
     *
     * @param integer $currentCityId Current round trip city ID
     * @param array $trips Round trips container
     * @param integer $homeCityId Round trips home city ID
     * @param integer $i Iteration number
     * @param array $currentTrip Current trip container
     */
    private static function constructTrips($currentCityId = 0, &$trips = [], $homeCityId = 0, $i = 0, $currentTrip = [])
    {
        if (self::canRoundTripEnd($i, $currentCityId, $homeCityId, $trips)) {
            return;
        }

        $loads = Loads::getRoundTripLoads($currentCityId, self::EMPTY_TRANSPORTER_DISTANCE);
        if (!self::hasMoreLoads($loads, $trips)) {
            return;
        }

        $i++;
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($partialLoads, $fullLoads) = Loads::separateLoadsByType($loads);
        foreach ($fullLoads as $loadId => $load) {
            $unloadCityInfo = current($load['unload']);
            $unloadCityId = $unloadCityInfo['city_id'];
            if (in_array($loadId, $currentTrip)) {
                continue;
            }
            array_push($currentTrip, $loadId);
            $trips[$loadId] = [];
            self::constructTrips($unloadCityId, $trips[$loadId], $homeCityId, $i, $currentTrip);
        }

        return;
    }

    /**
     * Checks whether round trip can end
     *
     * @param integer $iteration Iteration number
     * @param integer $currentCityId Current round trip city ID
     * @param integer $homeCityId Round trips home city ID
     * @param array $trip Round trips container
     * @return boolean
     */
    private static function canRoundTripEnd($iteration, $currentCityId, $homeCityId, &$trip)
    {
        if ($iteration < self::MIN_ROUND_TRIPS) {
            return false;
        }

        if ($iteration > self::MAX_ROUND_TRIPS) {
            array_push($trip, false);
            return true;
        }

        if (self::isNearHomeCity($currentCityId, $homeCityId)) {
            array_push($trip, true);
            return true;
        }

        return false;
    }

    /**
     * Checks whether current round trip city is near home city
     *
     * @param integer $currentCityId Current round trip city ID
     * @param integer $homeCityId User selected round trips home city ID
     * @return boolean
     */
    private static function isNearHomeCity($currentCityId, $homeCityId)
    {
        $distance = Cities::getDistance($currentCityId, $homeCityId);
        return $distance <= self::FINAL_UNLOAD_DISTANCE;
    }

    /**
     * Checks whether current round trip city has more loads to transport
     *
     * @param array $loads Current city loads
     * @param array $trips Round trips container
     * @return boolean
     */
    private static function hasMoreLoads($loads, &$trips)
    {
        if (empty($loads)) {
            array_push($trips, false);
            return false;
        }

        return true;
    }

    /**
     * Sorts out round trips
     *
     * Some of round trips may end not successfully,
     * i.e. home city is too far from current city,
     * or there is no more trips from current city.
     * This method sorts out these trips and returns list of trips,
     * that only ended successfully.
     *
     * @see http://forums.devshed.com/php-development/333626-function-return-path-value-multi-dimensional-array-post1426750.html#post1426750
     * @param boolean $successfulEnd Attribute, that round trip ended successfully.
     * True - ended successfully, false - no
     * @param array $trips Round trips container
     * @return array
     */
    private static function sortOutTrips($successfulEnd, $trips)
    {
        $strings = [];
        foreach ($trips as $loadId => $currentTrip) {
            if (is_array($currentTrip)) {
                $newTrip = self::sortOutTrips($successfulEnd, $currentTrip);
                if (!empty($newTrip)) {
                    foreach ($newTrip as $path) {
                        $strings[] = "[$loadId]$path";
                    }
                }
            } else {
                if ($successfulEnd === $currentTrip) {
                    $strings[] = "[$loadId]";
                }
            }
        }

        return $strings;
    }

    /**
     * Converts trips, whose path is in string, to array
     *
     * @param array $trips Round trips in strings
     * @return array
     */
    private static function convertPathToArray($trips)
    {
        $array = [];
        foreach ($trips as $path) {
            $trimmedPath = substr($path, 1, -1); // Removes '[' and ']' from string beginning and end
            $decomposed = explode('][', $trimmedPath);
            array_pop($decomposed); // Removes 0 from path
            array_push($array, $decomposed);
        }

        return $array;
    }

    /**
     * Extracts unique loads IDs from round trips
     *
     * @param array $roundTrips List of round trips
     * @return array
     */
    public static function getUniqueLoadsIds($roundTrips = [])
    {
        $ids = [];
        foreach ($roundTrips as $trip) {
            foreach ($trip as $loadId) {
                if (!in_array($loadId, $ids)) {
                    array_push($ids, $loadId);
                }
            }
        }

        return $ids;
    }
}