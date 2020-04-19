<?php

namespace App\Factory;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

final class UserFactory
{
    public function createFormRequest(Request $request): User 
    {
        $content =json_decode($request->getContent(), true);
        $data = $content;//['credentials'];

        $user = new User();
  
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($data['password']);

        return $user;
    }
}