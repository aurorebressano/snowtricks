<?php

namespace App\Service;

use App\Entity\User;

interface RegistrationMailInterface
{
    public function __invoke(User $user): void;
}