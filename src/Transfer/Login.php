<?php


namespace App\Transfer;

use Symfony\Component\Validator\Constraints as Assert;


final class Login
{
    /**
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    public $email;

    /**
     * @Assert\NotBlank()
     */
    public $password;
}