<?php
/*
 * Created on May 30, 2015
 * Author: Estelle Wan
 *
 * Purpose: Implements the Datatables plugin server-side request.
 *
 * The class parses the ajax request object $ctx and take relevant parameters
 * to construct or populate:
 *    1. Paging information which contain information about
 *       - Columns filtering, ordering
 *       - Page size and page offset
 *    2. Object output to be consumed by the datatable plugin
 *       - Total rows
 *       - Data row entries
 */

abstract class Datatable {

    /**
     * @var UI
     */
    protected $ui;

    /**
     * @var Formatter
     */
    protected $format;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var PagingInfo
     */
    protected $pagingInfo;

    /**
     * Array of database columns which should be read and sent back to DataTables.
     *
     * @var String[]
     */
    protected $aColumns;

    /**
     * @var BeanBase[]
     */
    protected $entries;

    /**
     * @var int
     */
    private $sEcho;

    /**
     * @var long
     */
    private $totalRecords;

    /**
     * @param Context $ctx
     */
    public function __construct($ctx) {
        $this->ui = $ctx->getUIManager();
        $this->form = $ctx->getForm();
        $this->format = $ctx->getUIManager()->getFormatter();
        $this->sEcho = $ctx->getRequest()->getLong('sEcho');

        $this->pagingInfo = new PagingInfo();

        $this->init($ctx);
    }

    public function init($ctx) {
        $this->initColumnInfo($ctx);
        $this->initPagingInfo($ctx);
        $this->initOrderingInfo($ctx);
        $this->initFiltering($ctx);
    }

    /*
     * Columns
     */
    private function initColumnInfo($ctx) {
        $aColumns = array();

        $numColumns = $ctx->getRequest()->getLong('iColumns');

        for ($i = 0; $i < $numColumns; ++$i) {
            $aColumns[$i] = $ctx->getRequest()->getString('mDataProp_' . $i);
        }
        $this->aColumns = $aColumns;
    }

    abstract function getSearchColumns();

    /*
     * Paging
     */
    private function initPagingInfo($ctx) {
        $offset = $ctx->getRequest()->getLong('iDisplayStart', 0);
        $pageSize = $ctx->getRequest()->getLong('iDisplayLength', 10);

        if ($pageSize > 0) { // if pagination is turned off, iDisplayLength is set to -1 by datatables
            $this->pagingInfo->setPageNumber($offset/$pageSize);
            $this->pagingInfo->setRecordsPerPage($pageSize);
        }
    }


    /*
     * Ordering
     */
    private function initOrderingInfo($ctx) {
        if ($ctx->getRequest()->containsKey('iSortCol_0')) {
            $sortColumnIndex = $ctx->getRequest()->getLong('iSortCol_0');
            $sortOrder = $ctx->getRequest()->getString('sSortDir_0', 'asc');

            $this->pagingInfo->setOrderByColumn($this->aColumns[$sortColumnIndex]);
            $this->pagingInfo->setOrderByAscending($sortOrder == 'asc');
        }
    }

    /*
     * Filtering
     */
    private function initFiltering($ctx) {
        $searchString = $ctx->getRequest()->getString('sSearch', '');

        $searchColumns = array();

        if (!empty($searchString))
            $searchColumns = $this->getSearchColumns();

        $this->pagingInfo->setSearchString($searchString);
        $this->pagingInfo->setSearchColumns($searchColumns);
    }

    /*
     * Total records
     */
    public function initNonFilteredCount($nonFilteredCount) {
        $this->totalRecords = $nonFilteredCount;
    }

    /*
     * Output
     */
    public function getDocument() {
        $doc = array(
          'sEcho' => $this->sEcho,
          'iTotalRecords' => $this->totalRecords,
          'iTotalDisplayRecords' => $this->pagingInfo->getTotalRows(),
          'aaData' => $this->entries
        );
        return $doc;
    }

    /*
     * Data Entry
     */

    /**
     * @param BeanBase $bean
     * @return Map (associative array)
     */
    abstract function getDataEntry($bean);

    /**
     * @param BeanBase[] $data
     */
    public function setData($data) {
        $entries = array();
        foreach ($data as $value) {
           $entries[] = (object)$this->getDataEntry($value);
        }

        $this->entries = $entries;
    }

    /**
     * @return PagingInfo
     */
    public function getPagingInfo() {
        return $this->pagingInfo;
    }

}