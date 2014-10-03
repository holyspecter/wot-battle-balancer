<?php

namespace BattleBalancer;

/**
 * Class Clan
 *
 * @author Roman Kliuchko <hospect@gmail.com>
 * @package BattleBalancer
 */
class Clan
{
    /** @var  string */
    public $id;

    /** @var  string */
    public $name;

    /** @var  array */
    public $members;

    /** @var  Unit[] */
    public $team;

    /** @return string */
    public function __toString()
    {
        return $this->name;
    }
}