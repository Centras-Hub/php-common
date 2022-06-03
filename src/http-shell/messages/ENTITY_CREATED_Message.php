<?php

namespace phpcommon\http\Messages;

class ENTITY_CREATED_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('ENTITY_CREATED', 'Entity has been successfully created', 201, $details);
    }
}
