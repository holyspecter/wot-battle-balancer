<?php

$loader = require_once 'vendor/autoload.php';
$loader->add('BattleBalancer', __DIR__ . '/src/');
$loader->register();

$start = microtime(true);

$watConnector = new \BattleBalancer\WotApi\WotConnector();

try {
    $topClans = $watConnector->getTopClans();

// todo add precision CLI parameter

//var_dump($topClans[0]);
    $clan1Members = $watConnector->getMembers($topClans[0]->clan_id);
    $clan2Members = $watConnector->getMembers($topClans[1]->clan_id);

    $balancer = new \BattleBalancer\Balancer($clan1Members, $clan2Members);
    $teams = $balancer->getTeams();

    for ($i = 0; $i < 15; $i++) {
//    var_dump($teams[0][$i]);
//    echo "\n";
//    echo sprintf("%s \n", $teams[0][$i]);
        echo sprintf("%s \t\t %s\n", $teams[0][$i], $teams[1][$i]);
    }

    $end = microtime(true);

    echo "\nTime spent: " . ($end - $start) . "\n";
} catch (\BattleBalancer\WotApi\Exception\ApiException $e) {
    echo $e->getMessage() . "\n";
}