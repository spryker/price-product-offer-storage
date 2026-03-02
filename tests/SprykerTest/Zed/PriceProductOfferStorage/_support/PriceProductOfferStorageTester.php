<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\PriceProductOfferStorage;

use Codeception\Actor;
use Orm\Zed\PriceProductOfferStorage\Persistence\SpyProductConcreteProductOfferPriceStorageQuery;

/**
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class PriceProductOfferStorageTester extends Actor
{
    use _generated\PriceProductOfferStorageTesterActions;

    public function getCountPriceProductOfferStorageCount(int $productId): int
    {
        return $this->createProductConcreteProductOfferPriceStorageQuery()->findByFkProduct($productId)->count();
    }

    public function deletePriceProductOfferStorageEntities(): void
    {
        $productConcreteProductOfferPriceStorageQuery = $this->createProductConcreteProductOfferPriceStorageQuery();

        $this->truncateTableRelations($productConcreteProductOfferPriceStorageQuery);
    }

    protected function createProductConcreteProductOfferPriceStorageQuery(): SpyProductConcreteProductOfferPriceStorageQuery
    {
        return SpyProductConcreteProductOfferPriceStorageQuery::create();
    }
}
