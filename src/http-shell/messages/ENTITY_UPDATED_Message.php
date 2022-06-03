<?php

namespace phpcommon\http\Messages;

class ENTITY_UPDATED_Message extends Message
{
    public function __construct($details = '')
    {
        parent::__construct('ENTITY_UPDATED', 'Entity has been successfully updated', 200, $details);
    }
}
