<?php
/*
 * This object retreives the paging info preferences for the user from both
 * the Request and the Session.
 * 
 * Created on Dec 14, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class PagingInfoPrefs {
    private $tableName;
    private $pageNumber;
    private $recordsPerPage;
    private $orderByColumn;
    private $orderByAscending; // boolean

    /**
     * Construct a new PagingInfoPrefs for a given table name. Assumes that the
     * table is present in the current page.
     * 
     * @param Context $ctx
     * @param String $tableName
     */
    public function PagingInfoPrefs($ctx, $tableName) {
        $this->tableName = $tableName;
        $this->init($ctx, $tableName);
    }

    public function getPageNumber() {
        return $this->pageNumber;
    }

    public function getRecodsPerPage() {
        return $this->recordsPerPage;
    }

    public function getOrderByColumn() {
        return $this->orderByColumn;
    }

    public function isOrderByAscending() {
        return $this->orderByAscending;
    }

    /**
     * Initialize the content, to hold the paging preferences for the given
     * table name in the current page.
     * This will attempt to retreive the paging info preferences from the session
     * and from the Request. If the Request overrode the Session, the preferences
     * will be set back into the session.
     * 
     * @param Context $ctx
     * @param String $tableName
     */
    private function init($ctx, $tableName) {
        // Initialize the main area for the paging preferences on the session.
        if (!isset($_SESSION['tablePagingPrefs'])) {
            $_SESSION['tablePagingPrefs'] = array();
        }
        $key = Application::getPathInfo() . ':' . $tableName;
        // Initialize this table's preferences area on the session
        if (!isset($_SESSION['tablePagingPrefs'][$key])) {
            $_SESSION['tablePagingPrefs'][$key] = array();
        }
        $sessionTablePrefs = $_SESSION['tablePagingPrefs'][$key];
        $this->setDefaults($sessionTablePrefs);

        $requestPrefs = $this->getPrefsFromRequest($ctx);

        // Override session vals with Request vals, and set local fields.
        // Then store session (if there were any changes)
        if ($this->overrideIfSet($sessionTablePrefs, $requestPrefs)) {
            // Update session
            $this->updateSession($key, $sessionTablePrefs);
        }
        $this->setFields($sessionTablePrefs);
    }

    /**
     * Update the tablePagingPrefs for the table with the given key with the
     * given preferences.
     * Does not save the page number and recodsPerPage. We don't want those to
     * be stuck in the session, because it can be confusing for the user.
     */
    private function updateSession($key, $prefs) {
        // Remove keys we don't want in the session
        unset($prefs['pageNumber']);
        unset($prefs['recordsPerPage']);
        $_SESSION['tablePagingPrefs'][$key] = $prefs;
    }

    /**
     * Set the provate fields of this object by the values of the given preferences
     * map.
     * 
     * @param Map $prefs a map containing the preferences.
     */
    private function setFields($prefs) {
        $this->pageNumber = $prefs['pageNumber'];
        $this->recordsPerPage = $prefs['recordsPerPage'];
        $this->orderByColumn = $prefs['orderByColumn'];
        $this->orderByAscending = $prefs['orderByAscending'];
    }

    /**
     * For each of the paging prefs keys, check if it's set on $prefs2. If it
     * is, set that value into $prefs1.
     * 
     * @param Map $prefs1 first preferences map
     * @param Map $prefs2 second preferences map
     * @return boolean true if any of the values was set on $prefs2
     */
    private function overrideIfSet(&$prefs1, $prefs2) {
        $wasSet = false;
        $wasSet = self::setIfSet($prefs1, $prefs2, 'pageNumber') || $wasSet;
        $wasSet = self::setIfSet($prefs1, $prefs2, 'recordsPerPage') || $wasSet;
        $wasSet = self::setIfSet($prefs1, $prefs2, 'orderByColumn') || $wasSet;
        $wasSet = self::setIfSet($prefs1, $prefs2, 'orderByAscending') || $wasSet;
        return $wasSet;
    }

    /**
     * Check if the given key is set in $map2. If it is, set it (to same value)
     * on $map1.
     * 
     * @param Map $map1
     * @param Map $map2
     * @param String $key
     * @return boolean true if the key was set on $map2.
     */
    private function setIfSet(&$map1, $map2, $key) {
        if (isset($map2[$key])) {
            $map1[$key] = $map2[$key];
            return true;
        }
        return false;
    }

    private function getPrefsFromRequest($ctx) {
        $requestPrefs = array();
        $pageNumberKeyName = self::getPageNumberParamName($this->tableName);
        $recordsPerPageKeyName = self::getRecordsPerPageParamName($this->tableName);
        $orderByColumnKeyName = self::getOrderByColumnParamName($this->tableName);
        $orderByAscendingKeyName = self::getOrderByAscendingParamName($this->tableName);
        
        if ($ctx->getRequest()->containsKey($pageNumberKeyName)) {
            $requestPrefs['pageNumber'] = $ctx->getRequest()->getLong($pageNumberKeyName, -1);
        }
        if ($ctx->getRequest()->containsKey($recordsPerPageKeyName)) {
            $requestPrefs['recordsPerPage'] = $ctx->getRequest()->getLong($recordsPerPageKeyName, -1);
        }
        if ($ctx->getRequest()->containsKey($orderByColumnKeyName)) {
            $requestPrefs['orderByColumn'] = $ctx->getRequest()->getString($orderByColumnKeyName, '');
        }
        if ($ctx->getRequest()->containsKey($orderByAscendingKeyName)) {
            $requestPrefs['orderByAscending'] = $ctx->getRequest()->getBoolean($orderByAscendingKeyName, false);
        }
        return $requestPrefs;
    }

    /**
     * Set the default values into the given associative array of preferences.
     * Values will be set only if they are not set yet.
     */
    private function setDefaults(&$prefs) {
        $this->setDefault($prefs, 'pageNumber', -1);
        $this->setDefault($prefs, 'recordsPerPage', -1);
        $this->setDefault($prefs, 'orderByColumn', '');
        $this->setDefault($prefs, 'orderByAscending', false);
    }

    /**
     * If the given key is not set in the given map, set it to the given
     * default value.
     * 
     * @param Map $prefs the map
     * @param String $key the key
     * @param Unknown $defaultValue the value to set.
     */
    private function setDefault(&$prefs, $key, $defaultVal) {
        if (!isset($prefs[$key])) {
            $prefs[$key] = $defaultVal;
        }
    }

    public static function getPageNumberParamName($tableName) {
        return "_" . $tableName . "_pn";
    }

    public static function getRecordsPerPageParamName($tableName) {
        return "_" . $tableName . "_rpp";
    }

    public static function getOrderByColumnParamName($tableName) {
        return "_" . $tableName . "_obc";
    }

    public static function getOrderByAscendingParamName($tableName) {
        return "_" . $tableName . "_oba";
    }
}