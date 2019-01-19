<?php

namespace App\Entity\Types;

class CustomDateTime extends \DateTime
{
    public function __toString()
    {
        return $this->format('d-m-Y H:i');
    }
}