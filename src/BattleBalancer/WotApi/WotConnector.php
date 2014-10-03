<?php

namespace BattleBalancer\WotApi;

/**
 * Class WotConnector
 *
 * @author Roman Kliuchko <hospect@gmail.com>
 * @package BattleBalancer\WotApi
 */
class WotConnector extends BaseConnector
{
    const TOP_CLANS_LIMIT = 10;

    /**
     * @param string $clanId
     *
     * @return mixed
     */
    public function getMembers($clanId)
    {
        return $this
            ->getResponse('clan/info/', ['clan_id' => $clanId])
            ->{$clanId}
            ->members;
    }

    /**
     * @param string $accountId
     *
     * @return mixed
     */
    public function getPlayersMastery($accountId)
    {
        $this->parameters['account_id'] = $accountId;

        return $this
            ->getResponse('account/tanks/', ['account_id' => $accountId])
            ->{$accountId};
    }

    /**
     * @return array
     */
    public function getTopClans()
    {
        $response = $this->getResponse('globalwar/top/', [
            'map_id'         => 'globalmap',
            'order_by'       => 'provinces_count',
        ]);

        return array_slice($response, 0, self::TOP_CLANS_LIMIT);
    }

    /**
     * @param string $tankId
     *
     * @return mixed
     */
    public function getTankInfo($tankId)
    {
        return $this
            ->getResponse('encyclopedia/tankinfo/', ['tank_id' => $tankId])
            ->{$tankId};
    }
}