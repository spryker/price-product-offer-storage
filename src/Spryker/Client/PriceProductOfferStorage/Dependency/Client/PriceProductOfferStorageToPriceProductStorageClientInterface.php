<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\PriceProductOfferStorage\Dependency\Client;

use Generated\Shared\Transfer\CurrentProductPriceTransfer;
use Generated\Shared\Transfer\PriceProductFilterTransfer;

interface PriceProductOfferStorageToPriceProductStorageClientInterface
{
    public function getResolvedCurrentProductPriceTransfer(PriceProductFilterTransfer $priceProductFilterTransfer): CurrentProductPriceTransfer;
}
