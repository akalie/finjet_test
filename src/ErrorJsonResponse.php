<?php declare(strict_types=1);


namespace Finjet;


use Laminas\Diactoros\Response\JsonResponse;

class ErrorJsonResponse extends JsonResponse
{
    public function __construct(array $errors, int $status = 200, array $headers = [], int $encodingOptions = self::DEFAULT_JSON_FLAGS)
    {
        $data = ['errors' => $errors];
        parent::__construct($data, $status, $headers, $encodingOptions);
    }
}