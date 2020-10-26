<?php declare(strict_types=1);


namespace Finjet;


use Laminas\Diactoros\Response\JsonResponse;

class ResultJsonResponse extends JsonResponse
{
    public function __construct(array $data, int $status = 200, array $headers = [], int $encodingOptions = self::DEFAULT_JSON_FLAGS)
    {
        $data = ['result' => $data];
        parent::__construct($data, $status, $headers, $encodingOptions);
    }
}