<?php

namespace phpcommon\http\Messages;

abstract class Message
{
    public string $code;
    public string $message;
    public int $status;
    public string $details;

    public function __construct(string $code, string $message, int $status, string $details = '')
    {
        $this->code = $code;
        $this->message = $message;
        $this->status = $status;
        $this->details = $details;
    }
}

