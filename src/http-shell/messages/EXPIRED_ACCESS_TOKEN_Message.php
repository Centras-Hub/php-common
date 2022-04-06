<?php

namespace phpcommon\http\Messages;

class EXPIRED_ACCESS_TOKEN_Message extends Message{
    public function __construct($details = '')
    {
        parent::__construct('EXPIRED_ACCESS_TOKEN', 'Access token is invalid', 401, $details);
    }
}
