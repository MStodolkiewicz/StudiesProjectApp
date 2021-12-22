<?php

namespace App\DataProvider;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use ApiPlatform\Core\JsonLd\Serializer\ItemNormalizer;
use App\Entity\Product;
use App\Entity\Rate;
use App\Repository\ProductRepository;
use ApiPlatform\Core\DataProvider\DenormalizedIdentifiersAwareItemDataProviderInterface;
use App\Repository\RateRepository;
use Symfony\Component\Security\Core\Security;

class ProductDataProvider
{

    private $itemDataProvider;

    private $productRepository;

    private $collectionDataProvider;

    private $security;

    private $rateRepository;

    private $iriConverter;

    public function __construct(ItemDataProviderInterface $itemDataProvider, ProductRepository $productRepository, CollectionDataProviderInterface $collectionDataProvider,RateRepository $rateRepository, Security $security,IriConverterInterface $iriConverter)
    {

        $this->itemDataProvider = $itemDataProvider;
        $this->productRepository = $productRepository;
        $this->collectionDataProvider = $collectionDataProvider;
        $this->security = $security;
        $this->rateRepository = $rateRepository;
        $this->iriConverter = $iriConverter;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass == Product::class;
    }
/*
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?Product
    {

        /** @var Product $product *//*
        $product = $this->itemDataProvider->getItem($resourceClass, $id, $operationName, $context);

        if (!$product) {
            return null;
        }

        $product->setAvarageRate($this->productRepository->getAverageRate($product->getId()));
        /** @var Rate $yourRate *//*
        $yourRate
            ? $product->setYourRate([
                '@id'=> $this->iriConverter->getIriFromItem($yourRate),
                '@type' => (new \ReflectionClass($yourRate))->getShortName(),
                'value'=> $yourRate->getValue()
                ])
            : [];

        return $product;
    }

    public function getCollection(string $resourceClass, string $operationName = null)
    {
        /** @var Product[] $products *//*
        $products = $this->collectionDataProvider->getCollection($resourceClass, $operationName);

        if(!$products){
            return [];
        }
        //in my understanding code above creates first query and fetches all records of products
        //next in code below there are sub-queries that populate every objects avarageRate field.
        //is that a proper practice ? dunno atm...
        //maybe it is optimized on a query execution level by doctrine... but also not 100% sure about it
        foreach ($products as $product){
            /** @var Rate $yourRate *//*
            $yourRate = $this->rateRepository->getUsersRateForProduct($this->security->getUser()->getId(), $product->getId());
            $yourRate
                ? $product->setYourRate(['@id'=> $this->iriConverter->getIriFromItem($yourRate), '@type' => (new \ReflectionClass($yourRate))->getShortName(), 'value'=> $yourRate->getValue()])
                : $product->setYourRate(null);
            $product->setAvarageRate($this->productRepository->getAverageRate($product->getId()));
        }

        return $products;


    }
    */
}