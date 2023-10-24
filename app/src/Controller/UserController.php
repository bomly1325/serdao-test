<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\DriverManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Alias;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    #[Route('/user', methods: ['POST'], name: 'create_a_user')]
    public function createUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $firstname = $request->get("firstname");
        $lastname = $request->get("lastname");
        $address = $request->get("address");

        // validate user data
        if (!$firstname || !$lastname || !$address) {
            $this->addFlash(
                'error',
                'All fields are required.'
            );

            return $this->redirectToRoute('get_users', ['firstname' => $firstname, 'lastname' => $lastname, 'address' => $address]);
        }

        // create user if no error
        $user = new User();
        $data = $firstname . " - " . $lastname . " - " . $address;
        $user->setData($data);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Add a user successfully!'
        );

        
        return $this->redirectToRoute('get_users');
    }

    #[Route('/user', methods: ['GET'],  name: 'get_users')]
    public function getUsers(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        return $this->render('user.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/user/{id}/delete', methods: ['GET'])]
    public function deleteUser(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash(
            'success',
            'Delete a user successfully!'
        );
        return $this->redirectToRoute('get_users');
    }
}