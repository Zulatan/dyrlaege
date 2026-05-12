<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\TestCase;

final class TaskControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        $client->request('GET', '/tasks');

        self::assertResponseIsSuccessful();

        $data = json_decode(
            $client->getResponse()->getContent(),
            true
        );

        self::assertIsArray($data);
    }

    public function testSearch(): void
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/{id}');

        self::assertResponseIsSuccessful();

        $data = json_decode(
            $client->getResponse()->getContent(),
            true
        );

        self::assertIsArray($data);
    }
}
