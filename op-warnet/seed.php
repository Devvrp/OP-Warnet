<?php
require_once 'App/Init.php';
require_once 'App/Database/Factories/ProductFactory.php';

echo "Memulai seeding...\n";

$factory = new ProductFactory();
$factory->create(20);

echo "Seeding selesai.\n";