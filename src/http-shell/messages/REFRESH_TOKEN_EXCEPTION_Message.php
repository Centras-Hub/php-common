<?php

namespace phpcommon\http\Messages;

class REFRESH_TOKEN_EXCEPTION_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('AUTH_TOKEN_EXCEPTION', 'Unauthorized', 401, $details);
    }
}
