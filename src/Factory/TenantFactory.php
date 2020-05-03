<?php

namespace App\Factory;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Tenant;

final class TenantFactory
{
    public function createFormRequest(Request $request, $user): Tenant 
    {
        $data =json_decode($request->getContent(), true);

        $tenant = new Tenant();
  
        $tenant->setName($data['name']);
        $tenant->setMeterNumber($data['meterNumber']);
        $tenant->setMeterInitialReading($data['meterInitialReading']);

        $tenant->setOwner($user);

        return $tenant;
    }
}