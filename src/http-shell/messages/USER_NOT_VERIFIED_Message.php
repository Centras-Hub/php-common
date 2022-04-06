<?php

namespace phpcommon\http\Messages;

class USER_NOT_VERIFIED_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('USER_NOT_VERIFIED', 'User is not verified', 401, $details);
    }
}
