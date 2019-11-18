<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductOfferStorage\Communication\Plugin\Event\Listener;

use Orm\Zed\PriceProductOffer\Persistence\Map\SpyPriceProductOfferTableMap;
use Spryker\Zed\Event\Dependency\Plugin\EventBulkHandlerInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\PriceProductOfferStorage\PriceProductOfferStorageConfig getConfig()
 * @method \Spryker\Zed\PriceProductOfferStorage\Business\PriceProductOfferStorageFacadeInterface getFacade()
 * @method \Spryker\Zed\PriceProductOfferStorage\Communication\PriceProductOfferStorageCommunicationFactory getFactory()
 */
class PriceProductOfferStorageUnpublishListener extends AbstractPlugin implements EventBulkHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\EventEntityTransfer[] $transfers
     * @param string $eventName
     *
     * @return void
     */
    public function handleBulk(array $transfers, $eventName): void
    {
        $priceProductOfferIds = $this->getFactory()->getEventBehaviorFacade()->getEventTransferIds($transfers);
        $productOfferIds = $this->getFactory()->getEventBehaviorFacade()->getEventTransferForeignKeys($transfers, SpyPriceProductOfferTableMap::COL_FK_PRODUCT_OFFER);

        $this->getFacade()->unpublish(array_combine($priceProductOfferIds, $productOfferIds));
    }
}
