<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOfferGuiPage\Communication\Table\ProductListTable;

use Generated\Shared\Transfer\TableConfigurationTransfer;
use Generated\Shared\Transfer\TableDataTransfer;
use Spryker\Zed\ProductOfferGuiPage\Exception\InvalidPaginationDataException;
use Spryker\Zed\ProductOfferGuiPage\Exception\InvalidSortingDataException;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractTable
{
    protected const RESULT_KEY_DATA = 'data';
    protected const RESULT_KEY_PAGINATION = 'pagination';

    protected const CONFIG_COLUMNS = 'columns';
    protected const CONFIG_AVAILABLE_PAGE_SIZES = 'availablePageSizes';
    protected const CONFIG_FILTERS = 'filters';

    protected const PAGINATION_KEY_CURRENT_PAGE = 'currentPage';
    protected const PAGINATION_KEY_LAST_PAGE = 'lastPage';
    protected const PAGINATION_KEY_PAGE_SIZE = 'pageSize';
    protected const PAGINATION_KEY_TOTAL_COUNT = 'totalCount';
    protected const PAGINATION_KEY_PREVIOUS_PAGE = 'previousPage';
    protected const PAGINATION_KEY_NEXT_PAGE = 'nextPage';
    protected const PAGINATION_KEY_FIRST_PAGE = 'firstPage';

    protected const PARAM_PAGE = 'page';
    protected const PARAM_PAGE_SIZE = 'pageSize';
    protected const PARAM_ORDER_BY = 'orderBy';
    protected const PARAM_SEARCH_TERM = 'search';
    protected const PARAM_FILTERS = 'filters';

    protected const DEFAULT_PAGE = 1;
    protected const DEFAULT_PAGE_SIZE = 10;
    protected const DEFAULT_AVAILABLE_PAGE_SIZES = [10, 25, 50];
    protected const DEFAULT_ORDER_DIRECTION = 'ASC';

    /**
     * @var string
     */
    protected $searchTerm;

    /**
     * @var array
     */
    protected $sorting;

    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $pageSize;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Spryker\Zed\ProductOfferGuiPage\Exception\InvalidPaginationDataException
     *
     * @return array
     */
    public function getData(Request $request): array
    {
        $this->initialize($request);

        $tableDataTransfer = $this->provideTableData();
        $paginationTransfer = $tableDataTransfer->getPagination();

        if (!$paginationTransfer) {
            throw new InvalidPaginationDataException('Pagination data is not present.');
        }

        return [
            static::RESULT_KEY_DATA => $tableDataTransfer->getRows(),
            static::RESULT_KEY_PAGINATION => [
                static::PAGINATION_KEY_CURRENT_PAGE => $paginationTransfer->getPage(),
                static::PAGINATION_KEY_LAST_PAGE => $paginationTransfer->getLastPage(),
                static::PAGINATION_KEY_PAGE_SIZE => $paginationTransfer->getMaxPerPage(),
                static::PAGINATION_KEY_TOTAL_COUNT => $paginationTransfer->getNbResults(),
                static::PAGINATION_KEY_PREVIOUS_PAGE => $paginationTransfer->getPreviousPage(),
                static::PAGINATION_KEY_NEXT_PAGE => $paginationTransfer->getNextPage(),
                static::PAGINATION_KEY_FIRST_PAGE => $paginationTransfer->getFirstPage(),
            ],
        ];
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        $tableConfigurationTransfer = $this->provideTableConfiguration();

        return [
            static::CONFIG_COLUMNS => $this->prepareColumnsConfigurationData($tableConfigurationTransfer),
            static::CONFIG_AVAILABLE_PAGE_SIZES => $this->prepareAvailablePageSizesConfigurationData($tableConfigurationTransfer),
            static::CONFIG_FILTERS => $this->prepareFiltersConfigurationData($tableConfigurationTransfer),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\TableConfigurationTransfer $tableConfigurationTransfer
     *
     * @return array
     */
    protected function prepareColumnsConfigurationData(TableConfigurationTransfer $tableConfigurationTransfer): array
    {
        $columnsData = [];

        foreach ($tableConfigurationTransfer->getColumns() as $columnTransfer) {
            $columnsData[] = $columnTransfer->toArray();
        }

        return $columnsData;
    }

    /**
     * @param \Generated\Shared\Transfer\TableConfigurationTransfer $tableConfigurationTransfer
     *
     * @return int[]
     */
    protected function prepareAvailablePageSizesConfigurationData(TableConfigurationTransfer $tableConfigurationTransfer): array
    {
        return !empty($tableConfigurationTransfer->getAvailablePageSizes())
            ? $tableConfigurationTransfer->getAvailablePageSizes()
            : static::DEFAULT_AVAILABLE_PAGE_SIZES;
    }

    /**
     * @param \Generated\Shared\Transfer\TableConfigurationTransfer $tableConfigurationTransfer
     *
     * @return array
     */
    protected function prepareFiltersConfigurationData(TableConfigurationTransfer $tableConfigurationTransfer): array
    {
        $filtersData = [];

        foreach ($tableConfigurationTransfer->getFilters() as $filterTransfer) {
            $filtersData[] = $filterTransfer->toArray();
        }

        return $filtersData;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return void
     */
    protected function initialize(Request $request): void
    {
        $this->searchTerm = $request->query->get(static::PARAM_SEARCH_TERM);
        $this->page = $request->query->get(static::PARAM_PAGE, static::DEFAULT_PAGE);
        $this->pageSize = $request->query->get(static::PARAM_PAGE_SIZE, static::DEFAULT_PAGE_SIZE);
        $this->setSorting($request);
        $this->setFilters($request);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Spryker\Zed\ProductOfferGuiPage\Exception\InvalidSortingDataException
     *
     * @return void
     */
    protected function setSorting(Request $request): void
    {
        $sortingData = $request->query->has(static::PARAM_ORDER_BY) ? json_decode($request->query->get(static::PARAM_ORDER_BY), true) : null;
        $defaultSortColumn = $this->provideTableConfiguration()->getDefaultSortColumn();

        if (!$sortingData && $defaultSortColumn) {
            $sortingData = [$defaultSortColumn => static::DEFAULT_ORDER_DIRECTION];
        }

        if (!$sortingData) {
            throw new InvalidSortingDataException('Sorting data is not present.');
        }

        foreach ($sortingData as $field => $direction) {
            $this->sorting[$field] = $direction;
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return void
     */
    protected function setFilters(Request $request): void
    {
        $filtersData = json_decode($request->query->get(static::PARAM_FILTERS), true);

        if (!$filtersData) {
            return;
        }

        foreach ($filtersData as $filterName => $filterData) {
            $this->filters[$filterName] = $filterData;
        }
    }

    /**
     * @return \Generated\Shared\Transfer\TableDataTransfer
     */
    abstract protected function provideTableData(): TableDataTransfer;

    /**
     * @return \Generated\Shared\Transfer\TableConfigurationTransfer
     */
    abstract protected function provideTableConfiguration(): TableConfigurationTransfer;
}
