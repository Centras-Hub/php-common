<?php

namespace phpcommon\http\Messages;

class INTERNAL_SERVER_ERROR_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('INTERNAL_SERVER_ERROR', 'Internal server error', 500, $details);
    }
}
