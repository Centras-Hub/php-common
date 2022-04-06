<?php

namespace phpcommon\http\Messages;

class CONFLICTED_REQUEST_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('CONFLICTED_REQUEST', 'Conflicting access to a resource', 409, $details);
    }
}
