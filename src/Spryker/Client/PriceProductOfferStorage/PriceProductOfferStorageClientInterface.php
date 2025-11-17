<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\PriceProductOfferStorage;

interface PriceProductOfferStorageClientInterface
{
    /**
     * Specification:
     * - Gets product offer prices from storage by idProductConcrete.
     * - If storeName is provided, the prices will be filtered by the provided store.
     * - If storeName is not provided, the prices will be returned for the current store.
     *
     * @api
     *
     * @param int $idProductConcrete
     * @param string|null $storeName
     *
     * @return array<\Generated\Shared\Transfer\PriceProductTransfer>
     */
    public function getProductOfferPrices(int $idProductConcrete, ?string $storeName = null): array;
}
