<?php
#tests/CategoryTest.php

namespace App\Tests;

use App\Tests\CustomApiTestCase;
use App\Entity\Book;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Entity\Category;

class ProductTest extends AbstractTest
{
    // This trait provided by AliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

//    public function testGetCategories(): void
//    {
//        $response = $this->createClientWithCredentials()->request('GET', '/api/categories');
//        $this->assertResponseIsSuccessful();
//    }

    public function testGetProductsCollection(): void
    {
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = $this->createClientWithUserCredentials()->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => "/api/contexts/Product",
            '@id' => "/api/products",
            '@type' => 'hydra:Collection',
            "hydra:totalItems" => 27,
            'hydra:view' => [
                "@id" => "/api/products?page=1",
                "@type" => "hydra:PartialCollectionView",
                "hydra:first" => "/api/products?page=1",
                "hydra:last" => "/api/products?page=6",
                "hydra:next" => "/api/products?page=2",
            ],
        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        $this->assertCount(5, $response->toArray()['hydra:member']);
    }

    public function testCreateProduct(): void
    {

        $response = $this->createClientWithUserCredentials()->request('POST', '/api/products', ['json' => [
            "barCodeNumbers" => "123321123",
            "name"=> "Batonik",
            "brand"=> "Gregory",
            "proteins"=> "22.2",
            "carbohydrates"=> "10.1",
            "fat"=> "9.4",
            "kcal"=> "213.8",
            "category"=> "/api/categories/11c2d8d4-3911-4bef-8f6a-ffeac99c1cab", 
            "subCategory"=> "/api/sub_categories/f5dc315e-4f8e-4aca-a631-674bb9d45b03" 
        ]]);

        $this->assertResponseStatusCodeSame(201); //Test database records change every time tests are executed. Right now it's gonna be error 400 every time.
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "@context" => "/api/contexts/Category",
            "@type" => "Category",
            "name" => "Fruits",
            "subCategories" => [],
            "createdAtAgo" => "1 second ago"
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