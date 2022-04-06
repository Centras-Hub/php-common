<?php

namespace phpcommon\http\Messages;

class ENTITY_EXIST_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('ENTITY_EXIST', 'Such a record already exists', 409, $details);
    }
}
