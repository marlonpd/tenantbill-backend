<?php

namespace App\Controller\Auth;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Factory\UserFactory;
use App\Traits\ResponseTrait;

class RegisterController extends AbstractController
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
    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        UserFactory $userFactory
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->userFactory = $userFactory;
    }

    /**
     * @Route("/api/register", methods={"POST"}, name="auth_register")
     */
    public function index(Request $request, ValidatorInterface $validator) {

        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['username']) || empty($data['password']) ) {

            $response = [
                'error'   => 'Invalid credential.',
            ];    
    
            return $this->respond($response)->setStatusCode(422);
        }

        $emailConstraint = new Assert\Email();

        $errors = $validator->validate(
            $data['email'],
            $emailConstraint
        );

        if (count($errors) > 0 ) {
            $response = [
                'error'   => 'Invalid email.',
            ];    
    
            return $this->respond($response)->setStatusCode(422);
        } 


        if (strlen($data['username']) < 6 || strlen($data['password']) < 6) {
            $response = [
                'error'   => 'Username or password must be min of 6 char.',
            ];    
    
            return $this->respond($response)->setStatusCode(422);
        }
        
        $user = $this->userRepository->findOneByEmail($data["email"]);

        if (!empty($user)) {
            $response = [
                'error'   => 'Email is already in used.',
            ];    
    
            return $this->respond($response)->setStatusCode(422);
        }

        $user = $this->userRepository->findOneByUsername($data["username"]);

        if (!empty($user)) {
            $response = [
                'error'   => 'Username is already in used.',
            ];    
    
            return $this->respond($response)->setStatusCode(422);
        }

        $user = $this->userFactory->createFormRequest($request);
        $encoded = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encoded);
        $this->userRepository->save($user);

        $response = [
            'status'    => 'success',
            'message'   => 'Successfully created an account.',
        ];   

        return $this->respond($response);
    }

    
}
