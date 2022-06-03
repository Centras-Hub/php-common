<?php

namespace phpcommon\http\Messages;

class ACCESS_DENIED_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('ACCESS_DENIED', 'Access is denied', 403, $details);
    }
}
