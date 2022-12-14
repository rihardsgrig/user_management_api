<?php

declare(strict_types=1);

namespace App\Controller\Group;

use App\Application\Handler\Exception\ResourceNotFoundException;
use App\Application\Handler\Exception\UserIsMemberException;
use App\Application\Handler\Exception\UserMissingException;
use App\Application\Handler\Group\AddMemberHandler;
use App\Application\Handler\Group\ListMembersHandler;
use App\Application\Handler\Group\RemoveMemberHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MembershipController extends AbstractController
{
    public function __construct(
        private readonly ListMembersHandler $listMemberHandler,
        private readonly AddMemberHandler $addMemberHandler,
        private readonly RemoveMemberHandler $removeMemberHandler
    ) {
    }

    #[Route('/groups/{groupId}/members', name: 'app_members', methods: ['GET'])]
    public function index(Request $request, int $groupId): Response
    {
        try {
            $members = $this->listMemberHandler->handle($groupId);
        } catch (ResourceNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        return $this->json($members->data());
    }

    #[Route('/groups/{groupId}/members', methods: ['POST'])]
    public function add(Request $request, int $groupId): Response
    {
        $userId = 0;
        $body = json_decode((string) $request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (isset($body['user_id'])) {
            $userId = (int) $body['user_id'];
        }

        try {
            $response = $this->addMemberHandler->handle($groupId, $userId);
        } catch (ResourceNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        } catch (UserMissingException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        } catch (UserIsMemberException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        return new JsonResponse($response->data(), Response::HTTP_CREATED);
    }

    #[Route('/groups/{groupId}/members/{userId}', methods: ['DELETE'])]
    public function remove(int $groupId, int $userId): Response
    {
        try {
            $response = $this->removeMemberHandler->handle($groupId, $userId);
        } catch (ResourceNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        } catch (UserMissingException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        return new JsonResponse($response->data(), Response::HTTP_NO_CONTENT);
    }
}
