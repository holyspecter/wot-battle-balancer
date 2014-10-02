<?php

namespace BattleBalancer\WotApi;

use Curl\Curl;
use BattleBalancer\WotApi\Exception\ApiException;

abstract class BaseConnector
{
    const STATUS_SUCCESS = 'ok';
    const STATUS_ERROR   = 'error';

    /** @var string  */
    protected $baseUrl = 'https://api.worldoftanks.ru/wot/';

    /** @var array  */
    protected $parameters = [
        'application_id' => '00e76626cb4895ee57939af6ee349c17',
        'language'       => 'en',
    ];

    /** @var  Curl */
    protected $curl;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getParameter($key)
    {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : null;
    }

    /**
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     *
     * @throws Exception\ApiException
     */
    public function getResponse($method, array $parameters)
    {
        $parameters = array_merge($this->parameters, $parameters);
        $response = $this->curl->get($this->baseUrl . $method, $parameters);
        if (self::STATUS_SUCCESS === $response->status) {
            return $response->data;
        }

        throw new ApiException($response);
    }
}