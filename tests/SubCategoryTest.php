<?php
#tests/SubCategoryTest.php

namespace App\Tests;

use App\Tests\CustomApiTestCase;
use App\Entity\Book;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Entity\SubCategory;
use App\Entity\Category;

class SubCategoryTest extends AbstractTest
{
    // This trait provided by AliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

//    public function testGetCategories(): void
//    {
//        $response = $this->createClientWithCredentials()->request('GET', '/api/categories');
//        $this->assertResponseIsSuccessful();
//    }

    public function testGetSubCategoriesCollection(): void
    {
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = $this->createClientWithAdminCredentials()->request('GET', '/api/sub_categories');

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => "/api/contexts/SubCategory",
            '@id' => "/api/sub_categories",
            '@type' => 'hydra:Collection',
            "hydra:totalItems" => 50,
            'hydra:view' => [
                "@id" => "/api/sub_categories?page=1",
                "@type" => "hydra:PartialCollectionView",
                "hydra:first" => "/api/sub_categories?page=1",
                "hydra:last" => "/api/sub_categories?page=10",
                "hydra:next" => "/api/sub_categories?page=2",
            ],
        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        $this->assertCount(5, $response->toArray()['hydra:member']);

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
//        $this->assertMatchesResourceCollectionJsonSchema(Category::class);
    }

    public function testCreateSubCategory(): void
    {
        $iri = $this->findIriBy(Category::class, ['name' => 'Mushrooms']);

        $response = $this->createClientWithAdminCredentials()->request('POST', '/api/sub_categories', ['json' => [
            'name' => 'Cheese',
            'category' => $iri
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "@context" => "/api/contexts/SubCategory",
            "@type" => "SubCategory",
            "name" => "Cheese"
        ]);
        $this->assertMatchesRegularExpression('/\/api\/sub_categories\/*/', $response->toArray()['@id']);
    }

    public function testCreateInvalidSubCategory(): void
    {
        $iri = $this->findIriBy(Category::class, ['name' => 'Mushrooms']);

        $response = $this->createClientWithAdminCredentials()->request('POST', '/api/sub_categories', ['json' => [
            'name' => 123,
            'category' => $iri
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

    public function testCreateValidSubCategoryWithInvalidcategory(): void
    {
        $response = $this->createClientWithAdminCredentials()->request('POST', '/api/sub_categories', ['json' => [
            'name' => 'Cheese',
            'category' => [
                (object)["name" => 123],
            ]
        ]]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context" => "/api/contexts/Error",
            "@type" => "hydra:Error",
            "hydra:title" => "An error occurred",
            "hydra:description" => 'Nested documents for attribute "category" are not allowed. Use IRIs instead.',
        ]);
    }

    public function testUpdateSubCategory(): void
    {
        $client = $this->createClientWithAdminCredentials();

        $iri = $this->findIriBy(SubCategory::class, ['name' => 'Vel eos.']);

        $client->request('PUT', $iri, ['json' => [
            'name' => 'Vegetables'
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'Vegetables',
        ]);
    }

    public function testDeleteSubCategoryAssignedToProduct(): void
    {
        $client = $this->createClientWithAdminCredentials();
        $sub_categoryRepository = $this->getContainer()->get('doctrine')->getRepository(SubCategory::class);
        $iri = $this->findIriBy(SubCategory::class, ['name' => 'Culpa dolore.']);

        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(500);
        $sub_category = $sub_categoryRepository->findOneBy(['name' => 'Culpa dolore.']);
        $this->assertNotNull($sub_category);
    }

    public function testEditSubCategoryByNormalUser(): void
    {
        $client = $this->createClientWithUserCredentials();
        $categoryRepository = $this->getContainer()->get('doctrine')->getRepository(SubCategory::class);
        $iri = $this->findIriBy(SubCategory::class, ['name' => 'Vel eos.']);

        $client->request('PUT', $iri, [
            'json' => [
                "name" => "Candies",
            ]
        ]);

        $this->assertResponseStatusCodeSame(403);
        $category = $categoryRepository->findOneBy(['name' => 'Vel eos.']);
        $this->assertNotNull($category);

        $this->assertJsonContains([
            "@context" => "/api/contexts/Error",
            "@type" => "hydra:Error",
            "hydra:title" => "An error occurred",
            "hydra:description" => "You are not allowed to change this resource!",
        ]);
    }
}