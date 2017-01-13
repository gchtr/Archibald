<?php

namespace Archibald\Request;

class RequestError
{
    private $type;
    private $request;
    private $message;

    public function __construct($type, $message = '', $request = null)
    {
        $this->type = $type;
        $this->request = $request;
        $this->message = $message;
    }

    public function isErrorOfType($type)
    {
        return $type === $this->type;
    }

    public function getMessage()
    {
        $message = preg_replace('/\`/', '', $this->message);

        if (!empty($this->request)) {
            $message .= "({$this->request})";
        }

        return $message;
    }

    public static function isError($thing)
    {
        return $thing instanceof self;
    }
}
