<?php

namespace phpcommon\http;

use phpcommon\http\Messages\Message;

class ResponseMessagesDTO
{

    private string $code;
    private string $message;
    private $data;
    private int $status;
    private string $details;


    public function __construct(Message $message, mixed $data = null)
    {
        $this->data = $data;
        $this->message = $message->message;
        $this->code = $message->code;
        $this->status = $message->status;
        $this->details = $message->details;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setTrace(array $trace)
    {
        $this->data = (array)$this->data;
        $this->data += ['trace' => $trace];
    }

    public function serialize()
    {
        $message = [];
        $message['headers']['code'] = $this->code;
        $message['headers']['message'] = $this->message;
        if (strlen($this->details))
            $message['headers']['details'] = $this->details;
        $message['data'] = $this->data;
        return $message;
    }
}
