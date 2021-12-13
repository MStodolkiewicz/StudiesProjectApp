<?php
#tests/CategoryTest.php

namespace App\Tests;

use App\Tests\CustomApiTestCase;
use App\Entity\Book;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Entity\Category;

class CategoryTest extends AbstractTest
{
    // This trait provided by AliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

//    public function testGetCategories(): void
//    {
//        $response = $this->createClientWithCredentials()->request('GET', '/api/categories');
//        $this->assertResponseIsSuccessful();
//    }

    public function testGetCategoriesCollection(): void
    {
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = $this->createClientWithAdminCredentials()->request('GET', '/api/categories');

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => "/api/contexts/Category",
            '@id' => "/api/categories",
            '@type' => 'hydra:Collection',
            "hydra:totalItems" => 51,
            'hydra:view' => [
                "@id" => "/api/categories?page=1",
                "@type" => "hydra:PartialCollectionView",
                "hydra:first" => "/api/categories?page=1",
                "hydra:last" => "/api/categories?page=11",
                "hydra:next" => "/api/categories?page=2",
            ],
        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        $this->assertCount(5, $response->toArray()['hydra:member']);

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
//        $this->assertMatchesResourceCollectionJsonSchema(Category::class);
    }

    public function testCreateCategory(): void
    {
        $response = $this->createClientWithAdminCredentials()->request('POST', '/api/categories', ['json' => [
            'name' => 'Fruits'
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "@context" => "/api/contexts/Category",
            "@type" => "Category",
            "name" => "Fruits",
            "subCategories" => []
        ]);
        $this->assertMatchesRegularExpression('/\/api\/categories\/*/', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Category::class);
    }

    public function testCreateInvalidCategory(): void
    {
        $response = $this->createClientWithAdminCredentials()->request('POST', '/api/categories', ['json' => [
            'name' => 123
        ]]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context" => "/api/contexts/Error",
            "@type" => "hydra:Error",
            "hydra:title" => "An error occurred",
            "hydra:description" => "The type of the \"name\" attribute must be \"string\", \"integer\" given.",
        ]);
    }

    public function testCreateValidCategoryWithInvalidSubcategory(): void
    {
        $response = $this->createClientWithAdminCredentials()->request('POST', '/api/categories', ['json' => [
            'name' => "Owocki",
            'subCategories' => [
                (object)["name" => 123],
            ]
        ]]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context" => "/api/contexts/Error",
            "@type" => "hydra:Error",
            "hydra:title" => "An error occurred",
            "hydra:description" => "The type of the \"name\" attribute must be \"string\", \"integer\" given.",
        ]);
    }

    public function testUpdateCategory(): void
    {
        $client = $this->createClientWithAdminCredentials();

        $iri = $this->findIriBy(Category::class, ['name' => 'Mushrooms']);

        $client->request('PUT', $iri, ['json' => [
            'name' => 'Vegetables'
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'Vegetables',
        ]);
    }

    public function testDeleteCategoryAssignedToProduct(): void
    {
        $client = $this->createClientWithAdminCredentials();
        $categoryRepository = $this->getContainer()->get('doctrine')->getRepository(Category::class);
        $iri = $this->findIriBy(Category::class, ['name' => 'Mushrooms']);

        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(500);
        $category = $categoryRepository->findOneBy(['name' => 'Mushrooms']);
        $this->assertNotNull($category);
    }

    public function testEditCategoryByNormalUser(): void
    {
        $client = $this->createClientWithUserCredentials();
        $categoryRepository = $this->getContainer()->get('doctrine')->getRepository(Category::class);
        $iri = $this->findIriBy(Category::class, ['name' => 'Mushrooms']);

        $client->request('PUT', $iri, [
            'json' => [
                "name" => "TuttiFruit",
            ]
        ]);

        $this->assertResponseStatusCodeSame(403);
        $category = $categoryRepository->findOneBy(['name' => 'Mushrooms']);
        $this->assertNotNull($category);

        $this->assertJsonContains([
            "@context" => "/api/contexts/Error",
            "@type" => "hydra:Error",
            "hydra:title" => "An error occurred",
            "hydra:description" => "You are not allowed to change this resource!",
        ]);
    }
}