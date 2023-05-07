<?php

namespace App\Service;

use App\Entity\User;

interface ResetMailInterface
{
    public function __invoke(User $user): void;
}