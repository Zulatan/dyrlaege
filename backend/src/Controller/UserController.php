<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function PHPUnit\Framework\throwException;

#[Route('/users')]
final class UserController extends AbstractController
{
    #[Route(name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        if ($users === []) {
            return new JsonResponse('No users found', 200);
        }

        // return $this->json($tasks);
        return new JsonResponse(array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'bruger' => $user->getUsername(),
                'mail' => $user->getEmail()
            ];
        }, $users));
    }

    #[Route('/{id}', name: 'user_find_one', methods: ['GET'])]
    public function show(?User $user): Response
    {
        if ($user === null) {
            return new JsonResponse('No users found', 200);
        } 
        return new JsonResponse([
            'bruger' => $user->getUsername(),
            'mail' => $user->getEmail()
        ]);
    }


    // #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $user = new User();
    //     $form = $this->createForm(UserType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($user);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('user/new.html.twig', [
    //         'user' => $user,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/create', name: 'user_create', methods: ['POST'])]
    public function create(
        EntityManagerInterface $entityManager, 
        Request $request,
        ValidatorInterface $validator
    ): Response
    {
        try {
            $user = new User();
            $user->setEmail('new@mail.test');
            $user->setUsername('Namus');
            $user->setPassword('123456789');

            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                $errorMessages = [];
                
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage(); 
                }

                return new JsonResponse([
                    'errors' => $errorMessages, 
                ], 400);
            }
            
            // tell Doctrine you want to (eventually) save the user (no queries yet)
            $entityManager->persist($user);
    
            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
    
            return new Response('Oprettede ny bruger med titlen: '.$user->getUsername());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    #[Route('/edit/{id}', name: 'user_edit')]
    public function update(EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

        $user->setUsername('New user name!');
        $entityManager->flush();

        return $this->redirectToRoute('user', [
            'id' => $user->getId()
        ]);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        User $user, 
        EntityManagerInterface $entityManager
    ): Response
    {
        // if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
        //     $entityManager->remove($user);
        //     $entityManager->flush();
        // }
        try {
            $entityManager->remove($user);
            $entityManager->flush();
            return new Response($user->getUsername() . ' was deleted.');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        // return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
