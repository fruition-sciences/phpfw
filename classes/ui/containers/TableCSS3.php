<?php
/**
 * Generate a simpler Table (no class by default) and more W3C compliant
 * HTML table to use with CSS3 style sheet.
 * 
 * @author Benjamin SOUFFLET
 * @since Fev 17, 2012
 */

class TableCSS3 extends Table {
    public function __construct($name, $className='', $ctx=null) {
        parent::__construct($name, $className, $ctx);
        $this->un_set("cellpadding");
        $this->un_set("cellspacing");
    }

    public function head() {
        $this->endRowOrHead();
        echo "<thead>\n<tr>\n";
        $this->inHead = true;
    }

    public function row($class = null) {
        $this->endRowOrHead();
        if($this->rowCount == 0){
            echo "<tbody>\n";
        }
        echo $class ? "<tr class=\"$class\">\n" : "<tr>\n";
        $this->rowCount++;
        $this->inRow = true;
    }

    protected function endRowOrHead() {
        if ($this->inHead) {
            echo "</tr>\n</thead>\n";
            $this->inHead = false;
        }
        else if ($this->inRow) {
            echo "</tr>\n";
            $this->inRow = false;
        }
    }

    protected function writeNoDataMessage() {
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
        echo "</tbody>\n";
        echo $this->getElementCloseTag() . "\n";
    }

    private function writePagingBar() {
        if (!$this->getPaging()->getTotalRows()) {
            return;
        }
        echo "<tr class=\"paging\">";
        echo "<td colspan='20'>";
        $this->showTotalCount();
        $this->writePageLinks();
        $this->showRecordsPerPage();
        echo "</td>";
        echo "</tr>";
    }
    
    /**
     * Print the the current and total row count on the footer of the table.
     */
    private function showTotalCount(){
        $body = $this->getPaging()->getFirstRecord()+1 . "-";
        if($this->getPaging()->isLastPage()){
            $body .= $this->getPaging()->getTotalRows();
        }else{
            $body .= $this->getPaging()->getFirstRecord() + $this->getPaging()->getRecordsPerPage();
        }
        $body .= " of " . $this->getPaging()->getTotalRows();
        
        $totalCountSpan = new HTMLElement("span");
        $totalCountSpan->set("class", "totalCount");
        $totalCountSpan->setBody($body);
        echo $totalCountSpan;
    }
    
    private function showRecordsPerPage(){
        $numbers = array(Config::getInstance()->getInt("tablePaging/rowsPerPage", 10), 50, 100, 200);
        if($this->getPaging()->getTotalRows() < $numbers[0]){
            return;
        }
        $body = "Show:";
        foreach($numbers as $number){
            if($number == $this->getPaging()->getRecordsPerPage()){
                $recordsNumber = new HTMLElement("span");
                $recordsNumber->setBody($number);
            }else{
                $recordsNumber = new Link(Href::current());
                $recordsNumber->setParam(PagingInfoPrefs::getPageNumberParamName($this->getName()), floor($this->getPaging()->getFirstRecord()/$number));
                $recordsNumber->setTitle($number)->setParam(PagingInfoPrefs::getRecordsPerPageParamName($this->getName()), $number);
            }
            $body .= $recordsNumber;
            if($this->getPaging()->getTotalRows() < $number){
                break;
            }
        }
        $recordsPerPageSpan = new HTMLElement("span");
        $recordsPerPageSpan->set("class", "recordsPerPage");
        $recordsPerPageSpan->setBody($body);
        echo $recordsPerPageSpan;
    }
}
