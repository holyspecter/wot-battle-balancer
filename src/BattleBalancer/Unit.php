<?php

namespace BattleBalancer;

/**
 * Class Unit
 *
 * @author Roman Kliuchko <hospect@gmail.com>
 * @package BattleBalancer
 */
class Unit
{
    /** @var  string */
    public $accountId;

    /** @var  string */
    public $accountName;

    /** @var  string */
    public $mastery;

    /** @var  string */
    public $tankId;

    /** @var  string */
    public $tankName;

    /** @var  int */
    public $gunDamageMin;

    /** @var  int */
    public $gunDamageMax;

    /** @var  int */
    public $maxHealth;

    /** @return string */
    public function __toString()
    {
        return $this->accountName . ' (' . $this->tankName . ')';
    }
}