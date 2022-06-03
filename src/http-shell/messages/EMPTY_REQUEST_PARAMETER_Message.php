<?php

namespace App\Helpers\Messages;

class EMPTY_REQUEST_PARAMETER_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('EMPTY_REQUEST_PARAMETER', 'Argument value is empty', 400, $details);
    }
}
