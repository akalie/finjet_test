<?php declare(strict_types = 1);


namespace Finjet\Middlewares;


use Finjet\ErrorJsonResponse;
use Finjet\Repositories\UsersRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

class AuthMiddleware implements MiddlewareInterface
{
    private UsersRepositoryInterface $repository;

    public function __construct(UsersRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getParsedBody()['token'] ?? '';
        if (!$token) {
            $token = $request->getQueryParams()['token'] ?? '';
        }
        if (!Uuid::isValid($token)) {
            return new ErrorJsonResponse(['token' => 'Please provide token']);
        }

        if (!$this->repository->getByToken($token)) {
            return new ErrorJsonResponse(['token' => 'Wrong token']);
        }

        $request = $request
            ->withParsedBody(array_diff_key($request->getParsedBody(), ['token' => 1]))
            ->withQueryParams(array_diff_key($request->getQueryParams(), ['token' => 1]))
        ;

        return $handler->handle($request);
    }
}