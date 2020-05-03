<?php

namespace App\Factory;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\PowerRate;

final class PowerRateFactory
{
    public function createFormRequest(Request $request, $user): PowerRate 
    {
        $data =json_decode($request->getContent(), true);

        $powerRate = new PowerRate();
  
        $powerRate->setRate($data['rate']);
        $powerRate->setOwner($user);

        return $powerRate;
    }
}