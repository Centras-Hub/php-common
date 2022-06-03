<?php

namespace phpcommon\http\Messages;

class AUTH_TOKEN_EXCEPTION_Message extends Message{
    public function __construct($details = '')
    {
        parent::__construct('AUTH_TOKEN_EXCEPTION', 'Bad request', 401, $details);
    }
}
