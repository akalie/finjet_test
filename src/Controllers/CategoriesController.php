<?php declare(strict_types=1);

namespace Finjet\Controllers;

use Finjet\Repositories\CategoriesRepositoryInterface;
use Psr\Log\LoggerInterface;

class CategoriesController extends ApiController
{
    public function __construct(LoggerInterface $logger, CategoriesRepositoryInterface $repository)
    {
        $this->logger = $logger;
        $this->repository = $repository;
    }

    public function validateFields(array $attrs): array
    {
        return [];
    }
}