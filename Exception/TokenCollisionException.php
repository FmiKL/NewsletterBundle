<?php

namespace App\Bundle\NewsletterBundle\Exception;

use Exception;

class TokenCollisionException extends Exception
{
    public function __construct(?Exception $previous = null)
    {
        parent::__construct('Token collision occurred during subscription.', 0, $previous);
    }
}
