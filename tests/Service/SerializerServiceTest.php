<?php

namespace App\Tests\Service;

use App\Entity\Item;
use App\Service\SerializerService;
use PHPUnit\Framework\TestCase;

class SerializerServiceTest extends TestCase
{
    public function testSerialize (): void
    {
        $serializerService = new SerializerService();
        self::assertJson($serializerService->serialize(new Item()));
    }
}
