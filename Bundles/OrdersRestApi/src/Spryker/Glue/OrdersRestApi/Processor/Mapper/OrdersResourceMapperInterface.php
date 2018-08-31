<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\OrdersRestApi\Processor\Mapper;

use Generated\Shared\Transfer\OrderRestAttributesTransfer;
use Generated\Shared\Transfer\OrdersRestAttributesTransfer;
use Generated\Shared\Transfer\OrderTransfer;

interface OrdersResourceMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer[] $items
     * @param \Generated\Shared\Transfer\OrdersRestAttributesTransfer $ordersRestAttributes
     *
     * @return void
     */
    public function mapOrderListToOrdersRestAttribute(OrderTransfer $orderTransfer, array $items, OrdersRestAttributesTransfer $ordersRestAttributes): void;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer[] $items
     *
     * @return \Generated\Shared\Transfer\OrderRestAttributesTransfer
     */
    public function mapOrderToOrdersRestAttribute(OrderTransfer $orderTransfer, array $items): OrderRestAttributesTransfer;
}
