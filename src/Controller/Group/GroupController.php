<?php

namespace App\Controller\Group;

use App\Domain\Group\Group;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    public function __construct(private GroupRepositoryInterface $manager)
    {
    }

    #[Route('/groups', name: 'app_group')]
    public function index(): Response
    {
        $entity = new Group(
            'some',
            'descriptions',
        );
        $this->manager->save($entity);

        return $this->json($entity);
    }
}
