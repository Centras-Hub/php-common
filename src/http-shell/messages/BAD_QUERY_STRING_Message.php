<?php

namespace phpcommon\http\Messages;

class BAD_QUERY_STRING_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('BAD_QUERY_STRING', 'Invalid query-string parameter value', 400, $details);
    }
}
