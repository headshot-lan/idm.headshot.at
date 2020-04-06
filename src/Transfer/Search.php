<?php


namespace App\Transfer;

use Symfony\Component\Validator\Constraints as Assert;

final class Search
{
    /**
     * @Assert\All({
     *      @Assert\NotBlank,
     *      @Assert\Uuid(strict=false)
     * })
     */
    public $uuid = [];
}