<?php
#tests/ProductTest.php

namespace App\Tests;

use App\Tests\CustomApiTestCase;
use App\Entity\Book;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Entity\Category;
use App\Entity\Product;

class ProductTest extends AbstractTest
{
    // This trait provided by AliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

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
        $iri = $this->findIriBy(Category::class, ['name' => 'Mushrooms']);

        $response = $this->createClientWithAdminCredentials()->request('POST', '/api/products', ['json' => [
            "barCodeNumbers" => "123321123",
            "name"=> "Batonik",
            "brand"=> "Gregory",
            "proteins"=> "22.2",
            "carbohydrates"=> "10.1",
            "fat"=> "9.4",
            "kcal"=> "213.8",
            "category"=> $iri
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "@context" => "/api/contexts/Product",
            "@type" => "Product",
            "name" => "Batonik",
            "createdAtAgo" => "1 second ago"
        ]);
        $this->assertMatchesRegularExpression('/\/api\/products\/*/', $response->toArray()['@id']);
    }

    public function testCreateInvalidProduct(): void
    {
        $response = $this->createClientWithUserCredentials()->request('POST', '/api/products', ['json' => [
            "barCodeNumbers" => "123321123",
            "name" => "Batonik",
            "brand" => "Gregory",
            "proteins" => "22.2",
            "carbohydrates"=> "10.1",
            "fat"=> "9.4",
            "kcal"=> "113.8",
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context" => "/api/contexts/ConstraintViolationList",
            "@type" => "ConstraintViolationList"
        ]);
    }

    public function testCreateValidProductWithInvalidCategory(): void
    {
        $response = $this->createClientWithUserCredentials()->request('POST', '/api/products', ['json' => [
            "barCodeNumbers" => "123321123",
            "name" => "Batonik",
            "brand" => "Gregory",
            "proteins" => "22.2",
            "carbohydrates"=> "10.1",
            "fat"=> "9.4",
            "kcal"=> "113.8",
            "category"=> "/api/categories/invalid", 
        ]]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context" => "/api/contexts/Error",
            "@type" => "hydra:Error",
            "hydra:title" => "An error occurred",
            "hydra:description" => 'Invalid IRI "/api/categories/invalid".'
        ]);
    }

    public function testUpdateProduct(): void //YourRate
    {
        $client = $this->createClientWithAdminCredentials();

        $iri = $this->findIriBy(Product::class, ['name' => 'Aut dolores perspiciatis.']);

        $client->request('PUT', $iri, ['json' => [
            'name' => 'Aut dolores perspiciatises.'
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'Aut dolores perspiciatises.',
        ]);
    }

    public function testUpdateProductwithoutAuthority(): void //YourRate
    {
        $client = $this->createClientWithUserCredentials();

        $iri = $this->findIriBy(Product::class, ['name' => 'Sed aut quia.']);

        $client->request('PUT', $iri, ['json' => [
            'name' => 'Aut dolores perspiciatises.'
        ]]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteProductByAdmin(): void //YourRate
    {
        $client = $this->createClientWithAdminCredentials();
        $productRepository = $this->getContainer()->get('doctrine')->getRepository(Product::class);
        $iri = $this->findIriBy(Product::class, ['name' => 'Vero animi.']);

        $client->request('DELETE', $iri);
        $this->assertResponseIsSuccessful();
        $product = $productRepository->findOneBy(['name' => 'Vero animi.']);
        $this->assertNull($product);
    }

    public function testDeleteProductByNormalUser(): void //YourRate
    {
        $client = $this->createClientWithUserCredentials();
        $productRepository = $this->getContainer()->get('doctrine')->getRepository(Product::class);
        $iri = $this->findIriBy(Product::class, ['name' => 'Sit in.']);

        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(403);
        $product = $productRepository->findOneBy(['name' => 'Sit in.']);
        $this->assertNotNull($product);
    }
}