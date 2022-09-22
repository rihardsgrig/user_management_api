<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Application\Handler\Exception\InvalidUserEmailException;
use App\Application\Handler\Exception\ResourceNotFoundException;
use App\Application\Handler\Exception\UserAreadyExistsException;
use App\Application\Handler\User\CreateUserHandler;
use App\Application\Handler\User\DeleteUserHandler;
use App\Application\Handler\User\Dto\CreateUserRequest;
use App\Application\Handler\User\ListUsersHandler;
use App\Application\Handler\User\ShowUserHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ListUsersHandler $listHandler,
        private readonly ShowUserHandler $showHandler,
        private readonly CreateUserHandler $createHandler,
        private readonly DeleteUserHandler $deleteHandler
    ) {
    }

    #[Route('/users', name: 'app_user', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $offset = (int) ($request->query->get('offset') ?? 0);
        $limit = (int) ($request->query->get('limit') ?? 10);

        $data = $this->listHandler->handle($offset, $limit);

        return $this->json($data->data());
    }

    #[Route(path: '/users', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $dto = $this->serializer->deserialize($request->getContent(), CreateUserRequest::class, 'json');

        try {
            $data = $this->createHandler->handle($dto);
        } catch (UserAreadyExistsException|InvalidUserEmailException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        return $this->json($data->data());
    }

    #[Route(path: '/users/{id}', methods: ['GET'])]
    public function show(int $id): Response
    {
        try {
            $data = $this->showHandler->handle($id);
        } catch (ResourceNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        return $this->json($data->data());
    }

    #[Route(path: '/users/{id}', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        try {
            $response = $this->deleteHandler->handle($id);
        } catch (ResourceNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        return new JsonResponse($response->data(), Response::HTTP_NO_CONTENT);
    }
}
