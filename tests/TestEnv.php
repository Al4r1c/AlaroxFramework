<?php
error_reporting(E_ALL ^ E_NOTICE);

include_once(__DIR__ . '/../libraries/autoload.php');

$classLoader = new ClassLoader();
$classLoader->ajouterNamespace('Tests', __DIR__ . '/src/', '.php');
$classLoader->register();