<?php

namespace Kernery\Main\Exceptions;

use Exception;

class MissingZipExtensionException extends Exception
{
    public function __construct()
    {
        parent::__construct('PHP Zip extension is not installed. Please install it.');
    }
}
