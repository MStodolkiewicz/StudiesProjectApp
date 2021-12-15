<?php
#tests/IngredientTest.php

namespace App\Tests;

use App\Tests\CustomApiTestCase;
use App\Entity\Book;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Entity\Ingredient;
use App\Entity\Product;

class IngredientTest extends AbstractTest
{
    // This trait provided by AliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    public function testGetIngredientCollection(): void
    {
        $response = $this->createClientWithUserCredentials()->request('GET', '/api/ingredients');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => "/api/contexts/Ingredient",
            '@id' => "/api/ingredients",
            '@type' => 'hydra:Collection',
            "hydra:totalItems" => 50,
            'hydra:view' => [
                "@id" => "/api/ingredients?page=1",
                "@type" => "hydra:PartialCollectionView",
                "hydra:first" => "/api/ingredients?page=1",
                "hydra:last" => "/api/ingredients?page=2",
                "hydra:next" => "/api/ingredients?page=2",
            ],
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);
    }

    public function testCreateIngredient(): void //YourRate
    {
        $iri = $this->findIriBy(Product::class, ['name' => 'Sit in.']);

        $response = $this->createClientWithUserCredentials()->request('POST', '/api/ingredients', ['json' => [
            "name"=> "Egg",
            "product"=> $iri
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "@context" => "/api/contexts/Ingredient",
            "@type" => "Ingredient",
            "name" => "Egg",
        ]);
        $this->assertMatchesRegularExpression('/\/api\/ingredients\/*/', $response->toArray()['@id']);
    }

    public function testCreateInvalidIngredient(): void //YourRate
    {
        $iri = $this->findIriBy(Product::class, ['name' => 'Sit in.']);
        $response = $this->createClientWithUserCredentials()->request('POST', '/api/ingredients', ['json' => [
            "name" => "311",
            "product" => $iri
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context" => "/api/contexts/ConstraintViolationList",
            "@type" => "ConstraintViolationList"
        ]);
    }

    public function testCreateValidIngredientWithInvalidProduct(): void
    {
        $response = $this->createClientWithUserCredentials()->request('POST', '/api/ingredients', ['json' => [
            "name" => "Egg",
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

    public function testUpdateIngredient(): void
    {
        $client = $this->createClientWithAdminCredentials();

        $iri = $this->findIriBy(Ingredient::class, ['name' => 'Possimus.']);

        $client->request('PUT', $iri, ['json' => [
            'name' => 'Egg'
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'Egg',
        ]);
    }

    public function testUpdateIngredientWithoutAuthority(): void //ValidatorNotFinished
    {
        $client = $this->createClientWithUserCredentials();

        $iri = $this->findIriBy(Ingredient::class, ['name' => 'Velit dolorem.']);

        $client->request('PUT', $iri, ['json' => [
            'name' => 'Egg'
        ]]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteIngredientByAdmin(): void
    {
        $client = $this->createClientWithAdminCredentials();
        $ingredientRepository = $this->getContainer()->get('doctrine')->getRepository(Ingredient::class);
        $iri = $this->findIriBy(Ingredient::class, ['name' => 'Ea quo.']);

        $client->request('DELETE', $iri);
        $this->assertResponseIsSuccessful();
        $ingredient = $ingredientRepository->findOneBy(['name' => 'Ea quo.']);
        $this->assertNull($ingredient);
    }

    public function testDeleteIngredientByNormalUser(): void //ValidatoNotFinished
    {
        $client = $this->createClientWithUserCredentials();
        $ingredientRepository = $this->getContainer()->get('doctrine')->getRepository(Ingredient::class);
        $iri = $this->findIriBy(Ingredient::class, ['name' => 'Nostrum dolore.']);

        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(403);
        $ingredient = $ingredientRepository->findOneBy(['name' => 'Nostrum dolore.']);
        $this->assertNotNull($ingredient);
    }
}