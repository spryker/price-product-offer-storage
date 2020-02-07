<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductBundle\Business\ProductBundle\Availability\PreCheck;

use ArrayObject;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\CartPreCheckResponseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\DecimalObject\Decimal;

class ProductBundleCartAvailabilityCheck extends BasePreCheck implements ProductBundleCartAvailabilityCheckInterface
{
    public const CART_PRE_CHECK_ITEM_AVAILABILITY_FAILED = 'cart.pre.check.availability.failed';
    public const CART_PRE_CHECK_ITEM_AVAILABILITY_EMPTY = 'cart.pre.check.availability.failed.empty';
    public const STOCK_TRANSLATION_PARAMETER = '%stock%';
    public const SKU_TRANSLATION_PARAMETER = '%sku%';

    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartPreCheckResponseTransfer
     */
    public function checkCartAvailability(CartChangeTransfer $cartChangeTransfer)
    {
        $cartPreCheckFailedItems = new ArrayObject();
        $itemsInCart = clone $cartChangeTransfer->getQuote()->getItems();

        $storeTransfer = $cartChangeTransfer->getQuote()->getStore();
        $storeTransfer->requireName();

        $storeTransfer = $this->storeFacade->getStoreByName($storeTransfer->getName());
        foreach ($cartChangeTransfer->getItems() as $itemTransfer) {
            $itemTransfer->requireSku()->requireQuantity();

            if (!$this->isProductBundle($itemTransfer)) {
                continue;
            }

            $messageTransfers = $this->checkItemAvailability($itemsInCart, $itemTransfer, $storeTransfer);
            $itemsInCart->append($itemTransfer);

            foreach ($messageTransfers as $messageTransfer) {
                $cartPreCheckFailedItems[] = $messageTransfer;
            }
        }

        return $this->createCartPreCheckResponseTransfer($cartPreCheckFailedItems);
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     * @param string $sku
     *
     * @return \Spryker\DecimalObject\Decimal
     */
    protected function getAccumulatedItemQuantityForBundledProductsByGivenSku(ArrayObject $itemTransfers, string $sku): Decimal
    {
        $quantity = new Decimal(0);
        foreach ($itemTransfers as $itemTransfer) {
            if (!$itemTransfer->getRelatedBundleItemIdentifier()) {
                continue;
            }

            if ($itemTransfer->getSku() !== $sku) {
                continue;
            }

            $quantity = $quantity->add($itemTransfer->getQuantity());
        }

        return $quantity;
    }

    /**
     * @param \Spryker\DecimalObject\Decimal $stock
     * @param string $sku
     *
     * @return \Generated\Shared\Transfer\MessageTransfer
     */
    protected function createItemIsNotAvailableMessageTransfer(Decimal $stock, string $sku): MessageTransfer
    {
        $translationKey = $this->getItemAvailabilityTranslationKey($stock);

        return $this->createCartMessageTransfer($stock, $translationKey, $sku);
    }

    /**
     * @param \Spryker\DecimalObject\Decimal $stock
     * @param string $translationKey
     * @param string $sku
     *
     * @return \Generated\Shared\Transfer\MessageTransfer
     */
    protected function createCartMessageTransfer(Decimal $stock, string $translationKey, string $sku): MessageTransfer
    {
        $messageTransfer = new MessageTransfer();
        $messageTransfer->setValue($translationKey);
        $messageTransfer->setParameters([
            static::SKU_TRANSLATION_PARAMETER => $sku,
            static::STOCK_TRANSLATION_PARAMETER => $stock->trim()->toString(),
        ]);

        return $messageTransfer;
    }

    /**
     * @param \Spryker\DecimalObject\Decimal $stock
     *
     * @return string
     */
    protected function getItemAvailabilityTranslationKey(Decimal $stock): string
    {
        $translationKey = static::CART_PRE_CHECK_ITEM_AVAILABILITY_FAILED;
        if ($stock->lessThanOrEquals(0)) {
            $translationKey = static::CART_PRE_CHECK_ITEM_AVAILABILITY_EMPTY;
        }

        return $translationKey;
    }

    /**
     * @param string $sku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer|null
     */
    protected function findAvailabilityBySku(string $sku, StoreTransfer $storeTransfer): ?ProductConcreteAvailabilityTransfer
    {
        return $this->availabilityFacade
            ->findOrCreateProductConcreteAvailabilityBySkuForStore($sku, $storeTransfer);
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\MessageTransfer[] $messages
     *
     * @return \Generated\Shared\Transfer\CartPreCheckResponseTransfer
     */
    protected function createCartPreCheckResponseTransfer(ArrayObject $messages)
    {
        return (new CartPreCheckResponseTransfer())
            ->setIsSuccess(count($messages) == 0)
            ->setMessages($messages);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $itemsInCart
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Spryker\DecimalObject\Decimal
     */
    protected function calculateRegularItemAvailability(
        ItemTransfer $itemTransfer,
        ArrayObject $itemsInCart,
        StoreTransfer $storeTransfer
    ): Decimal {
        $productConcreteAvailabilityTransfer = $this->availabilityFacade
            ->findOrCreateProductConcreteAvailabilityBySkuForStore(
                $itemTransfer->getSku(),
                $storeTransfer
            );

        if ($productConcreteAvailabilityTransfer === null || $productConcreteAvailabilityTransfer->getAvailability() === null) {
            return new Decimal(0);
        }

        $bundledItemsQuantity = $this->getAccumulatedItemQuantityForBundledProductsByGivenSku(
            $itemsInCart,
            $itemTransfer->getSku()
        );

        $availabilityAfterBundling = $productConcreteAvailabilityTransfer->getAvailability()
            ->subtract($bundledItemsQuantity);

        return $availabilityAfterBundling;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $itemsInCart
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\MessageTransfer[]
     */
    protected function checkItemAvailability(
        ArrayObject $itemsInCart,
        ItemTransfer $itemTransfer,
        StoreTransfer $storeTransfer
    ) {
        $bundledProducts = $this->findBundledProducts($itemTransfer->getSku());
        $bundledProductsMessages = [];

        if (count($bundledProducts) > 0) {
            return $this->getUnavailableBundleItemsInCart($itemsInCart, $bundledProducts, $itemTransfer, $storeTransfer);
        }

        $regularItemAvailability = $this->checkRegularItemAvailability($itemsInCart, $itemTransfer, $storeTransfer);
        if ($regularItemAvailability) {
            $bundledProductsMessages[] = $regularItemAvailability;
        }

        return $bundledProductsMessages;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $itemsInCart
     * @param \Orm\Zed\ProductBundle\Persistence\SpyProductBundle[] $bundledProducts
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\MessageTransfer[]
     */
    protected function getUnavailableBundleItemsInCart(
        ArrayObject $itemsInCart,
        array $bundledProducts,
        ItemTransfer $itemTransfer,
        StoreTransfer $storeTransfer
    ) {
        $unavailableBundleItems = $this->getUnavailableBundleItems($itemsInCart, $bundledProducts, $itemTransfer, $storeTransfer);

        $availabilityTransfer = $this->findAvailabilityBySku($itemTransfer->getSku(), $storeTransfer);

        if (
            $availabilityTransfer && $this->isUserRequestedMoreItemsThanInStock($itemTransfer, $availabilityTransfer)
            && $this->isAllBundleItemsUnavailable($unavailableBundleItems, $bundledProducts)
        ) {
            $bundleAvailabilityErrorMessage = $this->createItemIsNotAvailableMessageTransfer(
                $availabilityTransfer->getAvailability(),
                $itemTransfer->getSku()
            );

            return [
                $bundleAvailabilityErrorMessage,
            ];
        }

        return $this->createMessageTransfersForUnavailableBundleItems($unavailableBundleItems);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer $availabilityTransfer
     *
     * @return bool
     */
    protected function isUserRequestedMoreItemsThanInStock(ItemTransfer $itemTransfer, ProductConcreteAvailabilityTransfer $availabilityTransfer): bool
    {
        return $availabilityTransfer->getAvailability()->lessThan($itemTransfer->getQuantity());
    }

    /**
     * @param array $unavailableBundleItems
     * @param \Orm\Zed\ProductBundle\Persistence\SpyProductBundle[] $bundledProducts
     *
     * @return bool
     */
    protected function isAllBundleItemsUnavailable(array $unavailableBundleItems, array $bundledProducts): bool
    {
        return count($unavailableBundleItems) === count($bundledProducts);
    }

    /**
     * @param array $unavailableBundleItems
     *
     * @return \Generated\Shared\Transfer\MessageTransfer[]
     */
    protected function createMessageTransfersForUnavailableBundleItems(array $unavailableBundleItems): array
    {
        $unavailableBundleItemsMessages = [];

        foreach ($unavailableBundleItems as $unavailableBundleItem) {
            $unavailableBundleItemsMessages[] = (new MessageTransfer())
                ->setValue(static::ERROR_BUNDLE_ITEM_UNAVAILABLE_TRANSLATION_KEY)
                ->setParameters($unavailableBundleItem);
        }

        return $unavailableBundleItemsMessages;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $itemsInCart
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\MessageTransfer|null
     */
    protected function checkRegularItemAvailability(ArrayObject $itemsInCart, ItemTransfer $itemTransfer, StoreTransfer $storeTransfer): ?MessageTransfer
    {
        if ($this->checkIfItemIsSellable($itemsInCart, $itemTransfer->getSku(), $storeTransfer, new Decimal($itemTransfer->getQuantity()))) {
            return null;
        }

        $availability = $this->calculateRegularItemAvailability($itemTransfer, $itemsInCart, $storeTransfer);

        return $this->createItemIsNotAvailableMessageTransfer($availability, $itemTransfer->getSku());
    }
}
