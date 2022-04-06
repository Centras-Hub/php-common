<?php

namespace phpcommon\http\Messages;

class BAD_REQUEST_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('BAD_REQUEST', 'Bad request', 400, $details);
    }
}
