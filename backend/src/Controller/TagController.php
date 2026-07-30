<?php

namespace App\Controller;

use App\Api\TagViewFactory;
use App\Entity\User;
use App\Service\Exception\TagConflictException;
use App\Service\Exception\TagNotFoundException;
use App\Service\TagService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/tags')]
final class TagController extends AbstractController
{
    #[Route('', name: 'api_tags_index', methods: ['GET'])]
    public function index(TagService $tagService, TagViewFactory $tagViewFactory): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['message' => $this->trans('api.auth_required')], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $tags = array_map(
            $tagViewFactory->create(...),
            $tagService->findVisibleForUser($user)
        );

        return $this->json($tags);
    }

    #[Route('', name: 'api_tags_create', methods: ['POST'])]
    public function create(
        Request $request,
        TagService $tagService,
        TagViewFactory $tagViewFactory,
        ValidatorInterface $validator,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['message' => $this->trans('api.auth_required')], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            /** @var array{name?:mixed, emoji?:mixed, color?:mixed} $payload */
            $payload = $request->toArray();
        } catch (\JsonException) {
            return $this->json(['message' => $this->trans('api.invalid_json')], JsonResponse::HTTP_BAD_REQUEST);
        }

        $name = is_string($payload['name'] ?? null) ? $payload['name'] : '';
        $emoji = is_string($payload['emoji'] ?? null) ? $payload['emoji'] : null;
        $color = is_string($payload['color'] ?? null) ? $payload['color'] : null;

        try {
            $tag = $tagService->createForUser($user, $name, $emoji, $color);
        } catch (TagConflictException $exception) {
            return $this->json(['message' => $this->trans($exception->getMessage())], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $violations = $validator->validate($tag);

        if (count($violations) > 0) {
            return $this->json(['message' => $violations[0]->getMessage()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tagService->save($tag);

        return $this->json($tagViewFactory->create($tag), JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_tags_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        int $id,
        Request $request,
        TagService $tagService,
        TagViewFactory $tagViewFactory,
        ValidatorInterface $validator,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['message' => $this->trans('api.auth_required')], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            /** @var array{name?:mixed, emoji?:mixed, color?:mixed} $payload */
            $payload = $request->toArray();
        } catch (\JsonException) {
            return $this->json(['message' => $this->trans('api.invalid_json')], JsonResponse::HTTP_BAD_REQUEST);
        }

        $name = is_string($payload['name'] ?? null) ? $payload['name'] : '';
        $emoji = is_string($payload['emoji'] ?? null) ? $payload['emoji'] : null;
        $color = is_string($payload['color'] ?? null) ? $payload['color'] : null;

        try {
            $tag = $tagService->updateForUser($user, $id, $name, $emoji, $color);
        } catch (TagNotFoundException) {
            return $this->json(['message' => $this->trans('tag.api.not_found')], JsonResponse::HTTP_NOT_FOUND);
        } catch (TagConflictException $exception) {
            return $this->json(['message' => $this->trans($exception->getMessage())], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $violations = $validator->validate($tag);

        if (count($violations) > 0) {
            return $this->json(['message' => $violations[0]->getMessage()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tagService->save($tag);

        return $this->json($tagViewFactory->create($tag));
    }

    #[Route('/{id}', name: 'api_tags_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id, TagService $tagService): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['message' => $this->trans('api.auth_required')], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            $tagService->deleteForUser($user, $id);
        } catch (TagNotFoundException) {
            return $this->json(['message' => $this->trans('tag.api.not_found')], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
