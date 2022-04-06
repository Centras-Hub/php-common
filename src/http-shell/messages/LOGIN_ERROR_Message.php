<?php

namespace phpcommon\http\Messages;

class LOGIN_ERROR_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('INVALID_LOGIN', 'Invalid login or password', 401, $details);
    }
}
