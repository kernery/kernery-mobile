<?php

namespace Kernery\Main\Exceptions;

use Exception;

class MissingCURLExtensionException extends Exception
{
    public function __construct()
    {
        parent::__construct('PHP cURL extension is not installed. Please install cURL');
    }
}
