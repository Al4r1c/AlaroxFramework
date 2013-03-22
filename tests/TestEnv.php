<?php
error_reporting(E_ALL);

include_once(__DIR__ . '/../libraries/autoload.php');

$classLoader = new ClassLoader();
$classLoader->ajouterNamespace('Framework', realpath(__DIR__ . '/../src/framework/'), '.php');
$classLoader->ajouterNamespace('Tests', __DIR__ . '/src/', '.php');
$classLoader->register();