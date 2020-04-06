<?php


namespace App\Transfer;

use Symfony\Component\Validator\Constraints as Assert;

final class Error
{
    /**
     * @Assert\Type(type="int")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     */
    public $message;


    static public function withMessage(string $msg)
    {
        $ret = new self();
        $ret->message = $msg;
        return $ret;
    }
}