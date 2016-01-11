<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Product\Business;

use Spryker\Zed\Product\Business\Attribute\AttributeManager;
use Spryker\Zed\Product\Business\Model\ProductBatchResult;
use Spryker\Zed\Product\Business\Importer\Writer\Db\ConcreteProductWriter;
use Spryker\Zed\Product\Business\Importer\Writer\Db\AbstractProductWriter;
use Spryker\Zed\Product\Business\Importer\Writer\ProductWriter;
use Spryker\Zed\Product\Business\Importer\Builder\ProductBuilder;
use Spryker\Zed\Product\Business\Importer\Reader\File\CsvReader;
use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Product\Business\Attribute\AttributeManagerInterface;
use Spryker\Zed\Product\Business\Builder\ProductBuilderInterface;
use Spryker\Zed\Product\Business\Importer\FileImporter;
use Spryker\Zed\Product\Business\Importer\Reader\File\IteratorReaderInterface;
use Spryker\Zed\Product\Business\Importer\Upload\UploadedFileImporter;
use Spryker\Zed\Product\Business\Importer\Validator\ImportProductValidator;
use Spryker\Zed\Product\Business\Importer\Writer\AbstractProductWriterInterface;
use Spryker\Zed\Product\Business\Importer\Writer\ConcreteProductWriterInterface;
use Spryker\Zed\Product\Business\Importer\Writer\ProductWriterInterface;
use Spryker\Zed\Product\Business\Internal\Install;
use Spryker\Zed\Product\Business\Model\ProductBatchResultInterface;
use Spryker\Zed\Product\Business\Product\ProductManagerInterface;
use Spryker\Zed\Product\Dependency\Facade\ProductToLocaleInterface;
use Spryker\Zed\Product\Dependency\Facade\ProductToTouchInterface;
use Spryker\Zed\Product\Dependency\Facade\ProductToUrlInterface;
use Spryker\Zed\Product\Persistence\ProductQueryContainerInterface;
use Spryker\Zed\Messenger\Business\Model\MessengerInterface;
use Spryker\Zed\Product\ProductConfig;
use Spryker\Zed\Product\ProductDependencyProvider;
use Spryker\Zed\Product\Business\Product\ProductManager;

/**
 * @method ProductConfig getConfig()
 * @method ProductQueryContainerInterface getQueryContainer()
 */
class ProductBusinessFactory extends AbstractBusinessFactory
{

    /**
     * @var ProductManager
     */
    protected $productManager;

    /**
     * @return UploadedFileImporter
     */
    public function createHttpFileImporter()
    {
        return new UploadedFileImporter(
            $this->getConfig()->getDestinationDirectoryForUploads()
        );
    }

    /**
     * @return string
     */
    public function createYvesUrl()
    {
        return $this->getConfig()->getHostYves();
    }

    /**
     * @return FileImporter
     */
    public function createProductImporter()
    {
        $importer = new FileImporter(
            $this->createImportProductValidator(),
            $this->createCSVReader(),
            $this->createImportProductBuilder(),
            $this->createProductWriter(),
            $this->createProductBatchResult()
        );

        return $importer;
    }

    /**
     * @return ImportProductValidator
     */
    protected function createImportProductValidator()
    {
        return new ImportProductValidator();
    }

    /**
     * @return IteratorReaderInterface
     */
    protected function createCSVReader()
    {
        return new CsvReader();
    }

    /**
     * @return ProductBuilderInterface
     */
    protected function createImportProductBuilder()
    {
        return new ProductBuilder();
    }

    /**
     * @return ProductWriterInterface
     */
    protected function createProductWriter()
    {
        return new ProductWriter(
            $this->createAbstractProductWriter(),
            $this->createConcreteProductWriter()
        );
    }

    /**
     * @return AbstractProductWriterInterface
     */
    protected function createAbstractProductWriter()
    {
        return new AbstractProductWriter(
            $this->getCurrentLocale()
        );
    }

    /**
     * @return ConcreteProductWriterInterface
     */
    protected function createConcreteProductWriter()
    {
        return new ConcreteProductWriter(
            $this->getCurrentLocale()
        );
    }

    /**
     * @return ProductBatchResultInterface
     */
    protected function createProductBatchResult()
    {
        return new ProductBatchResult();
    }

    /**
     * @param MessengerInterface $messenger
     *
     * @return Install
     */
    public function createInstaller(MessengerInterface $messenger)
    {
        $installer = new Install(
            $this->createAttributeManager()
        );
        $installer->setMessenger($messenger);

        return $installer;
    }

    /**
     * @return AttributeManagerInterface
     */
    public function createAttributeManager()
    {
        return new AttributeManager(
            $this->getQueryContainer()
        );
    }

    /**
     * @return ProductManagerInterface
     */
    public function createProductManager()
    {
        if ($this->productManager === null) {
            $this->productManager = new ProductManager(
                $this->getQueryContainer(),
                $this->getTouchFacade(),
                $this->getUrlFacade(),
                $this->getLocaleFacade()
            );
        }

        return $this->productManager;
    }

    /**
     * @return ProductToLocaleInterface
     */
    protected function getLocaleFacade()
    {
        return $this->getProvidedDependency(ProductDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return ProductToTouchInterface
     */
    protected function getTouchFacade()
    {
        return $this->getProvidedDependency(ProductDependencyProvider::FACADE_TOUCH);
    }

    /**
     * @return ProductToUrlInterface
     */
    protected function getUrlFacade()
    {
        return $this->getProvidedDependency(ProductDependencyProvider::FACADE_URL);
    }

    /**
     * @return LocaleTransfer
     */
    protected function getCurrentLocale()
    {
        return $this->getLocaleFacade()->getCurrentLocale();
    }

}
