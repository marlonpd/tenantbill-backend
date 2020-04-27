<?php

namespace App\Factory;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

final class UserFactory
{
    public function createFormRequest(Request $request): User 
    {
        $data =json_decode($request->getContent(), true);

        $user = new User();
  
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($data['password']);

        return $user;
    }
}