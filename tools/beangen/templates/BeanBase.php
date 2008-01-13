global $descriptor;
echo "<" . "?php";
?>

/*
 * Do not edit this file.
 * This code was automatically generated.
 * 
 * Generated on <?php echo date("F j, Y") ?> 
 */

abstract class <?php echo $descriptor->xml['name'] ?>BeanBase extends BeanBase {
    const TABLE_NAME = "<?php echo $descriptor->xml['tableName'] ?>";
    
    // Columns
<?php
  foreach ($descriptor->xml->field as $field) {
?>
    const <?php echo $descriptor->fieldConstant($field) ?> = "<?php echo $field['column'] ?>";
<?php
  }
?>

    // All columns
    public static $ALL = array(<?php
  $started = false;
  foreach ($descriptor->xml->field as $field) {
    if ($started) {
        echo ", ";
    }
    echo "self::" . $descriptor->fieldConstant($field);
    $started = true;
  }
?>);

    // Fields
<?php
  foreach ($descriptor->xml->field as $field) {
?>
    private $<?php 
      echo $field['name'];
      if ($field['type'] == "id") {
          echo " = -1";
      }
      if (isset($field['defaultValue'])) {
          echo " = " . $field['defaultValue'];
      }
      ?>;
<?php
  }

  if (sizeof($descriptor->xml->relationship) > 0) {
?>

    // Relationships
<?php
    foreach ($descriptor->xml->relationship as $rel) {
?>
    private $<?php echo $rel['name']?>;
<?php
    }
?>
<?php
  }
?>

<?php
  foreach ($descriptor->xml->field as $field) {
?>
    public function <?php echo $descriptor->getterName($field) ?>() {
        return $this-><?php echo $field['name']?>;
    }

    public function <?php echo $descriptor->setterName($field) ?>($<?php echo $field["name"]?>) {
<?php
  if ($field["type"] == "Boolean") {
?>
      $this-><?php echo $field["name"]?> = ($<?php echo $field["name"]?> != null && $<?php echo $field["name"]?> != 0 && $<?php echo $field["name"]?> != false);
<?php
  }
  else {
?>
      $this-><?php echo $field["name"]?> = $<?php echo $field["name"]?>;
<?php
  }
  $fieldName = $field["name"];
    if (isset($descriptor->oneToOneRelsMap["${fieldName}"])) {
      $rel = $descriptor->oneToOneRelsMap["${fieldName}"];
?>
        if (isset($this-><?php echo $rel['name']?>) && $this-><?php echo $rel['name']?>->getId() != $<?php echo $field["name"]?>) {
            $<?php echo $field["name"]?> = -1;
        }
<?php
    }
?>
    }
<?php
    if (isset($descriptor->oneToOneRelsMap["${fieldName}"])) {
      $rel = $descriptor->oneToOneRelsMap["${fieldName}"];
?>

    /**
	 * Get the relationship field <?php echo $rel["name"]?>.
	 */
    public function <?php echo $descriptor->getterName($rel) ?>() {
        return $this-><?php echo $rel["name"]?>;
    }

    /**
	 * Set the relationship field <?php echo $rel["name"]?>.
	 * This also sets the field <?php echo $rel["foreignKey"]?> according to its primary key.
	 *
	 * @param <?php echo $rel["name"]?> <?php echo $rel["refType"]?> 
	 */
    public function <?php echo $descriptor->setterName($rel) ?>($<?php echo $rel["name"]?>) {
        $this-><?php echo $rel["name"]?> = $<?php echo $rel["name"]?>;
        $this-><?php echo $rel["foreignKey"]?> = $<?php echo $rel["name"]?>->getId();
    }

<?php
    }
  }

