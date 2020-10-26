<?php declare(strict_types=1);

namespace Finjet\Controllers;


use Finjet\ErrorJsonResponse;
use Finjet\Repositories\ItemsRepositoryInterface;
use Finjet\ResultJsonResponse;
use Illuminate\Database\Eloquent\Model;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Psr\Log\LoggerInterface;

class ItemsController extends ApiController
{
    public function __construct(LoggerInterface $logger, ItemsRepositoryInterface $repository)
    {
        $this->logger = $logger;
        $this->repository = $repository;
    }

    public function show(ServerRequest $request, $args = []): ResultJsonResponse
    {
        if ($categoryName = $args['category']) {
            $categoryName = urldecode($categoryName);
            return new ResultJsonResponse($this->repository->getByCategory($categoryName));
        }

        return new ResultJsonResponse($this->repository->getAll()->toArray());
    }

    public function create(ServerRequest $request): JsonResponse
    {
        $attrs = $request->getParsedBody();
        $categoriesList = $attrs['categories'] ?? [];
        $attrs = array_diff_key($attrs, ['categories' => 1]);
        if ($errors = $this->validateFields($attrs)) {
            return new ErrorJsonResponse($errors);
        }
        /** @var ?Model $model */
        $model = $this->repository->create($attrs, $categoriesList);

        if (null === $model) {
            return new ErrorJsonResponse(['Some error happened during creating.']);
        }
        return new ResultJsonResponse(['success' => true, 'created' => $model]);

    }

    public function validateFields(array $attrs): array
    {
        return [];
    }
}