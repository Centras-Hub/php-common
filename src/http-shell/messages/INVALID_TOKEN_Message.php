<?php

namespace phpcommon\http\Messages;

class INVALID_TOKEN_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('INVALID_TOKEN', 'The token has been deactivated or is invalid', 401, $details);
    }
}
