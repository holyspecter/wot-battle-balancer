#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use BattleBalancer\Console\Command\TestBalancerCommand;

$loader = require_once 'vendor/autoload.php';
$loader->add('BattleBalancer', __DIR__ . '/src/');
$loader->register();


$application = new Application();
$application->add(new TestBalancerCommand());
$application->run();