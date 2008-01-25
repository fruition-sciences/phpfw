<?php
/*
 * Created on Jul 28, 2007
 * Author: Yoni Rosenbaum
 * 
 */

require_once("PagingInfoPrefs.php");

class Table extends HtmlElement {
    private $rowCount = 0;
    private $inHead = false;
    private $inRow = false;
    private $noDataMessage;
    private $pagingInfo;

    public function __construct($name, $className='', $ctx=null) {
        parent::__construct("table", $name);
        if ($className != '') {
            $this->set('class', $className);
        }
        $this->set("cellpadding", 0);
        $this->set("cellspacing", 0);
        $this->pagingInfo = $this->createPagingInfo($ctx);
    }

    public function begin() {
        echo $this->getElementOpenTag() . "\n";
    }

    public function head() {
        $this->endRowOrHead();
        echo "<tr class=\"head\">\n";
        $this->inHead = true;
    }

    /**
     * Return a TD element that serves as a header for a column. Can be used
     * for either sortable or non-sortable columns.
     * 
     * @param String $title the displayable title for the column.
     * @param String $column (optional) Makes this column sortable by this (db) column name.
     * @param boolean $descendByDefault if true, when clicked for the first time,
     *        this column will be sorted in descending order. (false by default). 
     */
    public function columnHeader($title, $column=null, $descendByDefault=false) {
        $td = new HTMLElement("td");
        // If this column is sortable
        if ($column) {
            $href = Href::current();
            $href->set(PagingInfoPrefs::getOrderByColumnParamName($this->getName()), $column);
            $newSortOrder = $this->getNewSortOrderForColumn($column, $descendByDefault);
            $className = 'sortable';
            // If this column is sorted
            if ($this->pagingInfo->getOrderByColumn() != null && $this->pagingInfo->getOrderByColumn() == $column) {
                $className = $this->pagingInfo->isOrderByAscending() ? "orderAsc" : "orderDesc";
            }
            $href->set(PagingInfoPrefs::getOrderByAscendingParamName($this->getName()), $newSortOrder);
            $td->set('class', $className);
            $td->set('onclick', "button_click('$href')");
        } 
        $td->setBody($title);

        return $td;
    }

    /**
     * Find out what should be the sort order for the given column if this
     * column is clicked.
     * The logic is: If the column is currently the sorting order for this table,
     * then the sorting order will be the oposite of what it was.
     * If the column is not the sorting order, the sorting order would be ascending,
     * unless the argument $descendByDefault is set to true.
     * 
     * @param String $column the column name
     * @param boolean $descendByDefault if true, the sorting order will be 
     *        descending, unless the column was already the sorting column.
     * @return String '0' for descending, '1' for ascending.
     */
    private function getNewSortOrderForColumn($column, $descendByDefault) {
        if ($this->pagingInfo->getOrderByColumn() == $column) {
            return $this->pagingInfo->isOrderByAscending() ? '0' : '1';
        }
        return $descendByDefault ? '0' : '1';
    }

    public function row($class = 'row') {
        $this->endRowOrHead();
        echo "<tr class=\"$class\">\n";
        $this->rowCount++;
        $this->inRow = true;
    }

    public function setNoDataMessage($noDataMessage) {
        $this->noDataMessage = $noDataMessage;
    }

    protected function endRowOrHead() {
        if ($this->inHead) {
            echo "</tr>\n";
            $this->inHead = false;
        }
        else if ($this->inRow) {
            echo "</tr>\n";
            $this->inRow = false;
        }
    }

    private function writeNoDataMessage() {
        if (isset($this->noDataMessage) && $this->rowCount == 0) {
            echo "<tr>";
            echo "<td colspan=\"20\">$this->noDataMessage</td>";
            echo "</tr>";
        }
    }

    public function end() {
        $this->endRowOrHead();
        $this->writeNoDataMessage();
        $this->writePagingBar();
        echo $this->getElementCloseTag() . "\n";
    }

    private function writePagingBar() {
        if (!$this->pagingInfo->getTotalRows()) {
            return;
        }
        echo "<tr class=\"paging\">";
        echo "<td colspan='20' style='text-align:right'>";
        $this->writePageLinks();
        echo "</td>";
        echo "</tr>";
    }

