<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use App\Repository\UserRepository;
use App\Repository\TenantRepository;
use App\Repository\PowerRateRepository;
use App\Factory\TenantFactory;
use App\Traits\ResponseTrait;

class TenantController extends AbstractController
{
    use ResponseTrait;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var PowerRateRepository
     */
    private $powerRateRepository;

    /**
     * @var TenantRepository
     */
    private $tenantRepository;
        /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var TenantFactory
     */
    private $tenantFactory;

    public function __construct(
        UserRepository $userRepository, 
        PowerRateRepository $powerRateRepository,
        TenantRepository $tenantRepository,
        TenantFactory $tenantFactory,
        SerializerInterface $serializer) 
    {
        $this->tenantRepository = $tenantRepository;
        $this->powerRateRepository = $powerRateRepository;
        $this->userRepository = $userRepository;
        $this->tenantFactory = $tenantFactory;
        $this->serializer = $serializer;

    }
    /**
     * @Route("/api/tenants", methods={"GET"}, name="fetch_tenants")
     */
    public function index(SerializerInterface $serializer)
    {
        $tenants = $this->tenantRepository->findAll();

        $response = [
            'tenants'   => $this->serializer->serialize($tenants, 'json', ['groups' => ['primary']]),
            'message'   => '',
        ];    

        return $this->respond($response);
    }

    /**
     * @Route("/api/tenant/{tenantId}/get", methods={"GET"}, name="fetch_tenant")
     */
    public function getOne(int $tenantId, SerializerInterface $serializer)
    {
        $tenant = $this->tenantRepository->find($tenantId);

        if (empty($tenant)) {
            $response = [
                'errors'   => 'Tenant not found!',
            ];    
    
            return $this->respond($response);
        }

        $user = $this->getUser();

        $powerRate = $this->powerRateRepository->findCurrentRateByOwner($user->getId());

        $response = [
            'tenant'    => [
                'id'                    => $tenant->getId(),
                'name'                  => $tenant->getName(),
                'meterNumber'           => $tenant->getMeterNumber(),
                'meterInitialReading'   => $tenant->getMeterInitialReading(),
                'created'               => $tenant->getCreated()->format('Y-m-d'),
                'ratePerKwh'            => $powerRate->getRate(),
            ],
            'message'   => '',
        ];    

        return $this->respond($response);
    }

    /**
     * @Route("/api/tenant/create", methods={"POST"}, name="create_tenant")
     */
    public function create(Request $request, SerializerInterface $serializer)
    {
        $user = $this->getUser();

        $tenant = $this->tenantFactory->createFormRequest($request, $user);

        if (empty($tenant->getName())) {
            $response = [
                'errors'   => 'Tenant name is required',
            ];    
    
            return $this->respond($response);
        }

        $this->tenantRepository->save($tenant);

        $tenant = $this->tenantRepository->getLastInserted($user->getId());

        $response = [
            'tenant'    => [
                'id'            => $tenant->getId(),
                'name'          => $tenant->getName(),
                'meterNumber'   => $tenant->getMeterNumber()
            ],
            'message'   => 'Successfully added new tenant!',
        ];    

        return $this->respond($response);
    }
    
}
