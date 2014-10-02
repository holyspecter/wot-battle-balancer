<?php

namespace BattleBalancer\WotApi\Exception;

class ApiException extends \Exception
{
    protected $field;

    protected $value;

    public function __construct($responseObj)
    {
        $this->code = $responseObj->error->code;
        $this->message = $responseObj->error->message;
        $this->field = $responseObj->error->field;
        $this->value = $responseObj->error->value;
    }

    public function __toString()
    {
        return sprintf("Got error %s saying `%s`.", $this->code, $this->message);
    }
}