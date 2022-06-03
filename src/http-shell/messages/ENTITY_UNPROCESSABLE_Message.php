<?php

namespace phpcommon\http\Messages;

class ENTITY_UNPROCESSABLE_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('ENTITY_UNPROCESSABLE', 'Unprocessable entity', 422, $details);
    }
}
