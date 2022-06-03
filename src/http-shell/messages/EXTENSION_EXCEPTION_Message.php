<?php

namespace phpcommon\http\Messages;

class EXTENSION_EXCEPTION_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('EXTENSION_EXCEPTION', 'File extension error', 409, $details);
    }
}
