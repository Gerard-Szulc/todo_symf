<?php


namespace App\Tests;


use App\Entity\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    function testItemEntity () {
        self::assertClassHasAttribute('position', Item::class);
        self::assertClassHasAttribute('filePath', Item::class);
        self::assertClassHasAttribute('description', Item::class);
        self::assertClassHasAttribute('title', Item::class);
        self::assertClassHasAttribute('deadlineAt', Item::class);
    }
}
