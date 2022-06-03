<?php

namespace phpcommon\http\Messages;

class ACCESS_TOKEN_EXPIRED_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('ACCESS_TOKEN_EXPIRED', 'Access token has expired', 401, $details);
    }
}
