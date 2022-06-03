<?php

namespace phpcommon\http\Messages;

class UNAUTHORIZED_ACCESS_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('UNAUTHORIZED_ACCESS', 'User is unauthorized', 401, $details);
    }
}
