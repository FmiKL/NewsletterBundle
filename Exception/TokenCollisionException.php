<?php

namespace App\Bundle\NewsletterBundle\Exception;

use Exception;

class TokenCollisionException extends Exception
{
    public function __construct()
    {
        parent::__construct('Token collision occurred during subscription.');
    }
}
