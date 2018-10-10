<?php

require_once('AssociativeArray.php');

$PACKAGES = [
    'A' => [
        'name' => 'A',
        'dependencies' => ['B', 'C'],
    ],
    'B' => [
        'name' => 'B',
        'dependencies' => [],
    ],
    'C' => [
        'name' => 'C',
        'dependencies' => ['B', 'D'],
    ],
    'D' => [
        'name' => 'D',
        'dependencies' => [],
    ]
];

try {
    $entity = new AssociativeArray();
    var_dump($entity->getAllPackageDependencies($PACKAGES, 'A'));
} catch (DependencyException $error) {
    echo $error->getMessage();
}