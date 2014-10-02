<?php

namespace BattleBalancer;

use BattleBalancer\WotApi\WotConnector;

class Balancer
{
    const PLAYERS_COUNT = 15;

    /** @var array  */
    protected $clan1Members;

    /** @var array  */
    protected $clan2Members;

    protected $team1;

    protected $team2;

    protected $wotConnector;

    /** @var array  */
    protected $allowedTankTypes = [4, 5, 6];

    /** @var float  */
    protected $precision;

    /**
     * @param object     $clan1Members
     * @param object     $clan2Members
     * @param float|null $precision
     */
    public function __construct($clan1Members, $clan2Members, $precision = null)
    {
        // Need to make arrays from iterable objects
        foreach ($clan1Members as $clan1Member) {
            $this->clan1Members[] = $clan1Member;
        }
        foreach ($clan2Members as $clan2Member) {
            $this->clan2Members[] = $clan2Member;
        }
        $this->wotConnector = new WotConnector();
        $this->precision = $precision ?: 0.1;

        $this->initTeam1();
        $this->initTeam2();
    }

    /**
     * @return array
     */
    public function getTeams()
    {
        return [
            $this->team1,
            $this->team2,
        ];
    }

    protected function initTeam1()
    {
        echo "Initializing first team...\n";
        shuffle($this->clan1Members);
        for ($i = 0; $i < self::PLAYERS_COUNT; $i++) {
            $unit = $this->composeUnit($this->clan1Members[$i]->account_id);
            $unit->accountName = $this->clan1Members[$i]->account_name;

            $this->team1[] = $unit;
        }
    }

    protected function initTeam2()
    {
        echo "Initializing second team...\n";
        shuffle($this->clan2Members);
        $accountIds = [];
        foreach ($this->team1 as $opponent) {
            $i = 0;
            while (count($this->team2) < self::PLAYERS_COUNT) {
                $accountId = $this->clan2Members[$i]->account_id;
                if (in_array($accountId, $accountIds)) {
                    $i = $i === (count($this->clan2Members) - 1) ? 0 : $i + 1;
                    continue;
                } elseif ($unit = $this->composeUnit($accountId, $opponent)) {
                    $unit->accountName = $this->clan2Members[$i]->account_name;
                    $this->team2[] = $unit;

                    $accountIds[] = $accountId;
                    break;
                }

                $i = $i === (count($this->clan2Members) - 1) ? 0 : $i + 1;
            }
        }
    }

    /**
     * @param string $accountId
     * @param Unit|null   $unitOpponent
     *
     * @return Unit
     */
    protected function composeUnit($accountId, $unitOpponent = null)
    {
        $playersTanks = $this->wotConnector->getPlayersMastery($accountId);
        shuffle($playersTanks);
        foreach ($playersTanks as $playersTank) {
            if (!$unitOpponent) {
                $tankInfo = $this->wotConnector->getTankInfo($playersTank->tank_id);
//                echo "max_damage: " . $tankInfo->gun_damage_max . "\n";
//                echo "min_damage: " . $tankInfo->gun_damage_min . "\n";
//                echo "health: " . $tankInfo->max_health . "\n";
                if (in_array($tankInfo->level, $this->allowedTankTypes)) {
                    $unit = $this->doCreateUnit($tankInfo);
                    $unit->accountId = $accountId;
                    $unit->mastery = $playersTank->mark_of_mastery;

                    return $unit;
                }
            } else {
                if ($unitOpponent->mastery == $playersTank->mark_of_mastery) {
//                    echo $unitOpponent->mastery .' == ' . $playersTank->mark_of_mastery . "\n";
                    $tankInfo = $this->wotConnector->getTankInfo($playersTank->tank_id);

                    if (in_array($tankInfo->level, $this->allowedTankTypes)
                        && $this->compareStats($tankInfo, $unitOpponent)
                    ) {
                        $unit = $this->doCreateUnit($tankInfo);
                        $unit->accountId = $accountId;
                        $unit->mastery = $playersTank->mark_of_mastery;
//                        echo "found\n";

                        return $unit;
                    }
                }
//                echo "wasted\n";
            }
        }

        return null;
    }

    /**
     * @param object $tankInfo
     *
     * @return Unit
     */
    private function doCreateUnit($tankInfo)
    {
        $unit = new Unit();
        $unit->tankId = $tankInfo->tank_id;
        $unit->tankName = $tankInfo->name;
        $unit->gunDamageMax = $tankInfo->gun_damage_max;
        $unit->gunDamageMin = $tankInfo->gun_damage_min;
        $unit->maxHealth = $tankInfo->max_health;

        return $unit;
    }

    /**
     * @param object $tankInfo
     * @param Unit   $opponent
     *
     * @return bool
     */
    protected function compareStats($tankInfo, Unit $opponent)
    {
        $stat1 = $opponent->gunDamageMin + $opponent->gunDamageMax + $opponent->maxHealth;
        $stat2 = $tankInfo->gun_damage_min + $tankInfo->gun_damage_max + $tankInfo->max_health;

        if ($stat1 > $stat2) {
//            echo "error: " . ($stat1 - $stat2) / $stat1 . "\n";
            return ($stat1 - $stat2) / $stat1 <= $this->precision;
        } else {
//            echo "error: " . ($stat2 - $stat1) / $stat2 . "\n";
            return ($stat2 - $stat1) / $stat2 <= $this->precision;
        }
    }
}