<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\User;
use App\Entity\Task;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) { }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('junior@test.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'password')
        );

        $manager->persist($user);

        $dev = new User();
        $dev->setEmail('dev@test.com');
        $dev->setRoles(['ROLE_USER']);
        $dev->setPassword(
            $this->passwordHasher->hashPassword($dev, 'password')
        );

        $manager->persist($dev);

        $juniorTasks = [
            [
                'title' => 'Fix login redirect after authentication',
                'description' => 'Investigate redirect path after login and ensure correct route configuration.',
                'done' => true
            ],
            [
                'title' => 'Implement task ownership validation',
                'description' => 'Ensure only the task owner can edit or delete tasks.',
                'done' => true
            ],
            [
                'title' => 'Add filtering by task status',
                'description' => 'Allow filtering tasks by open or done using query parameters.',
                'done' => false
            ],
            [
                'title' => 'Implement search by task title',
                'description' => 'Add title search functionality using Doctrine QueryBuilder.',
                'done' => false
            ],
            [
                'title' => 'Add validation to task form',
                'description' => 'Ensure title length and required fields are validated.',
                'done' => false
            ]
        ];

        foreach ($juniorTasks as $taskData) {
            $task = new Task();
            $task->setTitle($taskData['title']);
            $task->setDescription($taskData['description']);
            $task->setIsDone($taskData['done']);
            $task->setOwner($user);
            $task->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($task);
        }

        $devTasks = [
            [
                'title' => 'Refactor TaskRepository queries',
                'description' => 'Move filtering logic from controller to repository.',
                'done' => true
            ],
            [
                'title' => 'Add pagination to task list',
                'description' => 'Limit tasks per page and improve performance.',
                'done' => false
            ],
            [
                'title' => 'Improve UI feedback with flash messages',
                'description' => 'Display notifications after CRUD operations.',
                'done' => false
            ],
            [
                'title' => 'Write functional tests for TaskController',
                'description' => 'Add tests for CRUD operations and access control.',
                'done' => false
            ],
            [
                'title' => 'Optimize database queries',
                'description' => 'Review and optimize Doctrine queries used in task listing.',
                'done' => false
            ]
        ];

        foreach ($devTasks as $taskData) {
            $task = new Task();
            $task->setTitle($taskData['title']);
            $task->setDescription($taskData['description']);
            $task->setIsDone($taskData['done']);
            $task->setOwner($dev);
            $task->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($task);
        }

        $manager->flush();
    }
}
