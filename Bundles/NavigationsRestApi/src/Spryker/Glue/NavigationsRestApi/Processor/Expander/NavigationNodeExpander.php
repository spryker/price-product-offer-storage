<?php

namespace Spryker\Glue\NavigationsRestApi\Processor\Expander;

use ArrayObject;
use Generated\Shared\Transfer\RestNavigationAttributesTransfer;
use Generated\Shared\Transfer\UrlStorageTransfer;
use Spryker\Glue\NavigationsRestApi\Dependency\Client\NavigationsRestApiToUrlStorageClientInterface;
use Spryker\Glue\NavigationsRestApi\NavigationsRestApiConfig;

class NavigationNodeExpander implements NavigationNodeExpanderInterface
{
    /**
     * @var \Spryker\Glue\NavigationsRestApi\Dependency\Client\NavigationsRestApiToUrlStorageClientInterface
     */
    protected $urlStorageClient;

    /**
     * @var \Spryker\Glue\NavigationsRestApi\NavigationsRestApiConfig
     */
    protected $config;

    /**
     * @param \Spryker\Glue\NavigationsRestApi\Dependency\Client\NavigationsRestApiToUrlStorageClientInterface $urlStorageClient
     * @param \Spryker\Glue\NavigationsRestApi\NavigationsRestApiConfig $config
     */
    public function __construct(
        NavigationsRestApiToUrlStorageClientInterface $urlStorageClient,
        NavigationsRestApiConfig $config
    ) {
        $this->urlStorageClient = $urlStorageClient;
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\RestNavigationAttributesTransfer $restNavigationAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestNavigationAttributesTransfer
     */
    public function expand(RestNavigationAttributesTransfer $restNavigationAttributesTransfer): RestNavigationAttributesTransfer
    {
        $nodes = $this->expandNavigationNodeTransfers($restNavigationAttributesTransfer->getNodes());
        $restNavigationAttributesTransfer->setNodes($nodes);

        return $restNavigationAttributesTransfer;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\RestNavigationNodeTransfer[] $restNavigationNodeTransfers
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\RestNavigationNodeTransfer[]
     */
    protected function expandNavigationNodeTransfers(ArrayObject $restNavigationNodeTransfers): ArrayObject
    {
        $urlCollection = array_filter($this->getUrlsCollection($restNavigationNodeTransfers));
        $urlStorageTransfers = $this->urlStorageClient->findUrlStorageTransferByUrls($urlCollection);

        return $this->mapUrlStorageTransfersToRestNavigationNodeTransfers(
            $urlStorageTransfers,
            $restNavigationNodeTransfers
        );
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\RestNavigationNodeTransfer[] $restNavigationNodeTransfers
     * @param string[] $urlCollection
     *
     * @return string[]
     */
    protected function getUrlsCollection(ArrayObject $restNavigationNodeTransfers, array $urlCollection = []): array
    {
        foreach ($restNavigationNodeTransfers as $restNavigationNodeTransfer) {
            $urlCollection[] = $restNavigationNodeTransfer->getUrl();

            if ($restNavigationNodeTransfer->getChildren()->count() > 0) {
                $urlCollection += $this->getUrlsCollection($restNavigationNodeTransfer->getChildren(), $urlCollection);
            }
        }

        return $urlCollection;
    }

    /**
     * @param \Generated\Shared\Transfer\UrlStorageTransfer[] $urlStorageTransfers
     * @param \ArrayObject|\Generated\Shared\Transfer\RestNavigationNodeTransfer[] $restNavigationNodeTransfers
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\RestNavigationNodeTransfer[]
     */
    protected function mapUrlStorageTransfersToRestNavigationNodeTransfers(
        array $urlStorageTransfers,
        ArrayObject $restNavigationNodeTransfers
    ): ArrayObject {
        foreach ($restNavigationNodeTransfers as $restNavigationNodeTransfer) {
            if (array_key_exists($restNavigationNodeTransfer->getUrl(), $urlStorageTransfers)) {
                $restNavigationNodeTransfer->setResourceId(
                    $this->findResourceIdByNodeType($urlStorageTransfers[$restNavigationNodeTransfer->getUrl()], $restNavigationNodeTransfer->getNodeType())
                );
            }

            if ($restNavigationNodeTransfer->getChildren()->count() > 0) {
                $navigationNodeChildren = $this->mapUrlStorageTransfersToRestNavigationNodeTransfers($urlStorageTransfers, $restNavigationNodeTransfer->getChildren());
                $restNavigationNodeTransfer->setChildren($navigationNodeChildren);
            }
        }

        return $restNavigationNodeTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\UrlStorageTransfer $urlStorageTransfer
     * @param string $nodeType
     *
     * @return int|null
     */
    protected function findResourceIdByNodeType(UrlStorageTransfer $urlStorageTransfer, string $nodeType): ?int
    {
        if (!isset($this->config->getNavigationTypeToUrlResourceIdFieldMapping()[$nodeType])) {
            return null;
        }

        return $urlStorageTransfer[$this->config->getNavigationTypeToUrlResourceIdFieldMapping()[$nodeType]]
            ?? null;
    }
}
