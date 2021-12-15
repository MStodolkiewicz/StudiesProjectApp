<?php
#tests/UserTest.php

namespace App\Tests;

use App\Tests\CustomApiTestCase;
use App\Entity\Book;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Entity\User;

class UserTest extends AbstractTest
{
    use RefreshDatabaseTrait;

    public function testGetUserCollection(): void
    {
        $response = $this->createClientWithAdminCredentials()->request('GET', '/api/users');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => "/api/contexts/User",
            '@id' => "/api/users",
            '@type' => 'hydra:Collection',
            "hydra:totalItems" => 52,
            'hydra:view' => [
                "@id" => "/api/users?page=1",
                "@type" => "hydra:PartialCollectionView",
                "hydra:first" => "/api/users?page=1",
                "hydra:last" => "/api/users?page=11",
                "hydra:next" => "/api/users?page=2",
            ],
        ]);

        $this->assertCount(5, $response->toArray()['hydra:member']);
    }

    public function testGetUserCollectionWithoutAuthority(): void
    {
        $response = $this->createClientWithUserCredentials()->request('GET', '/api/users');

        $this->assertResponseStatusCodeSame(403);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => "/api/contexts/Error",
            '@type' => 'hydra:Error',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'Access Denied.',
        ]);
    }

    public function testGetUserById(): void
    {
        $client = $this->createClientWithAdminCredentials();

        $iri = $this->findIriBy(User::class, ['username' => 'pvandervort']);

        $client->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => "/api/contexts/User",
            '@type' => "User",
            'username' => 'pvandervort',
            'email' => 'zprosacco@hotmail.com'
        ]);
    }

    public function testGetUserByIdWithoutAuthority(): void
    {
        $client = $this->createClientWithUserCredentials();

        $iri = $this->findIriBy(User::class, ['username' => 'pvandervort']);

        $client->request('GET', $iri);

        $this->assertResponseStatusCodeSame(403);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Error',
            '@type' => 'hydra:Error',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'Access Denied.'
        ]);
    }

    public function testCreateUser(): void
    {
        $response = $this->createClientWithAdminCredentials()->request('POST', '/api/users', ['json' => [
            "email"=> "Example@example.com",
            "roles"=> ["ROLE_USER"],
            "password" => "ExamplePass123",
            "username" => "ExampleUser",
            "height" => 180,
            "weight" => 80,
            "birthDate" => "2021-12-15T11:08:25.769Z"
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "@context" => "/api/contexts/User",
            "@type" => "User",
            "email"=> "Example@example.com",
            "roles"=> ["ROLE_USER"],
            "password" => "ExamplePass123",
            "username" => "ExampleUser",
            "height" => '180',
            "weight" => 80,
        ]);

        $userRepository = $this->getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findOneBy(['username' => "ExampleUser"]);
        $this->assertNotNull($user);

        $this->assertMatchesRegularExpression('/\/api\/users\/*/', $response->toArray()['@id']);
    }

    public function testCreateInvalidUser(): void
    {
        $response = $this->createClientWithAdminCredentials()->request('POST', '/api/users', ['json' => [
            "email"=> "Example%example.com",
            "roles"=> ["None"],
            "password" => 1,
            "username" => "ExampleUser",
            "height" => 0,
            "weight" => 0,
            "birthDate" => "2"
        ]]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Error',
            '@type' => 'hydra:Error',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'The type of the "password" attribute must be "string", "integer" given.'
        ]);
    }

    public function testRegisterUser(): void
    {
        $client = static::createClient();
        $response = $client->request('POST', '/api/users/register', ['json' => [
            "username" => "TestRegisterUser",
            "email"=> "TestRegisterUser@example.com",
            "password" => "ExamplePass123"
        ]]);

        $this->assertResponseStatusCodeSame(204);
        $this->assertJsonContains([
            "@context" => "/api/contexts/User",
            "@type" => "Users",
            "email"=> "Example@example.com",
            "roles"=> ["ROLE_USER"],
            "password" => "ExamplePass123",
            "username" => "ExampleUser",
            "height" => '180',
            "weight" => 80,
            "birthDate" => "2021-12-15T11:08:25.769Z"
        ]);
        $userRepository = $this->getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findOneBy(['username' => "TestRegisterUser"]);
        $this->assertNotNull($user);
        $this->assertMatchesRegularExpression('/\/api\/users\/*/', $response->toArray()['@id']);
    }

    public function testRegisterInvalidUser(): void
    {
        $client = static::createClient();
        $response = $client->request('POST', '/api/users/register', ['json' => [
            "username" => 123,
            "email"=> "TestRegisterInvalidUser%example.com",
            "password" => "1"
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
        ]);
    }

    public function testActivateUserWithWrongToken(): void
    {
        $response = $this->createClientWithAdminCredentials()->request('POST', '/api/users/activate', ['json' => [
            "token" => '123123123123123123'
        ]]);
        
        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'token: The token is invalid.',
        ]);
    }

    public function testUpdateUser(): void
    {
        $client = $this->createClientWithAdminCredentials();

        $iri = $this->findIriBy(User::class, ['username' => 'ojohns']);

        $client->request('PUT', $iri, ['json' => [
            "email"=> "Example@example.com",
            "roles"=> ["ROLE_USER"],
            "password" => "ExamplePass123",
            "username" => "ojohnsTest",
            "height" => 180,
            "weight" => 80,
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            "email"=> "Example@example.com",
            "roles"=> ["ROLE_USER"],
            "password" => "ExamplePass123",
            "username" => "ojohnsTest",
            "height" => '180',
            "weight" => 80,
        ]);
    }

    public function testUpdateUserWithoutAuthority(): void
    {
        $client = $this->createClientWithUserCredentials();

        $iri = $this->findIriBy(User::class, ['username' => 'celia68']);

        $client->request('PUT', $iri, ['json' => [
            "email"=> "Example@example.com",
            "roles"=> ["ROLE_USER"],
            "password" => "ExamplePass123",
            "username" => "ojohnsTest",
            "height" => 180,
            "weight" => 80,
            "birthDate" => "2021-12-15T11:08:25.769Z"
        ]]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testWrongUpdateUser(): void
    {
        $client = $this->createClientWithAdminCredentials();

        $iri = $this->findIriBy(User::class, ['username' => 'mmedhurst']);

        $client->request('PUT', $iri, ['json' => [
            "email"=> "Example%example.com",
            "height" => "182",
            "weight" => "75",
        ]]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testDeleteUserByAdmin(): void
    {
        $client = $this->createClientWithAdminCredentials();
        $userRepository = $this->getContainer()->get('doctrine')->getRepository(User::class);
        $iri = $this->findIriBy(User::class, ['username' => 'gbarton']);

        $client->request('DELETE', $iri);
        $this->assertResponseIsSuccessful();
        $user = $userRepository->findOneBy(['username' => 'gbarton']);
        $this->assertNull($user);
    }

    public function testDeleteUserWithoutAuthority(): void
    {
        $client = $this->createClientWithUserCredentials();
        $userRepository = $this->getContainer()->get('doctrine')->getRepository(User::class);
        $iri = $this->findIriBy(User::class, ['username' => 'obins']);

        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(403);
        $user = $userRepository->findOneBy(['username' => 'obins']);
        $this->assertNotNull($user);
    }
}