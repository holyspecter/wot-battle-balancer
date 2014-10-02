<?php

namespace BattleBalancer;

class Unit implements \JsonSerializable
{
    public $accountId;

    public $accountName;

    public $mastery;

    public $tankId;

    public $tankName;

    public $gunDamageMin;

    public $gunDamageMax;

    public $maxHealth;

    public function __toString()
    {
        return $this->accountName . ' (' . $this->tankName . ' ' . $this->mastery . ' ' . $this->gunDamageMin . ' ' . $this->gunDamageMax . ' ' . $this->maxHealth . ')';
    }

    public function jsonSerialize()
    {
        return [
            'account_id'   => $this->accountId,
            'account_name' => $this->accountName,
            'tank_id'      => $this->tankId,
            'tank_name'    => $this->tankName,
        ];
    }
}