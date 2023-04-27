<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\PingHandler;
use Laminas\Diactoros\Response\JsonResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class PingHandlerTest extends TestCase
{
    public function testResponse(): void
    {
        $pingHandler = new PingHandler();
        $response = $pingHandler->handle(
            $this->createMock(ServerRequestInterface::class),
        );

        /** @var array<string, mixed> $json */
        $json = \json_decode((string) $response->getBody(), true, 512, \JSON_THROW_ON_ERROR);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertArrayHasKey('ack', $json);
    }
}
