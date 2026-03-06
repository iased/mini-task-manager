<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

final class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'app_task_index')]
    public function index(Request $request,
                          TaskRepository $taskRepository
    ): Response {
        $status = $request->query->get('status');

        if ($status === 'done') {
            $tasks = $taskRepository->findBy(
                [
                    //'owner' => $this->getUser(),
                    'isDone' => true
                ], 
                ['createdAt' => 'DESC']);
        } elseif ($status === 'open') {
            $tasks = $taskRepository->findBy(
                [
                    //'owner' => $this->getUser(),
                    'isDone' => false
                ], 
                ['createdAt' => 'DESC']);
        } else {
            $tasks = $taskRepository->findBy(
                [
                    //'owner' => $this->getUser()
                ], 
                ['createdAt' => 'DESC']);
        }

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'current_status' => $status
        ]);
    }

    #[Route('/tasks/new', name: 'app_task_new')]
    public function new(Request $request, 
                        EntityManagerInterface $entityManager,
                        UserRepository $userRepository
    ): Response {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //$task->setOwner($this->getUser());

            $user = $userRepository->findOneBy([]);
            $task->setOwner($user);

            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index');
        }

        return $this->render('task/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/tasks/{id}/edit', name: 'app_task_edit')]
    public function edit(Task $task,
                         Request $request, 
                         EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form,
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/delete', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Task $task,
                           Request $request, 
                           EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();

            $this->addFlash('success', 'Task deleted successfully.');
        }

        return $this->redirectToRoute('app_task_index');
    }

    #[Route('/tasks/{id}/toggle', name: 'app_task_toggle', methods: ['POST'])]
    public function toggle(Task $task,
                           Request $request,
                           EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('toggle'.$task->getId(), $request->request->get('_token'))) {
            $task->setIsDone(!$task->isDone());
            $entityManager->flush();

            $this->addFlash('success', 'Task status updated successfully.');
        }

        return $this->redirectToRoute('app_task_index');
    }
}

