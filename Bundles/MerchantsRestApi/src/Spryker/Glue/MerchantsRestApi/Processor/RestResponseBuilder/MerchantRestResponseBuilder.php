<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\MerchantsRestApi\Processor\RestResponseBuilder;

use Generated\Shared\Transfer\MerchantStorageTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Generated\Shared\Transfer\RestMerchantsAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\MerchantsRestApi\MerchantsRestApiConfig;
use Spryker\Glue\MerchantsRestApi\Processor\Mapper\MerchantMapperInterface;
use Symfony\Component\HttpFoundation\Response;

class MerchantRestResponseBuilder implements MerchantRestResponseBuilderInterface
{
    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilder;

    /**
     * @var \Spryker\Glue\MerchantsRestApi\Processor\Mapper\MerchantMapperInterface
     */
    protected $merchantMapper;

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     * @param \Spryker\Glue\MerchantsRestApi\Processor\Mapper\MerchantMapperInterface $merchantMapper
     */
    public function __construct(
        RestResourceBuilderInterface $restResourceBuilder,
        MerchantMapperInterface $merchantMapper
    ) {
        $this->restResourceBuilder = $restResourceBuilder;
        $this->merchantMapper = $merchantMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantStorageTransfer $merchantStorageTransfer
     * @param string $localeName
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function createMerchantsRestResponse(
        MerchantStorageTransfer $merchantStorageTransfer,
        string $localeName
    ): RestResponseInterface {
        $merchantsRestResource = $this->createMerchantsRestResource($merchantStorageTransfer, $localeName);

        return $this->restResourceBuilder
            ->createRestResponse()
            ->addResource($merchantsRestResource);
    }

    /**
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function createMerchantNotFoundErrorResponse(): RestResponseInterface
    {
        return $this->restResourceBuilder
            ->createRestResponse()
            ->addError(
                (new RestErrorMessageTransfer())
                    ->setStatus(Response::HTTP_NOT_FOUND)
                    ->setCode(MerchantsRestApiConfig::RESPONSE_CODE_MERCHANT_NOT_FOUND)
                    ->setDetail(MerchantsRestApiConfig::RESPONSE_DETAIL_MERCHANT_NOT_FOUND)
            );
    }

    /**
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function createMerchantIdentifierMissingErrorResponse(): RestResponseInterface
    {
        return $this->restResourceBuilder
            ->createRestResponse()
            ->addError(
                (new RestErrorMessageTransfer())
                    ->setStatus(Response::HTTP_BAD_REQUEST)
                    ->setCode(MerchantsRestApiConfig::RESPONSE_CODE_MERCHANT_IDENTIFIER_MISSING)
                    ->setDetail(MerchantsRestApiConfig::RESPONSE_DETAIL_MERCHANT_IDENTIFIER_MISSING)
            );
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantStorageTransfer $merchantStorageTransfer
     * @param string $localeName
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface
     */
    protected function createMerchantsRestResource(
        MerchantStorageTransfer $merchantStorageTransfer,
        string $localeName
    ): RestResourceInterface {
        $restMerchantsAttributesTransfer = $this->merchantMapper->mapMerchantStorageTransferToRestMerchantAttributesTransfer(
            $merchantStorageTransfer,
            new RestMerchantsAttributesTransfer(),
            $localeName
        );

        return $this->restResourceBuilder->createRestResource(
            MerchantsRestApiConfig::RESOURCE_MERCHANTS,
            $merchantStorageTransfer->getMerchantReference(),
            $restMerchantsAttributesTransfer
        );
    }
}
