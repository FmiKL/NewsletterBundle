<?php

namespace App\Bundle\NewsletterBundle\Service;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

/**
 * Service class for validating email addresses.
 */
class EmailValidatorService
{
    /**
     * Validate the provided email address.
     *
     * @param string $email
     * @return bool
     */
    public function isValid(string $email): bool
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($email, [new NotBlank(), new Email()]);

        return count($violations) === 0;
    }
}
