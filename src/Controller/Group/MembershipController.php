<?php

declare(strict_types=1);

namespace App\Controller\Group;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MembershipController extends AbstractController
{
    #[Route('/membership', name: 'app_membership')]
    public function index(): Response
    {
        return new Response();
    }
}
