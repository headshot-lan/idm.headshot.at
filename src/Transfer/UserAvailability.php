<?php

namespace App\Transfer;

use Symfony\Component\Validator\Constraints as Assert;

final class UserAvailability
{
    /**
     * @Assert\NotBlank()
     */
    public $mode;

    /**
     * @Assert\NotBlank()
     */
    public $name;
}
