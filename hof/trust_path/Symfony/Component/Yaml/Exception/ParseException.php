<?php

class Symfony_Component_Yaml_Exception_ParseException extends RuntimeException implements Symfony_Component_Yaml_Exception_ExceptionInterface
{
    private $parsedFile;
    private $parsedLine;
    private $snippet;
    private $rawMessage;

    public function __construct($message, $code = 0, $previous = null, $parsedFile = null, $parsedLine = null, $snippet = null)
    {
        $this->rawMessage = $message;
        $this->parsedFile = $parsedFile;
        $this->parsedLine = $parsedLine;
        $this->snippet = $snippet;

        $this->updateMessage();
        parent::__construct($this->message, $code, $previous);
    }

    public function getParsedFile()
    {
        return $this->parsedFile;
    }

    public function setParsedFile($parsedFile)
    {
        $this->parsedFile = $parsedFile;
        $this->updateMessage();
    }

    private function updateMessage()
    {
        $this->message = $this->rawMessage;
        if ($this->parsedFile !== null) {
            $this->message .= sprintf(' in "%s"', $this->parsedFile);
        }
    }
}

class Symfony_Component_Yaml_Exception_DumpException extends RuntimeException implements Symfony_Component_Yaml_Exception_ExceptionInterface
{
}
