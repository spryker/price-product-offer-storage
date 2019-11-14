<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ShipmentCartConnector\Business;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\CartChangeBuilder;
use Generated\Shared\DataBuilder\ExpenseBuilder;
use Generated\Shared\DataBuilder\ItemBuilder;
use Generated\Shared\DataBuilder\QuoteBuilder;
use Generated\Shared\DataBuilder\ShipmentBuilder;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Shared\ShipmentCartConnector\ShipmentCartConnectorConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ShipmentCartConnector
 * @group Business
 * @group Facade
 * @group ShipmentCartConnectorFacadeTest
 * Add your own group annotations below this line
 */
class ShipmentCartConnectorFacadeTest extends Unit
{
    public const SKU = 'sku';
    public const CURRENCY_ISO_CODE = 'USD';
    public const DEFAULT_PRICE_LIST = [
        'DE' => [
            'EUR' => [],
        ],
    ];

    /**
     * @var \SprykerTest\Zed\ShipmentCartConnector\ShipmentCartConnectorBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testUpdateShipmentPriceShouldUpdatePriceBasedOnCurrency(): void
    {
        $shipmentCartConnectorFacade = $this->tester->getFacade();
        $storeTransfer = $this->tester->haveStore([
            StoreTransfer::NAME => 'DE',
        ]);

        $shipmentMethodTransfer = $this->tester->haveShipmentMethod([], [], static::DEFAULT_PRICE_LIST, [$storeTransfer->getIdStore()]);

        $shipmentMethodTransfer->setCurrencyIsoCode(static::CURRENCY_ISO_CODE);
        $shipmentMethodTransfer->setStoreCurrencyPrice(-1);

        $cartChangeTransfer = $this->createCartCartChangeTransfer($shipmentMethodTransfer, $storeTransfer);

        $updatedCartChangeTransfer = $shipmentCartConnectorFacade->updateShipmentPrice($cartChangeTransfer);

        $quoteTransfer = $updatedCartChangeTransfer->getQuote();

        $this->assertSame($quoteTransfer->getShipment()->getMethod()->getCurrencyIsoCode(), $quoteTransfer->getCurrency()->getCode());

        $price = $quoteTransfer->getShipment()->getMethod()->getStoreCurrencyPrice();
        $this->assertNotEmpty($price);
        $this->assertNotEquals(-1, $price);
    }

    /**
     * @return void
     */
    public function testUpdateShipmentPriceShouldUpdatePriceBasedOnCurrencyWithItemLevelShipments()
    {
        $shipmentCartConnectorFacade = $this->tester->getFacade();
        $storeTransfer = $this->tester->haveStore([
            StoreTransfer::NAME => 'DE',
        ]);

        $shipmentMethodTransfer = $this->tester->haveShipmentMethod([], [], static::DEFAULT_PRICE_LIST, [$storeTransfer->getIdStore()]);

        $shipmentMethodTransfer->setCurrencyIsoCode(static::CURRENCY_ISO_CODE);
        $shipmentMethodTransfer->setStoreCurrencyPrice(-1);

        $cartChangeTransfer = $this->createCartChangeTransferWithItemLevelShipments($shipmentMethodTransfer, $storeTransfer);

        $updatedCartChangeTransfer = $shipmentCartConnectorFacade->updateShipmentPrice($cartChangeTransfer);

        $quoteTransfer = $updatedCartChangeTransfer->getQuote();
        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $this->assertSame(
                $itemTransfer->getShipment()->getMethod()->getCurrencyIsoCode(),
                $quoteTransfer->getCurrency()->getCode()
            );

            $price = $itemTransfer->getShipment()->getMethod()->getStoreCurrencyPrice();
            $this->assertNotEmpty($price);
            $this->assertNotEquals(-1, $price);
        }
    }

    /**
     * @return void
     */
    public function testValidateShipmentShouldReturnFalseWhenSelectedShipmentHaveNoPrice()
    {
        $shipmentCartConnectorFacade = $this->tester->getFacade();
        $storeTransfer = $this->tester->haveStore([
            StoreTransfer::NAME => 'DE',
        ]);

        $shipmentMethodTransfer = $this->tester->haveShipmentMethod([], [], static::DEFAULT_PRICE_LIST, [$storeTransfer->getIdStore()]);

        $cartChangeTransfer = $this->createCartCartChangeTransfer($shipmentMethodTransfer, $storeTransfer);

        $cartChangeTransfer->getQuote()->getCurrency()->setCode('LTL');

        $cartPreCheckResponseTransfer = $shipmentCartConnectorFacade->validateShipment($cartChangeTransfer);

        $this->assertFalse($cartPreCheckResponseTransfer->getIsSuccess());
        $this->assertCount(1, $cartPreCheckResponseTransfer->getMessages());
    }

    /**
     * @return void
     */
    public function testValidateShipmentShouldReturnFalseWhenSelectedShipmentHaveNoPriceWithItemLevelShipments()
    {
        $shipmentCartConnectorFacade = $this->tester->getFacade();
        $storeTransfer = $this->tester->haveStore([
            StoreTransfer::NAME => 'DE',
        ]);

        $shipmentMethodTransfer = $this->tester->haveShipmentMethod([], [], static::DEFAULT_PRICE_LIST, [$storeTransfer->getIdStore()]);

        $cartChangeTransfer = $this->createCartChangeTransferWithItemLevelShipments($shipmentMethodTransfer, $storeTransfer);

        $cartChangeTransfer->getQuote()->getCurrency()->setCode('LTL');

        $cartPreCheckResponseTransfer = $shipmentCartConnectorFacade->validateShipment($cartChangeTransfer);

        $this->assertFalse($cartPreCheckResponseTransfer->getIsSuccess());
        $this->assertCount(1, $cartPreCheckResponseTransfer->getMessages());
    }

    /**
     * @return void
     */
    public function testValidateShipmentShouldReturnTrueWhenSelectedShipmentHavePrice()
    {
        $shipmentCartConnectorFacade = $this->tester->getFacade();
        $storeTransfer = $this->tester->haveStore([
            StoreTransfer::NAME => 'DE',
        ]);

        $shipmentMethodTransfer = $this->tester->haveShipmentMethod([], [], static::DEFAULT_PRICE_LIST, [$storeTransfer->getIdStore()]);

        $cartChangeTransfer = $this->createCartCartChangeTransfer($shipmentMethodTransfer, $storeTransfer);

        $cartPreCheckResponseTransfer = $shipmentCartConnectorFacade->validateShipment($cartChangeTransfer);

        $this->assertTrue($cartPreCheckResponseTransfer->getIsSuccess());
        $this->assertCount(0, $cartPreCheckResponseTransfer->getMessages());
    }

    /**
     * @return void
     */
    public function testValidateShipmentShouldReturnTrueWhenSelectedShipmentHavePriceWithItemLevelShipments()
    {
        $shipmentCartConnectorFacade = $this->tester->getFacade();
        $storeTransfer = $this->tester->haveStore([
            StoreTransfer::NAME => 'DE',
        ]);

        $shipmentMethodTransfer = $this->tester->haveShipmentMethod([], [], static::DEFAULT_PRICE_LIST, [$storeTransfer->getIdStore()]);

        $cartChangeTransfer = $this->createCartChangeTransferWithItemLevelShipments($shipmentMethodTransfer, $storeTransfer);

        $cartPreCheckResponseTransfer = $shipmentCartConnectorFacade->validateShipment($cartChangeTransfer);

        $this->assertTrue($cartPreCheckResponseTransfer->getIsSuccess());
        $this->assertCount(0, $cartPreCheckResponseTransfer->getMessages());
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentMethodTransfer $shipmentMethodTransfer
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    protected function createCartCartChangeTransfer(ShipmentMethodTransfer $shipmentMethodTransfer, StoreTransfer $storeTransfer): CartChangeTransfer
    {
        $cartChangeTransfer = (new CartChangeBuilder())->build();

        $quoteTransfer = (new QuoteBuilder())
            ->withStore($storeTransfer->toArray())
            ->withCurrency()
            ->withExpense()
            ->build();

        $shipmentTransfer = (new ShipmentBuilder())->build();

        $shipmentTransfer->setMethod($shipmentMethodTransfer);

        $quoteTransfer->setShipment($shipmentTransfer);
        $quoteTransfer = $this->removeItemLevelShipments($quoteTransfer);

        $cartChangeTransfer->setQuote($quoteTransfer);

        return $cartChangeTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function removeItemLevelShipments(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $itemTransfer->setShipment(null);
        }

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentMethodTransfer $shipmentMethodTransfer
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    protected function createCartChangeTransferWithItemLevelShipments(ShipmentMethodTransfer $shipmentMethodTransfer, StoreTransfer $storeTransfer): CartChangeTransfer
    {
        $cartChangeTransfer = (new CartChangeBuilder())->build();

        $quoteTransfer = (new QuoteBuilder())
            ->withStore($storeTransfer->toArray())
            ->withCurrency()
            ->build();

        $shipmentTransfer = (new ShipmentBuilder())->build();
        $shipmentTransfer->setMethod($shipmentMethodTransfer);

        $shipmentExpense = (new ExpenseBuilder())->build();
        $shipmentExpense->setType(ShipmentCartConnectorConfig::SHIPMENT_EXPENSE_TYPE);
        $shipmentExpense->setShipment($shipmentTransfer);

        $itemTransfer = (new ItemBuilder())->build();
        $itemTransfer->setSku(static::SKU);
        $itemTransfer->setGroupKey(static::SKU);
        $itemTransfer->setShipment($shipmentTransfer);

        $quoteTransfer->addItem($itemTransfer);
        $quoteTransfer->addExpense($shipmentExpense);

        $cartChangeTransfer->setQuote($quoteTransfer);

        return $cartChangeTransfer;
    }
}