<?php
#tests/IntakeTest.php

namespace App\Tests;

use App\Tests\CustomApiTestCase;
use App\Entity\Book;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Entity\Ingredient;
use App\Entity\Intake;
use App\Entity\Product;

class IntakeTest extends AbstractTest
{
    use RefreshDatabaseTrait;

    public function testGetIntakeCollection(): void
    {
        $response = $this->createClientWithUserCredentials()->request('GET', '/api/intakes');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => "/api/contexts/Intake",
            '@id' => "/api/intakes",
            '@type' => 'hydra:Collection',
            "hydra:totalItems" => 50,
            'hydra:view' => [
                "@id" => "/api/intakes?page=1",
                "@type" => "hydra:PartialCollectionView",
                "hydra:first" => "/api/intakes?page=1",
                "hydra:last" => "/api/intakes?page=10",
                "hydra:next" => "/api/intakes?page=2",
            ],
        ]);

        $this->assertCount(5, $response->toArray()['hydra:member']);
    }

    public function testCreateIntake(): void //YourRate
    {
        $iri = $this->findIriBy(Product::class, ['name' => 'Sit in.']);

        $response = $this->createClientWithUserCredentials()->request('POST', '/api/intakes', ['json' => [
            "amountInGrams"=> "100",
            "mealType"=> "Meat",
            "product"=> $iri
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "@context" => "/api/contexts/Intake",
            "@type" => "Intakes",
            "amountInGrams"=> "100",
            "mealType"=> "Meat",
        ]);
        $this->assertMatchesRegularExpression('/\/api\/intakes\/*/', $response->toArray()['@id']);
    }

    public function testCreateInvalidIntake(): void //YourRate
    {
        $iri = $this->findIriBy(Product::class, ['name' => 'Sit in.']);
        $response = $this->createClientWithUserCredentials()->request('POST', '/api/intakes', ['json' => [
            "amountInGrams"=> "Alaska",
            "mealType"=> "Meat",
            "product"=> $iri
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context" => "/api/contexts/ConstraintViolationList",
            "@type" => "ConstraintViolationList"
        ]);
    }

    public function testCreateValidIntakeWithInvalidProduct(): void
    {
        $response = $this->createClientWithUserCredentials()->request('POST', '/api/intakes', ['json' => [
            "amountInGrams"=> "100",
            "mealType"=> "Meat",
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

    public function testUpdateIntake(): void
    {
        $client = $this->createClientWithAdminCredentials();

        $iri = $this->findIriBy(Intake::class, ['mealType' => 'excepturi']);

        $client->request('PUT', $iri, ['json' => [
            "amountInGrams"=> "100",
            "mealType"=> "Meat",
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            "amountInGrams"=> "100",
            "mealType"=> "Meat",
        ]);
    }

    public function testUpdateIntakeWithoutAuthority(): void
    {
        $client = $this->createClientWithUserCredentials();

        $iri = $this->findIriBy(Intake::class, ['mealType' => 'excepturi']);

        $client->request('PUT', $iri, ['json' => [
            "amountInGrams"=> "100",
            "mealType"=> "Meat",
        ]]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteIntakeByAdmin(): void
    {
        $client = $this->createClientWithAdminCredentials();
        $productRepository = $this->getContainer()->get('doctrine')->getRepository(Intake::class);
        $iri = $this->findIriBy(Intake::class, ['mealType' => 'numquam']);

        $client->request('DELETE', $iri);
        $this->assertResponseIsSuccessful();
        $product = $productRepository->findOneBy(['mealType' => 'numquam']);
        $this->assertNull($product);
    }

    public function testDeleteIntakeByNormalUser(): void
    {
        $client = $this->createClientWithUserCredentials();
        $productRepository = $this->getContainer()->get('doctrine')->getRepository(Intake::class);
        $iri = $this->findIriBy(Intake::class, ['mealType' => 'consequatur']);

        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(403);
        $product = $productRepository->findOneBy(['mealType' => 'consequatur']);
        $this->assertNotNull($product);
    }
}