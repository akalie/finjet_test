<?php declare(strict_types=1);

namespace Finjet;

use Laminas\Diactoros\Response;

class SimpleResponseSender
{
    public function send(Response $response): void
    {
        http_response_code($response->getStatusCode());

        foreach ($response->getHeaders() as $name => $value) {
            header(sprintf('%s: %s', $name, $value), false);
        }

        echo $response->getBody();
        fastcgi_finish_request();
    }
}