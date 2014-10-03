<?php

namespace BattleBalancer\Console\Command;

use BattleBalancer\Clan;
use BattleBalancer\Balancer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestBalancerCommand
 *
 * @author Roman Kliuchko <hospect@gmail.com>
 * @package BattleBalancer\Console\Command
 */
class TestBalancerCommand extends Command
{
    /** {@inheritdoc} */
    protected function configure()
    {
        $this
            ->setName('balancer:test')
            ->setDescription('Runs balancing for two random clans.')
            ->addArgument('precision', InputArgument::OPTIONAL, 'Pass float value from 0.01 to 1. Less value will increase error but may require more time.');
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);

        try {
            $this->checkArguments($input);

            $output->writeln(sprintf("<comment>Balancing started... Please wait, it requires some time.</comment>"));

            $clans = (new Balancer($input->getArgument('precision')))
                ->getClans();

            $output->writeln(sprintf("<comment>Balancing is finished. Here are results:</comment>"));
            $this->printTable($clans, $output);

            $end = microtime(true);

            $output->writeln("\n<comment>Time spent: " . ($end - $start) . "sec.</comment>");
        } catch (\Exception $e) {
            $output->writeln(sprintf("<error>%s</error>", $e->getMessage()));
        }
    }

    /**
     * @param InputInterface $input
     *
     * @throws \InvalidArgumentException
     */
    protected function checkArguments(InputInterface $input)
    {
        $precision = $input->getArgument('precision');
        if (!is_null($precision)) {
            if (floatval($precision) < 0.01 || floatval($precision) >= 1) {
                throw new \InvalidArgumentException(
                    sprintf("`%s` is not a valid parameter. Pass float value in range [0.01;1).", $precision)
                );
            }
        }
    }

    /**
     * @param Clan[]          $clans
     * @param OutputInterface $output
     */
    protected function printTable(array $clans, OutputInterface $output)
    {
        $table = new \Console_Table();
        $table->setHeaders([$clans[0], $clans[1]]);
        for ($i = 0; $i < 15; $i++) {
            $table->addRow([$clans[0]->team[$i], $clans[1]->team[$i]]);
        }

        $output->write('<info>');
        $output->write($table->getTable());
        $output->writeln('</info>');
    }
} 