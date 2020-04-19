<?php

namespace App\Controller\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Factory\UserFactory;
use App\Traits\ResponseTrait;

class LoginController extends AbstractController
{
    use ResponseTrait;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @param UserRepository $userRepository
     */

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        UserFactory $userFactory,
        SerializerInterface $serializer
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->userFactory = $userFactory;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/api/login", name="auth_login")
     */
    public function index(Request $request, JWTTokenManagerInterface $JWTManager)
    {
        $content = json_decode($request->getContent(), true);

        $user = $this->userRepository->findOneByEmail($content["username"]);

        if (empty($user)) {
            $response = [
                'error'   => 'Invalid credential.',
            ];    
    
            return $this->respond($response)->setStatusCode(422);
        }

        $validPassword = $this->passwordEncoder->isPasswordValid(
            $user, 
            $content["password"]
        );

        if (!$validPassword) {
            $response = [
                'error'   => 'Invalid credential.',
            ];    
    
            return $this->respond($response)->setStatusCode(422);
        }

        $userClone = clone $user;
        $userClone->setPassword('');

        $response = [
            'status'    => 200,
            'token'     => $JWTManager->create($user),
            'user'      => $this->serializer->serialize($userClone, JsonEncoder::FORMAT),
            'message'   => '',
        ];    

        return $this->respond($response);
    }


    /**
     * @Route("/api/target", name="target_list")
     */
    public function target(Request $request)
    {

        $response = [
            'status'    => 200,
            'message'   => 'succcess.',
        ];    

        return $this->respond($response);
    }

}
