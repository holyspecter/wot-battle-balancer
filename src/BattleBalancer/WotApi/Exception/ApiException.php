<?php

namespace BattleBalancer\WotApi\Exception;

/**
 * Class ApiException
 *
 * @author Roman Kliuchko <hospect@gmail.com>
 * @package BattleBalancer\WotApi\Exception
 */
class ApiException extends \Exception
{
    protected $field;

    protected $value;

    public function __construct($responseObj)
    {
        $this->code = $responseObj->error->code;
        $this->message = sprintf("Got WoT API error %s saying `%s`.", $this->code, $responseObj->error->message);
        $this->field = $responseObj->error->field;
        $this->value = $responseObj->error->value;
    }
}