<?php

$flights = [
  [
    'from' => 'VKO',
    'to' => 'DME',
    'depart' => '01.01.2020 12:44',
    'arrival' => '01.01.2020 13:44',
  ],
  [
    'from' => 'DME',
    'to' => 'JFK',
    'depart' => '02.01.2020 23:00',
    'arrival' => '03.01.2020 11:44',
  ],
  [
    'from' => 'DME',
    'to' => 'HKT',
    'depart' => '01.01.2020 13:40',
    'arrival' => '01.01.2020 22:22',
  ],
];

function findRoutes($currentFly, &$flights, $visited = [], $currentRoute = [], $id = -1)
{
  // Add the current airport to visited array
  $visited[] = $id;

  // Check all possible next flights
  foreach ($flights as $flightId => $flight) {
    // If the next flight is found from the current airport
    if ($flight['from'] === $currentFly['to'] && !in_array($flightId, $visited)) {
      // Create a new route, adding the current flight to the current route
      $newRoute = $currentRoute;
      $newRoute[] = $flight;

      //Check if the flight is valid based on the arrival and departure times
      $depart = DateTime::createFromFormat('d.m.Y H:i', $currentFly['arrival']);
      $arrive = DateTime::createFromFormat('d.m.Y H:i', $flight['depart']);
      if ($depart > $arrive) {
        continue; // Skip invalid flights.
      }

      // Recursively find routes starting from the found airport
      $subRoutes = findRoutes($flight, $flights, $visited, $newRoute, $flightId);

      // Add the sub-routes to the results
      foreach ($subRoutes as $subRoute) {
        $routes[] = $subRoute;
      }
    }
  }

  // If the current route contains more than 1 flight, add it to the results
  if (count($currentRoute) > 1) {
    $routes[] = $currentRoute;
  }

  return isset($routes) ? $routes : [];
}

$results = [];
foreach ($flights as $flight) {
  $routes = findRoutes($flight, $flights, [], [$flight]);
  foreach ($routes as $route) {
    $results[] = $route;
  }
}


$loongRouter = 0;
$loongRouterKey = 0;
// Find the longest route
foreach ($results as $result) {
  $depart = DateTime::createFromFormat('d.m.Y H:i', $result[0]['depart']);
  $arrival = DateTime::createFromFormat('d.m.Y H:i', $result[count($result) - 1]['arrival']);
  $diff = $arrival->getTimestamp() - $depart->getTimestamp();
  if ($loongRouter < $diff) {
    $loongRouter = $diff;
    $loongRouterKey = $result;
  }
}

$route = '';
foreach ($loongRouterKey as $flight) {
  $route .= $flight['from'] . ' -> ' . $flight['to'] . ' (' . $flight['depart'] . ' - ' . $flight['arrival'] . ')' . "\n";
}
echo "<br>";
echo $route . "\n";