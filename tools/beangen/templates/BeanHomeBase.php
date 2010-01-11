global $descriptor;
echo "<" . "?php";
?>

/*
 * Do not edit this file.
 * This code was automatically generated.
 *
 * Generated on <?php echo date("F j, Y") ?>
 */

abstract class <?php echo $descriptor->xml['name'] ?>BeanHomeBase {
<?php if ($descriptor->xml['cache']) { ?>
    private static $cache; // Maps id -> <?php echo $descriptor->xml['name'] ?>Bean

<?php } ?>
    /**
     * Retrieve a <?php echo $descriptor->xml['name'] ?>Bean from the id
     * 
     * @param integer $id
     * @return <?php echo $descriptor->xml['name'] ?>Bean
     */
    public static function find($id) {
        $db = Transaction::getInstance()->getDB();
        $sql = "select * from " . <?php echo $descriptor->xml['name'] ?>Bean::TABLE_NAME .
            " where " . <?php echo $descriptor->xml['name'] ?>Bean::ID . "=" . $id;
        $db->query($sql);
        $rs = $db->fetch_row();
        return self::create($rs);
    }

<?php if ($descriptor->xml['cache']) { ?>
    /**
     * Returns the bean that has the given primary key.
     * This method is available only for beans marked with the 'cache' flag.
     * The 'cache' flag should be used only for beans that represent constants.
     *
     * @param long $id the primary key.
     */
    public static function get($id) {
        self::getAll();
        if (!isset(self::$cache[$id])) {
            return null;
        }
        return self::$cache[$id];
    }

    public static function getAll() {
        if (isset(self::$cache)) {
            return self::$cache;
        }
        self::$cache = array();
        $beans = self::findAll();
        foreach ($beans as $bean) {
            self::$cache[$bean->getId()] = $bean;
        }
        return array_values(self::$cache);
    }

<?php } ?>
    /**
     * Create a new <?php echo $descriptor->xml['name'] ?>Bean from a result set.
     *
     * @param $rs ResultSet
     * @param $alias String (optional) alias, if fields in the result set have
     *        a predefined prefix (followed by '_').
     * @return <?php echo $descriptor->xml['name'] ?>Bean
     */
    public static function create($rs, $alias='') {
        if (!$rs) {
            return null;
        }
        $bean = new <?php echo $descriptor->xml['name'] ?>Bean();
        $prefix = $alias ? "${alias}_" : "";
        self::populate($bean, $rs, $prefix);
        return $bean;
    }

    public static function populate($bean, $rs, $prefix='') {
<?php
  foreach ($descriptor->xml->field as $field) {
        $extraParams = "";
        switch ($field['type']) {
            case 'id': case 'long': case 'Boolean':
                $rsMethod = "getLong";
                break;
            case 'double':
                $rsMethod = "getDouble";
                break;
            case 'String':
                $rsMethod = "getString";
                break;
            case 'Date':
                $rsMethod = "getDate";
                if (isset($field['timezone'])) {
                    $extraParams = ", '" . $field['timezone'] . "'";
                }
                break;
            default:
                throw new Exception("Unrecognized data type (in BeanHomeBase.php): " . $field['type']);
        }
?>
        $bean-><?php echo $descriptor->setterName($field) ?>($rs-><?php echo $rsMethod ?>($prefix . <?php echo $descriptor->xml['name'] . "Bean::" . $descriptor->fieldConstant($field) . $extraParams ?>));
<?php
  }
?>
    }

    public static function findAll($paging=null) {
        $db = Transaction::getInstance()->getDB();
        $beans = array();
        $sql = "select * from " . <?php echo $descriptor->xml['name'] ?>Bean::TABLE_NAME;
        $db->query($sql, $paging);
        while ($row = $db->fetch_row()) {
            $beans[] = self::create($row);
        }
        $db->disposeQuery();
        return $beans;
    }
}