  foreach ($descriptor->oneToManyRelsList as $rel) {
?>

    /**
     * Get the content of the <?php echo $rel['name']?> relationship field.
     *
     * @return List of <?php echo $rel['refType']?> objects.
     */
    public function <?php echo $descriptor->getterName($rel)?>() {
        return $this-><?php echo $rel['name']?>;
    }

    /**
     * Set the the <?php echo $rel['name']?> relationship field.
     *
     * @param Array (of <?php echo $rel['refType']?> objects) $list
     */
    public function <?php echo $descriptor->setterName($rel)?>($list) {
        $this-><?php echo $rel['name']?> = $list;
    }

    /**
     * Add a new <?php echo $rel['refType']?> object to the <?php echo $rel['name']?> relationship field.
     * Creates the array if it's not yet initialized.
     *
     * @param <?php echo $rel['refType']?> $bean the bean to add.
     */
    public function <?php echo $descriptor->adderName($rel)?>($bean) {
        if (!isset($this-><?php echo $rel['name']?>)) {
            $this-><?php echo $rel['name']?> = array();
        }
        $this-><?php echo $rel['name']?>[] = $bean;
    }
<?php
  }
?>

    public function insert() {
        $db = Transaction::getInstance()->getDB();
        $this->createDate = time();
        $this->createUserId = Transaction::getInstance()->getUser()->getId();
        $sql = "insert into " . self::TABLE_NAME .
            " (" .
<?php
  for ($i=1; $i<sizeof($descriptor->xml->field); $i++) {
      $field = $descriptor->xml->field[$i];
      $sep = $i < sizeof($descriptor->xml->field)-1 ? "\", \"" : "\")\"";
?>
            self::<?php echo $descriptor->fieldConstant($field) . " . " . $sep;?> .
<?php
  }
?>
            " values (" .
<?php
  for ($i=1; $i<sizeof($descriptor->xml->field); $i++) {
      $field = $descriptor->xml->field[$i];
      $sep = $i < sizeof($descriptor->xml->field)-1 ? "\",\" ." : "\")\";";
?>
            <?php echo $descriptor->escapedField($field) . " . " . $sep?>

<?php
  }
?>

        $db->query($sql);
        $this->id = $db->get_last_id();
    }

    public function update() {
        $db = Transaction::getInstance()->getDB();
        $this->modDate = time();
        $this->modUserId = Transaction::getInstance()->getUser()->getId();
        $sql = "update " . self::TABLE_NAME . " set " .
<?php
  for ($i=1; $i<sizeof($descriptor->xml->field); $i++) {
      $field = $descriptor->xml->field[$i];
      $sep = $i < sizeof($descriptor->xml->field)-1 ? "\",\" ." : "";
?>
            self::<?php echo $descriptor->fieldConstant($field)?> . " = " . <?php echo $descriptor->escapedField($field) . " . $sep"?> 
<?php
  }
?>
            " where " . self::ID . "=" . $this->id;
        $db->query($sql);
    }

    public function remove() {
        $db = Transaction::getInstance()->getDB();
        $sql = "delete from " . self::TABLE_NAME .
            " where " . self::ID . "=" . $this->id;
        $db->query($sql);
    }

    public function getAttributes() {
        $format = Formatter::getInstance();
        $map = array();
<?php
  foreach ($descriptor->xml->field as $field) {
      $convertedValue = '$this->' . $field["name"];
      if ($field['type'] == 'Date') {
          $convertedValue = '$format->date(' . $convertedValue . ')';
      }
?>
        $map[self::<?php echo $descriptor->fieldConstant($field)?>] = <?php echo $convertedValue ?>;
<?php
  }
?>
        return $map;
    }

    public function setAttributes($map, $prefix='') {
<?php
  foreach ($descriptor->xml->field as $field) {
        $constant = "\$map[\$prefix . self::" . $descriptor->fieldConstant($field) . "]";
        if ($field['type'] == 'Date') {
            $parsedConstant = "strtotime($constant)";
        }
        else {
            $parsedConstant = $constant;
        }
?>
        if (isset(<?php echo $constant?>)) {
            $this-><?php echo $descriptor->setterName($field) ?>(<?php echo $parsedConstant?>);
        }
<?php
  }
?>
    }
}