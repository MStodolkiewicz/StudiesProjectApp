<?php
#tests/RateTest.php

namespace App\Tests;

use App\Tests\CustomApiTestCase;
use App\Entity\Book;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Entity\Product;
use App\Entity\Rate;

class RateTest extends AbstractTest
{
    use RefreshDatabaseTrait;

    public function testGetRateCollection(): void
    {
        $response = $this->createClientWithAdminCredentials()->request('GET', '/api/rates');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => "/api/contexts/Rate",
            '@id' => "/api/rates",
            '@type' => 'hydra:Collection',
            "hydra:totalItems" => 50,
            'hydra:view' => [
                "@id" => "/api/rates?page=1",
                "@type" => "hydra:PartialCollectionView",
                "hydra:first" => "/api/rates?page=1",
                "hydra:last" => "/api/rates?page=10",
                "hydra:next" => "/api/rates?page=2",
            ],
        ]);

        $this->assertCount(5, $response->toArray()['hydra:member']);
    }

    public function testCreateRate(): void 
    {
        $iri = $this->findIriBy(Product::class, ['name' => 'Sit in.']);

        $response = $this->createClientWithUserCredentials()->request('POST', '/api/rates', ['json' => [
            "value"=> 4,
            "product"=> $iri
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "@context" => "/api/contexts/Rate",
            "@type" => "Rate",
            "value"=> 4,
        ]);
        $this->assertMatchesRegularExpression('/\/api\/rates\/*/', $response->toArray()['@id']);
    }

    public function testCreateInvalidRate(): void
    {
        $iri = $this->findIriBy(Product::class, ['name' => 'Sit in.']);
        $response = $this->createClientWithUserCredentials()->request('POST', '/api/rates', ['json' => [
            "value"=> 'abc',
            "product"=> $iri
        ]]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Error',
            '@type' => 'hydra:Error',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'The type of the "value" attribute must be "int", "string" given.'
        ]);
    }

    public function testCreateValidRateWithInvalidProduct(): void
    {
        $response = $this->createClientWithUserCredentials()->request('POST', '/api/rates', ['json' => [
            "value"=> 0,
            "product" => "/api/products/Invalid"
        ]]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context" => "/api/contexts/Error",
            "@type" => "hydra:Error",
            "hydra:title" => "An error occurred",
            "hydra:description" => 'Invalid IRI "/api/products/Invalid".'
        ]);
    }

    public function testUpdateRate(): void
    {
        $client = $this->createClientWithAdminCredentials();

        $iri = $this->findIriBy(Rate::class, ['value' => 5]);

        $client->request('PUT', $iri, ['json' => [
            'value'=> 4,
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'value'=> 4,
        ]);
    }

    public function testWrongUpdateRate(): void
    {
        $client = $this->createClientWithAdminCredentials();

        $iri = $this->findIriBy(Rate::class, ['value' => 5]);

        $client->request('PUT', $iri, ['json' => [
            'value'=> 6,
        ]]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testUpdateRateWithoutAuthority(): void
    {
        $client = $this->createClientWithUserCredentials();

        $iri = $this->findIriBy(Rate::class, ['value' => 5]);

        $client->request('PUT', $iri, ['json' => [
            'value'=> 4,
        ]]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteRateByAdmin(): void
    {
        $client = $this->createClientWithAdminCredentials();
        $iri = $this->findIriBy(Rate::class, ['value' => 5]);

        $client->request('DELETE', $iri);
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteRateByNormalUser(): void
    {
        $client = $this->createClientWithUserCredentials();
        $iri = $this->findIriBy(Rate::class, ['value' => 5]);

        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(403);
    }
}