<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use App\Repository\TenantRepository;
use App\Factory\TenantFactory;
use App\Repository\UserRepository;
use App\Repository\PowerRateRepository;
use App\Repository\MeterReadingRepository;
use App\Factory\PowerRateFactory;
use App\Traits\ResponseTrait;

class MeterReadingController extends AbstractController
{
    use ResponseTrait;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var TenantRepository
     */
    private $tenantRepository;

    /**
     * @var MeterReadingRepository
     */
    private $meterReadingRepository;

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
        TenantRepository $tenantRepository,
        MeterReadingRepository $meterReadingRepository,
        SerializerInterface $serializer) 
    {
        $this->powerRateRepository = $powerRateRepository;
        $this->userRepository = $userRepository;
        $this->powerRateFactory = $powerRateFactory;
        $this->meterReadingRepository = $meterReadingRepository;
        $this->tenantRepository = $tenantRepository;
        $this->serializer = $serializer;

    }

    /**
     * @Route("/api/meter-readings/{tenantId}/get", methods={"GET"}, name="fetch_meter_readings")
     */
    public function index(int $tenantId)
    {
        $tenant = $this->tenantRepository->find($tenantId);

        $meterReadings = $this->meterReadingRepository->findByTenant($tenant->getId());

        $response = [
            "meterReadings" => $this->meterReadingRepository->transformMany($meterReadings)
        ];

        return $this->respond($response);
    }

    /**
     * @Route("/api/meter-reading/create", methods={"POST"}, name="create_meter_reading")
     */
    public function create(Request $request, SerializerInterface $serializer)
    {
        $user = $this->getUser();

        $data =json_decode($request->getContent(), true);

        $tenantId = $data['tenantId'];

        $tenant = $this->tenantRepository->find($tenantId);


        if (empty($tenant)) {
            $response = [
                'errors'   => 'Tenant not found!',
            ];    
    
            return $this->respond($response);
        }

        //$isOwner = $this->tenantRepository->isOwner($tenantId, $user->getId());

        if ($tenant->getOwner()->getId() !== $user->getId()) {
            $response = [
                'errors'   => 'Tenant not found!',
            ];    
    
            return $this->respond($response);
        }

        $meterReading = $this->meterReadingRepository->transform($request, $tenant);


       // $lastMeterReading = $this->meterReadingRepository->findLastReadingByOwner($tenant->getId());


        if (empty($meterReading->getRate())) {
            $response = [
                'errors'   => 'Power rate is required',
            ];    
    
            return $this->respond($response);
        }

        if (empty($meterReading->getPresentReadingKwh())) {
            $response = [
                'errors'   => 'Kwh reading is required',
            ];    
    
            return $this->respond($response);
        }

        $this->meterReadingRepository->save($meterReading);

        $meterReading = $this->meterReadingRepository->getLastInsertedByOwner($tenant->getId());

        $response = [
            'meterReading' => [
                'fromDate'              => $meterReading->getFromDate()->format('Y-m-d'),
                'previousReadingKwh'    => $meterReading->getPreviousReadingKwh(),
                'toDate'                => $meterReading->getToDate()->format('Y-m-d'),
                'presentReadingKwh'     => $meterReading->getPresentReadingKwh(),
                'consumedKwh'           => $meterReading->getConsumedKwh(),
                'ratePerKwh'            => $meterReading->getRate(),
                'bill'                  => $meterReading->getBill()
            ],
            'message'   => 'Successfully added new power rate!',
        ];    

        return $this->respond($response);
    }
}
