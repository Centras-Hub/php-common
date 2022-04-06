<?php

namespace phpcommon\http\Messages;

class SUCCESSFUL_REQUEST_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('SUCCESSFUL_REQUEST', 'Request was completed successfully', 200, $details);
    }
}

