<?php

declare(strict_types=1);

namespace App\Controller\Group;

use App\Application\Handler\Exceptions\MembersAttachedToGroupException;
use App\Application\Handler\Exceptions\ResourceNotFoundException;
use App\Application\Handler\Group\CreateGroupHandler;
use App\Application\Handler\Group\DeleteGroupHandler;
use App\Application\Handler\Group\Dto\CreateGroupRequest;
use App\Application\Handler\Group\ListGroupsHandler;
use App\Application\Handler\Group\ShowGroupHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GroupController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ListGroupsHandler $listHandler,
        private readonly ShowGroupHandler $showHandler,
        private readonly CreateGroupHandler $createHandler,
        private readonly DeleteGroupHandler $deleteHandler
    ) {
    }

    #[Route('/groups', name: 'app_group', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $offset = (int) ($request->query->get('offset') ?? 0);
        $limit = (int) ($request->query->get('limit') ?? 10);

        return $this->json($this->listHandler->handle($offset, $limit));
    }

    #[Route(path: '/groups', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $dto = $this->serializer->deserialize($request->getContent(), CreateGroupRequest::class, 'json');
        $data = $this->createHandler->handle($dto);

        return $this->json($data->toArray());
    }

    #[Route(path: '/groups/{id}', methods: ['GET'])]
    public function show(int $id): Response
    {
        try {
            $data = $this->showHandler->handle($id);
        } catch (ResourceNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        return $this->json($data->toArray());
    }

    #[Route(path: '/groups/{id}', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        try {
            $this->deleteHandler->handle($id);
        } catch (ResourceNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        } catch (MembersAttachedToGroupException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
