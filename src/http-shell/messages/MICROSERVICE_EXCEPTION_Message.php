<?php

namespace phpcommon\http\Messages;

class MICROSERVICE_EXCEPTION_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('MICROSERVICE_EXCEPTIONMessage', 'An exception occurred on a microservice', 500, $details);
    }
}
