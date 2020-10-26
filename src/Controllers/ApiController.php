<?php declare(strict_types=1);

namespace Finjet\Controllers;

use Finjet\ErrorJsonResponse;
use Finjet\Repositories\ApiRepositoryInterface;
use Finjet\ResultJsonResponse;
use Illuminate\Database\Eloquent\Model;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Psr\Log\LoggerInterface;

abstract class ApiController
{
    protected LoggerInterface $logger;
    protected ApiRepositoryInterface $repository;

    /**
     *  Should return array of errors - ['token' => 'respect my authority', ...]
     */
    abstract public function validateFields(array $attrs): array;

    public function show(ServerRequest $request, $args = []): ResultJsonResponse
    {
        return new ResultJsonResponse($this->repository->getAll()->toArray());
    }

    public function update(ServerRequest $request): JsonResponse
    {
        $attrs = $request->getParsedBody();
        if ($errors = $this->validateFields($attrs)) {
            return new ErrorJsonResponse($errors);
        }
        $id = (int)$attrs['id'];
        unset($attrs['id']);
        $res = $this->repository->update($id, $attrs);

        return new ResultJsonResponse(['success' => $res]);
    }

    public function delete(ServerRequest $request): JsonResponse
    {
        $id = $request->getParsedBody()['id'] ?? null;
        $id = (int) $id;
        if (!$id) {
            return new ErrorJsonResponse(['No id provided.']);
        }
        $res = $this->repository->delete($id);

        return new ResultJsonResponse(['success' => $res]);
    }

    public function create(ServerRequest $request): JsonResponse
    {
        $attrs = $request->getParsedBody();

        if ($errors = $this->validateFields($attrs)) {
            return new ErrorJsonResponse($errors);
        }
        /** @var ?Model $model */
        $model = $this->repository->create($attrs);

        if (null !== $model) {
            return new ResultJsonResponse(['success' => true, 'created' => $model]);
        }

        return new ErrorJsonResponse(['Some error happened during creating.']);
    }
}