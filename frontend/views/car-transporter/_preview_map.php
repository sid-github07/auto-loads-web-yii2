<?php

use common\components\DirectionsService;
use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\Map;
use dosamigos\google\maps\overlays\PolylineOptions;
use dosamigos\google\maps\services\DirectionsRenderer;
use dosamigos\google\maps\services\DirectionsRequest;
use dosamigos\google\maps\services\DirectionsWayPoint;
use dosamigos\google\maps\services\TravelMode;

$coords = [];
foreach ($carTransporter->carTransporterCities as $carTransporterCity) {
    array_push($coords, new LatLng([
        'lat' => $carTransporterCity->city->latitude,
        'lng' => $carTransporterCity->city->longitude,
    ]));
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

$map = new Map([
    'center' => new LatLng([
        'lat' => 48.771635,
        'lng' => 13.839543,
    ]),
    'zoom' => 4,
    'width' => '100%',
    'height' => 230,
    'containerOptions' => ['id' => 'gmap' . $carTransporter->id . '-map-canvas'],
]);

$directionsRenderer = new DirectionsRenderer([
    'map' => 'gmap' . $carTransporter->id,
    'polylineOptions' => $polylineOptions,
]);

$directionsService = new DirectionsService([
    'directionsRenderer' => $directionsRenderer,
    'directionsRequest' => $directionsRequest,
]);

$directionsService->setName('gdirectionsService' . $carTransporter->id);

$map->setName('gmap' . $carTransporter->id);

$map->appendScript($directionsService->getJs());

echo $map->display();
