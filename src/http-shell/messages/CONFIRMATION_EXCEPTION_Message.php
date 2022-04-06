<?php

namespace phpcommon\http\Messages;

class CONFIRMATION_EXCEPTION_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('CONFIRMATION_EXCEPTION', 'Invalid confirmation code', 409, $details);
    }
}
