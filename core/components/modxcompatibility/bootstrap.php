<?php
/**
 * @var \MODX\Revolution\modX $modx
 * @var array $namespace
 */
require_once $namespace['path'] . 'vendor/autoload.php';


$modx->services->add('modxcompatibiliy', function($c) use ($modx) {
    return new \ModxCompatibility\v3\Service($modx);
});