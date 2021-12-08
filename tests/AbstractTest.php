<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

// LOOK IN FIXTURES FOR PRE SET USER ACCOUNTS : /fixtures/user.yaml

abstract class AbstractTest extends ApiTestCase
{
    private $token;
    private $clientWithCredentials;

    use RefreshDatabaseTrait;

    public function setUp(): void
    {
        self::bootKernel();
    }

    protected function createClientWithAdminCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken('admin@admin.pl','adminStrongPass123');

        return static::createClient([], ['headers' => ['authorization' => 'Bearer ' . $token,'content-type' => 'application/json']]);
    }

    protected function createClientWithUserCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken('user@user.pl','userStrongPass123');

        return static::createClient([], ['headers' => ['authorization' => 'Bearer ' . $token,'content-type' => 'application/json']]);
    }

    /**
     * Use other credentials if needed.
     */
    protected function getToken($email,$password): string
    {

        if ($this->token) {
            return $this->token;
        }


        $response = static::createClient()->request('POST', '/api/login',
            [
                'json' => [
                    "username" =>  $email,
                    "password" => $password
                ],'headers' => ['content-type' => 'application/json']
            ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent());
        $this->token = $data->token;

        return $data->token;
    }
}