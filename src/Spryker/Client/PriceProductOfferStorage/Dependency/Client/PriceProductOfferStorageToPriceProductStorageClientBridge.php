<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\PriceProductOfferStorage\Dependency\Client;

use Generated\Shared\Transfer\CurrentProductPriceTransfer;
use Generated\Shared\Transfer\PriceProductFilterTransfer;

class PriceProductOfferStorageToPriceProductStorageClientBridge implements PriceProductOfferStorageToPriceProductStorageClientInterface
{
    /**
     * @var \Spryker\Client\PriceProductStorage\PriceProductStorageClientInterface
     */
    protected $priceProductStorageClient;

    /**
     * @param \Spryker\Client\PriceProductStorage\PriceProductStorageClientInterface $priceProductStorageClient
     */
    public function __construct($priceProductStorageClient)
    {
        $this->priceProductStorageClient = $priceProductStorageClient;
    }

    public function getResolvedCurrentProductPriceTransfer(PriceProductFilterTransfer $priceProductFilterTransfer): CurrentProductPriceTransfer
    {
        return $this->priceProductStorageClient->getResolvedCurrentProductPriceTransfer($priceProductFilterTransfer);
    }
}
