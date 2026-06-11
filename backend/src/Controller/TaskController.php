<?php

namespace App\Controller;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TaskController extends AbstractController
{
    #[Route('/api/tasks', name: 'tasks_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();
        if ($tasks === []) {
            return new JsonResponse('No tasks found', 200);
        }

        // return $this->json($tasks);
        return new JsonResponse(array_map(function ($task) {
            return [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'description' => $task->getDescription(),
                'priority' => $task->getPriority(),
                'status' => $task->getStatus(),
                // 'tildelt til' => $task->getUser(),
                'tildelt til' => $task->getUser()
                    ? [
                        'id' => $task->getUser()->getId(),
                        // 'brugernavn' => $task->getUser()->getUsername(),
                        'email' => $task->getUser()->getEmail(),
                        // 'rolle' => $task->getUser()->getRole(),
                    ]
                    : null,
            ];
        }, $tasks));
    }


    #[Route('/api/tasks/search', name: 'task_search', methods: ['GET'])]
    #[IsGranted('ROLE_VET')]
    public function search(TaskRepository $taskRepository): JsonResponse
    {
        try {
            $tasks = $taskRepository->getAllWithStatus(TaskStatus::TODO);
            return $this->json($tasks);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    #[Route('/api/tasks/{id}', name: 'task_find_one', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_VET')]
    public function findOne(Task $task): Response
    {
        return new JsonResponse([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'priority' => $task->getPriority(),
            'status' => $task->getStatus(),
            'tildelt til' => $task->getUser()
                ? [
                    'id' => $task->getUser()->getId(),
                    // 'brugernavn' => $task->getUser()->getUsername(),
                    'email' => $task->getUser()->getEmail(),
                    // 'rolle' => $task->getUser()->getRole(),
                ]
                : null,
        ]);
    }


    #[Route('api/tasks/create', name: 'create_task', methods: ['POST'])]
    #[IsGranted('ROLE_VET')]
    public function create(
        EntityManagerInterface $entityManager, 
        Request $request,
        ValidatorInterface $validator
    ): Response
    {
        try {
            // $user = $entityManager->getRepository(User::class)->find(1);

            $task = new Task();


            $task->setTitle('Sej titel.');
            $task->setDescription('Sej beskrivelse på opgaven.');
            $task->setPriority(TaskPriority::Moderate);
            $task->setCreatedAt(new DateTimeImmutable("now"));
            $task->setDueDate(new DateTimeImmutable("+1 day"));
          
            // relates this taska to a user
            // $task->setUser($user);

            // $request->toArray();

            $errors = $validator->validate($task);

            if (count($errors) > 0) {
                $errorMessages = [];
                
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage(); 
                }

                return new JsonResponse([
                    'errors' => $errorMessages, 
                ], 400);
            }
            
            // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $entityManager->persist($task);
    
            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
    
            return new Response(
                'Oprettede ny opgave med titlen: '.$task->getTitle()
                . ' og tildelte opgaven til: ' . $task->getUser()->getEmail()
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    #[Route('/api/tasks/{id}', name: 'app_task_edit', methods: ['PATCH'])]
    #[IsGranted('ROLE_VET')]
    public function edit(Task $task): Response
    {
        return new JsonResponse([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'priority' => $task->getPriority(),
            'status' => $task->getStatus(),
        ]);
    }

}
