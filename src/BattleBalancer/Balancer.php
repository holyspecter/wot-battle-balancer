<?php

namespace BattleBalancer;

use BattleBalancer\WotApi\WotConnector;

/**
 * Class Balancer
 *
 * @author Roman Kliuchko <hospect@gmail.com>
 * @package BattleBalancer
 */
class Balancer
{
    const PLAYERS_COUNT = 15;

    /** @var  Clan */
    protected $clan1;

    /** @var  Clan */
    protected $clan2;

    /** @var \BattleBalancer\WotApi\WotConnector  */
    protected $wotConnector;

    /** @var array  */
    protected $allowedTankTypes = [4, 5, 6];

    /** @var float  */
    protected $precision;

    /**
     * @param float|null $precision
     */
    public function __construct($precision = null)
    {
        $this->wotConnector = new WotConnector();
        $this->clan1 = new Clan();
        $this->clan2 = new Clan();

        $clanMembers = $this->getRandomClansMembers();

        // Need to make arrays from iterable objects
        foreach ($clanMembers[0] as $clan1Member) {
            $this->clan1->members[] = $clan1Member;
        }
        foreach ($clanMembers[1] as $clan2Member) {
            $this->clan2Members[] = $clan2Member;
        }

        $this->precision = $precision ?: 0.1;

        $this->initTeam1();
        $this->initTeam2();
    }

    /**
     * @return array
     */
    public function getClans()
    {
        return [
            $this->clan1,
            $this->clan2,
        ];
    }

    /**
     * @return array
     */
    protected function getRandomClansMembers()
    {
        $topClans = $this->wotConnector->getTopClans();
        shuffle($topClans);

        $this->clan1->id = $topClans[0]->clan_id;
        $this->clan1->name = $topClans[0]->name;
        $this->clan2->id = $topClans[1]->clan_id;
        $this->clan2->name = $topClans[1]->name;

        return [
            $this->wotConnector->getMembers($this->clan1->id),
            $this->wotConnector->getMembers($this->clan2->id),
        ];
    }

    /**
     * Takes random players from first clan
     */
    protected function initTeam1()
    {
        shuffle($this->clan1->members);
        for ($i = 0; $i < self::PLAYERS_COUNT; $i++) {
            $unit = $this->composeUnit($this->clan1->members[$i]->account_id);
            $unit->accountName = $this->clan1->members[$i]->account_name;

            $this->clan1->team[] = $unit;
        }
    }

    /**
     * Chooses players from second clan depending on parameters of first clan's team
     */
    protected function initTeam2()
    {
        shuffle($this->clan2Members);
        $accountIds = [];
        foreach ($this->clan1->team as $opponent) {
            $i = 0;
            while (count($this->clan2->team) < self::PLAYERS_COUNT) {
                $accountId = $this->clan2Members[$i]->account_id;
                if (in_array($accountId, $accountIds)) {
                    $i = $i === (count($this->clan2Members) - 1) ? 0 : $i + 1;
                    continue;
                } elseif ($unit = $this->composeUnit($accountId, $opponent)) {
                    $unit->accountName = $this->clan2Members[$i]->account_name;
                    $this->clan2->team[] = $unit;

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
                if (in_array($tankInfo->level, $this->allowedTankTypes)) {
                    $unit = $this->doCreateUnit($tankInfo);
                    $unit->accountId = $accountId;
                    $unit->mastery = $playersTank->mark_of_mastery;

                    return $unit;
                }
            } else {
                if ($unitOpponent->mastery == $playersTank->mark_of_mastery) {
                    $tankInfo = $this->wotConnector->getTankInfo($playersTank->tank_id);

                    if (in_array($tankInfo->level, $this->allowedTankTypes)
                        && $this->compareStats($tankInfo, $unitOpponent)
                    ) {
                        $unit = $this->doCreateUnit($tankInfo);
                        $unit->accountId = $accountId;
                        $unit->mastery = $playersTank->mark_of_mastery;

                        return $unit;
                    }
                }
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
            return ($stat1 - $stat2) / $stat1 <= $this->precision;
        } else {
            return ($stat2 - $stat1) / $stat2 <= $this->precision;
        }
    }
}