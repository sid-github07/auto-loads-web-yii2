<?php

use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\Map;
use dosamigos\google\maps\overlays\PolylineOptions;
use dosamigos\google\maps\services\DirectionsRenderer;
use dosamigos\google\maps\services\DirectionsRequest;
use dosamigos\google\maps\services\DirectionsWayPoint;
use dosamigos\google\maps\services\TravelMode;
use common\models\Load;
use common\components\DirectionsService;

/** @var Load $load */
/** @var null|string $showLoadInfo */

$coords = [];
foreach ($load->loadCities as $loadCity) {
    array_push($coords, new LatLng(['lat' => $loadCity->city->latitude, 'lng' => $loadCity->city->longitude]));
}
$start = array_shift($coords);
$end = array_pop($coords);

$waypoints = [];
foreach ($coords as $coord) {
    array_push($waypoints, new DirectionsWayPoint(['location' => $coord]));
}

$directionsRequest = new DirectionsRequest([
    'origin' => $start,
    'destination' => $end,
    'waypoints' => $waypoints,
    'travelMode' => TravelMode::DRIVING,
]);

$polylineOptions = new PolylineOptions([
    'strokeColor' => '#FF9C11',
    'draggable' => false,
]);

$europeMapCenter = Yii::$app->params['gmapsEuropeCenter'] ;
$map = new Map([
    'center' => new LatLng(['lat' => $europeMapCenter[0], 'lng' => $europeMapCenter[1]]),
    'zoom' => 4,
    'width' => '100%',
    'height' => 230,
    'containerOptions' => ['id' => 'gmap' . $load->id . '-map-canvas'],
]);

$directionsRenderer = new DirectionsRenderer([
    'map' => 'gmap' . $load->id,
    'polylineOptions' => $polylineOptions,
]);

$directionsService = new DirectionsService([
    'directionsRenderer' => $directionsRenderer,
    'directionsRequest' => $directionsRequest,
]);

$directionsService->setName('gdirectionsService' . $load->id);

$map->setName('gmap' . $load->id);

$map->appendScript($directionsService->getJs());

echo $map->display();