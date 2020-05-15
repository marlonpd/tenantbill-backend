<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use App\Repository\UserRepository;
use App\Repository\PowerRateRepository;
use App\Factory\PowerRateFactory;
use App\Traits\ResponseTrait;

class PowerRateController extends AbstractController
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
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var PowerRateFactory
     */
    private $powerRateFactory;

    public function __construct(
        UserRepository $userRepository, 
        PowerRateRepository $powerRateRepository,
        PowerRateFactory $powerRateFactory,
        SerializerInterface $serializer) 
    {
        $this->powerRateRepository = $powerRateRepository;
        $this->userRepository = $userRepository;
        $this->powerRateFactory = $powerRateFactory;
        $this->serializer = $serializer;

    }
    /**
     * @Route("/api/power-rates/get", methods={"GET"}, name="fetch_power_rates")
     */
    public function index(SerializerInterface $serializer)
    {
        $user = $this->getUser();

        $powerRates = $this->powerRateRepository->findByOwer($user->getId());

        $response = [
            'rates'   => $this->serializer->serialize($powerRates, 'json', ['groups' => ['primary']]),
            'message'   => '',
        ];    

        return $this->respond($response);
    }

    /**
     * @Route("/api/power-rate/get", methods={"GET"}, name="fetch_power_rate")
     */
    public function getPowerRate()
    {
        $user = $this->getUser();

        $powerRate = $this->powerRateRepository->findCurrentRateByOwner($user->getId());

        $response = [
            'rate'   => $powerRate->getRate(),
        ];    

        return $this->respond($response);
    }

    /**
     * @Route("/api/power-rate/create", methods={"POST"}, name="create_power_rate")
     */
    public function create(Request $request, SerializerInterface $serializer)
    {
        $user = $this->getUser();

        $powerRate = $this->powerRateRepository->transform($request, $user);

        if (empty($powerRate->getRate())) {
            $response = [
                'errors'   => 'Power rate is required',
            ];    
    
            return $this->respond($response);
        }

        $this->powerRateRepository->save($powerRate);

        $powerRate = $this->powerRateRepository->getLastInsertedByOwner($user->getId());

        $response = [
            'powerRate' => [
                'rate'    => $powerRate->getRate(),
                'created' => $powerRate->getCreated()->format('Y-m-d')
            ],
            'message'   => 'Successfully added new power rate!',
        ];    

        return $this->respond($response);
    }
    
}
