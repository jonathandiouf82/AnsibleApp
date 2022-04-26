<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Cart;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/api')]
class UserController extends AbstractController
{
    public function __construct(
        private Security $security,
        private SerializerInterface $serializer,
        private UserPasswordHasherInterface $pwd_hasher,
        private ManagerRegistry $doctrine
    ) {
        $this->_em = $this->doctrine->getManager();
    }

    #[Route('/register', methods: 'POST', name: 'user.register')]
    public function register(Request $req): JsonResponse
    {
        $data = json_decode($req->getContent());

        $user = new User();
        $user->setFirstname($data->firstname);
        $user->setLastname($data->lastname);
        $user->setEmail($data->email);
        $user->setLogin($data->login);
        $hashedPassword = $this->pwd_hasher->hashPassword(
            $user,
            $data->password
        );
        $user->setPassword($hashedPassword);        
        $this->_em->persist($user);

        $userCart = new Cart();
        $userCart->setOwner($user);
        $userCart->setTotalPrice(0);        
        $this->_em->persist($userCart);

        $user->setCart($userCart);
        $this->_em->flush();

        return new JsonResponse($user);
    }

    #[Route('/users', methods: 'GET', name: 'user.getInfos')]
    public function getCurrentUserInfos(): ?JsonResponse
    {
        $user = $this->getUser();
        if ($user == null) return null;
        return new JsonResponse($this->displayUser());
    }

    #[Route('/users', methods: 'PATCH', name: 'user.update')]
    public function updateCurrentUser(Request $req): JsonResponse
    {
        $data = json_decode($req->getContent());

        $user = $this->getUser();
        if (isset($data->firstname)) {
            $user->setFirstname($data->firstname);
        }
        if (isset($data->lastname)) {
            $user->setLastname($data->lastname);
        }
        if (isset($data->email)) {
            $user->setEmail($data->email);
        }
        if (isset($data->password)) {
            $hashedPassword = $this->pwd_hasher->hashPassword(
                $user,
                $data->password
            );
            $user->setPassword($hashedPassword);
        }

        $this->_em->flush();

        return new JsonResponse($this->displayUser());
    }

    public function displayUser()
    {
        $user = $this->getUser();
        $res = array(
            'id' => $user->getId(),
            'login' => $user->getLogin(),
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname()
        );
        return $res;
    }
}
