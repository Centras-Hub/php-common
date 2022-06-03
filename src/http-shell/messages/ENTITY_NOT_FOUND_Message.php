<?php

namespace phpcommon\http\Messages;

class ENTITY_NOT_FOUND_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('ENTITY_NOT_FOUND', 'Such an entity does not exist', 404, $details);
    }
}
