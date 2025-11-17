<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\PriceProductOfferStorage\Storage;

interface PriceProductOfferStorageReaderInterface
{
    /**
     * @param int $idProductConcrete
     * @param string|null $storeName
     *
     * @return array<\Generated\Shared\Transfer\PriceProductTransfer>
     */
    public function getProductOfferPrices(int $idProductConcrete, ?string $storeName = null): array;
}
