<?php

namespace phpcommon\http\Messages;

class ERROR_EXCEPTION_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('ERROR', 'Unknown error', 500, $details);
    }
}