    /**
     * Write the links to the various pages, including the 'prev' and 'next'
     * links.
     */
    private function writePageLinks() {
        $linksCount = Config::getInstance()->getInt("tablePaging/maxLinksCount", 10);
        $pageNumParamName =  PagingInfoPrefs::getPageNumberParamName($this->getName());
        $recordsPerPageParamName = PagingInfoPrefs::getRecordsPerPageParamName($this->getName());
        $pageLink = new Link(Href::current());
        $pageLink->set("class", "pageNumber");

        $arrowLink = new Link(Href::current());
        $arrowLink->set("class", "arrows");
        
        $totalPages = $this->pagingInfo->getTotalPages();
        $lastLinkablePage = min($totalPages, $this->pagingInfo->getPageNumber()+$linksCount);
        $lastLinkIsShown = $lastLinkablePage == $totalPages;
        $i =  max($this->pagingInfo->getPageNumber()-$linksCount, 0);
        $firstLinkIsShown = $i == 0;

        if (!$firstLinkIsShown) {
        	echo $arrowLink->setTitle("First")->setParam($pageNumParamName, 0);
            echo "&nbsp;|&nbsp;";
        }
        if ($this->pagingInfo->getPageNumber() > 0) {
            echo $arrowLink->setTitle("Prevous")->setParam($pageNumParamName, $this->pagingInfo->getPageNumber() - 1);
            echo " ";
        }
        if (!$firstLinkIsShown) {
            echo "...&nbsp;";
        }
        // If there's only one page available, don't write anything
        if ($i == $lastLinkablePage - 1) {
            echo "&nbsp;";
            return;
        }
        while ($i < $lastLinkablePage) {
            if ($i == $this->pagingInfo->getPageNumber()) {
                // Write current page number (not a link)
                $currentPageSpan = new HTMLElement("span");
                $currentPageSpan->set("class", "currentPage");
                $currentPageSpan->setBody($i+1);
                echo $currentPageSpan;
            }
            else {
                // Write a link to this page
                $pageLink->setParam($pageNumParamName, $i);
                $pageLink->setTitle($i+1);
                echo $pageLink;
            }
            echo "&nbsp;";
            $i++;
        }
        if (!$lastLinkIsShown) {
            echo "...";
        }
        //echo ($this->pagingInfo->getFirstRecord()+1) . " - " . ($this->pagingInfo->getFirstRecord() + $this->rowCount) . " of " . $this->pagingInfo->getTotalRows();
        if (!$this->pagingInfo->isLastPage()) {
            echo " ";
            echo $arrowLink->setTitle("Next")->setParam($pageNumParamName, $this->pagingInfo->getPageNumber() + 1);
        }
        if (!$lastLinkIsShown) {
            echo "&nbsp;|&nbsp;";
            echo $arrowLink->setTitle("Last")->setParam($pageNumParamName, $totalPages - 1);
        }
    }

    /**
     * Table is not supposed to be printed as a string. This method is overriden
     * and is used for debugging purposes only.
     */
    public function __toString() {
        return "table:" . $this->getName();
    }

    /**
     * Create a new PagingInfo object, populate with data from the Request.
     *
     * @param Context $ctx
     */
    private function createPagingInfo($ctx) {
    	$pagingInfoPrefs = new PagingInfoPrefs($ctx, $this->getName());

        $pagingInfo = new PagingInfo();
        if ($pagingInfoPrefs->getPageNumber() >= 0) {
        	$pagingInfo->setPageNumber($pagingInfoPrefs->getPageNumber());
        }
        if ($pagingInfoPrefs->getRecodsPerPage() > 0) {
        	$pagingInfo->setRecordsPerPage($pagingInfoPrefs->getRecodsPerPage());
        }
        if ($pagingInfoPrefs->getOrderByColumn() != '') {
            $pagingInfo->setOrderByColumn($pagingInfoPrefs->getOrderByColumn());
            $pagingInfo->setOrderByAscending($pagingInfoPrefs->isOrderByAscending());
        }
        return $pagingInfo;
    }

    public function getPaging() {
    	return $this->pagingInfo;
    }

    /**
     * Set the default sorting order for the PaginInfo of this table. This will
     * apply only if the order was not yet assigned.
     *
     * @param String $column the column by which to order
     * @param boolean $asc whether the sorting order is ascending or not. Default is false (descending)
     */
    public function setDefaultOrder($column, $asc=true) {
        if ($this->pagingInfo->getOrderByColumn() != '') {
            return;
        }
        $this->pagingInfo->setOrderByColumn($column);
        $this->pagingInfo->setOrderByAscending($asc);
    }

    public function getRowCount() {
        return $this->rowCount;
    }
}
