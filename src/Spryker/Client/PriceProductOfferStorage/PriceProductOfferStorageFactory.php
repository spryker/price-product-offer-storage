<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\PriceProductOfferStorage;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\PriceProductOfferStorage\Dependency\Client\PriceProductOfferStorageToPriceProductStorageClientInterface;
use Spryker\Client\PriceProductOfferStorage\Dependency\Client\PriceProductOfferStorageToStorageClientInterface;
use Spryker\Client\PriceProductOfferStorage\Dependency\Client\PriceProductOfferStorageToStoreClientInterface;
use Spryker\Client\PriceProductOfferStorage\Dependency\Service\PriceProductOfferStorageToPriceProductServiceInterface;
use Spryker\Client\PriceProductOfferStorage\Dependency\Service\PriceProductOfferStorageToSynchronizationServiceInterface;
use Spryker\Client\PriceProductOfferStorage\Mapper\PriceProductOfferStorageMapper;
use Spryker\Client\PriceProductOfferStorage\Mapper\PriceProductOfferStorageMapperInterface;
use Spryker\Client\PriceProductOfferStorage\Sorter\PriceProductOfferStorageSorter;
use Spryker\Client\PriceProductOfferStorage\Sorter\PriceProductOfferStorageSorterInterface;
use Spryker\Client\PriceProductOfferStorage\Storage\PriceProductOfferStorageReader;
use Spryker\Client\PriceProductOfferStorage\Storage\PriceProductOfferStorageReaderInterface;

class PriceProductOfferStorageFactory extends AbstractFactory
{
    public function createPriceProductOfferStorageReader(): PriceProductOfferStorageReaderInterface
    {
        return new PriceProductOfferStorageReader(
            $this->createPriceProductMapper(),
            $this->getSynchronizationService(),
            $this->getStorageClient(),
            $this->getStoreClient(),
            $this->getPriceProductService(),
            $this->getPriceProductOfferStoragePriceExtractorPlugins(),
        );
    }

    public function createPriceProductOfferStorageSorter(): PriceProductOfferStorageSorterInterface
    {
        return new PriceProductOfferStorageSorter();
    }

    public function createPriceProductMapper(): PriceProductOfferStorageMapperInterface
    {
        return new PriceProductOfferStorageMapper();
    }

    public function getSynchronizationService(): PriceProductOfferStorageToSynchronizationServiceInterface
    {
        return $this->getProvidedDependency(PriceProductOfferStorageDependencyProvider::SERVICE_SYNCHRONIZATION);
    }

    public function getStorageClient(): PriceProductOfferStorageToStorageClientInterface
    {
        return $this->getProvidedDependency(PriceProductOfferStorageDependencyProvider::CLIENT_STORAGE);
    }

    public function getStoreClient(): PriceProductOfferStorageToStoreClientInterface
    {
        return $this->getProvidedDependency(PriceProductOfferStorageDependencyProvider::FACADE_STORE_CLIENT);
    }

    public function getPriceProductService(): PriceProductOfferStorageToPriceProductServiceInterface
    {
        return $this->getProvidedDependency(PriceProductOfferStorageDependencyProvider::FACADE_PRICE_PRODUCT_SERVICE);
    }

    /**
     * @return array<\Spryker\Client\PriceProductOfferStorageExtension\Dependency\Plugin\PriceProductOfferStoragePriceExtractorPluginInterface>
     */
    protected function getPriceProductOfferStoragePriceExtractorPlugins(): array
    {
        return $this->getProvidedDependency(PriceProductOfferStorageDependencyProvider::PLUGINS_PRICE_PRODUCT_OFFER_STORAGE_PRICE_EXTRACTOR);
    }

    public function getPriceProductStorageClient(): PriceProductOfferStorageToPriceProductStorageClientInterface
    {
        return $this->getProvidedDependency(PriceProductOfferStorageDependencyProvider::CLIENT_PRICE_PRODUCT_STORAGE);
    }
}
