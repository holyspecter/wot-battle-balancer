<?php

namespace BattleBalancer\Console\Command;

use BattleBalancer\Balancer;
use BattleBalancer\WotApi\Exception\ApiException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestBalancerCommand extends Command
{
    /** {@inheritdoc} */
    protected function configure()
    {
        $this
            ->setName('balancer:test')
            ->setDescription('Runs balancing for two random clans.');
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);

        $output->writeln(sprintf("<comment>Balancing started... Please wait, it requires some time.</comment>"));

        try {
            $balancer = new Balancer();
            $teams = $balancer->getTeams();

            $output->writeln(sprintf("<comment>Balancing is finished. Here are results:</comment>"));
            $output->writeln(sprintf("<info>%s \t\t %s</info>", $teams[0][$i], $teams[1][$i]));
            for ($i = 0; $i < 15; $i++) {
                $output->writeln(sprintf("<info>%s \t\t %s</info>", $teams[0][$i], $teams[1][$i]));
            }

            $end = microtime(true);

            $output->writeln("\n<comment>Time spent: " . ($end - $start) . "sec.</comment>");
        } catch (ApiException $e) {
            $output->writeln(spreintf("<error>%s</error>", $e->getMessage()));
        }
    }
} 