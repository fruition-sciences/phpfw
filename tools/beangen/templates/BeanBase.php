global $descriptor;
echo "<" . "?php";
?>

/*
 * Do not edit this file.
 * This code was automatically generated.
<?php if ($descriptor->xml->description) { ?>
 *
 * <?php echo wordwrap($descriptor->xml->description, 77, "\n * ") ?> 
<?php } ?>
 * 
 * Generated on <?php echo date("F j, Y") ?> 
 */

abstract class <?php echo $descriptor->xml['name'] ?>BeanBase extends BeanBase {
    const TABLE_NAME = "<?php echo $descriptor->xml['tableName'] ?>";

<?php if (sizeof($descriptor->xml->constant) > 0) { ?>
    // Constants
<?php     foreach ($descriptor->xml->constant as $constant) { ?>
<?php         if ($constant['comment'] && strlen(trim($constant['comment'])) > 0) { ?>
    /**
     * <?php echo wordwrap($constant['comment'], 73, "\n     * ") ?>.
     */
<?php         } ?>
    const <?php echo $constant['name'] ?> = <?php echo $constant['id'] ?>;

<?php     } ?>
<?php } ?>
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
      $defaultValue = null;
      if ($field['type'] == "id") {
          $defaultValue = -1;
      }
      if (isset($field['defaultValue'])) {
          $defaultValue = $field['defaultValue'];
      }
      if ($defaultValue !== null) {
          echo " = $defaultValue";
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
      $comment = "";
      if ($field["comment"]) {
          $comment = $field["comment"];
      }
      if ($field['unit']) {
          $unitInfo = preg_split('/::/', $field["unit"]);
          $unitClassName = $unitInfo[0];
          $unitConstantName = $unitInfo[1];
          $comment .= " ($unitConstantName)";
      }
?>
<?php if ($comment) { ?>
    /**
     * <?php echo wordwrap($comment, 73, "\n     * ") ?>.
     * 
     * @return <?php echo $field['type']?> 
     */
<?php } ?>
    public function <?php echo $descriptor->getterName($field) ?>() {
        return $this-><?php echo $field['name']?>;
    }

<?php if ($comment) { ?>
    /**
     * <?php echo wordwrap($comment, 73, "\n     * ") ?>.
     *
     * @param <?php echo $field['type']?> $<?php echo $field["name"]?> 
     */
<?php } ?>
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
        // If id doesn't match the id of '<?php echo $rel["name"]?>', set '<?php echo $rel["name"]?>' to null
        if (isset($this-><?php echo $rel['name']?>) && $this-><?php echo $rel['name']?>->getId() != $<?php echo $field["name"]?>) {
            $this-><?php echo $rel["name"]?> = null;
        }
<?php
    }
?>
    }
<?php 
  if ($field["unit"]) {
      $measureParamName = $field["name"] . 'Measure';
?>

    /**
     * <?php echo wordwrap("Get the " . $field["name"] . " as a Zend Measure object, containing both the unit measure and the value", 73, "\n     * ") ?>.
     *
     * @return <?php echo $unitInfo[0] ?> 
     */
    public function <?php echo $descriptor->unitGetterName($field) ?>() {
        return new <?php echo $unitClassName ?>($this-><?php echo $descriptor->getterName($field) ?>(), <?php echo $field["unit"] ?>);
    }

    /**
     * <?php echo wordwrap("Set the {$field["name"]} using a Zend Measure object. The unit can be any $unitClassName unit. It will automatically be converted to $unitConstantName", 73, "\n     * ") ?>.
     * 
     * @param $<?php echo $field["name"]?>Measure <?php echo $unitClassName ?> 
     */
    public function <?php echo $descriptor->unitSetterName($field) ?>($<?php echo $measureParamName?>) {
        // If the unit is the same, just set the value
        if ($<?php echo $measureParamName?>->getType() == <?php echo $field["unit"] ?>) {
            $this-><?php echo $descriptor->setterName($field) ?>($<?php echo $measureParamName?>->getValue());
            return;
        }
        // Otherwise, clone the given measure (to avoid modifying it)
        $clonedMeasure = new <?php echo $unitClassName ?>($<?php echo $measureParamName?>->getValue(), $<?php echo $measureParamName?>->getType(), $<?php echo $measureParamName?>->getLocale());
        // Convert to <?php echo $unitConstantName ?> 
        $clonedMeasure->setType(<?php echo $field["unit"] ?>);
        $this-><?php echo $descriptor->setterName($field) ?>($clonedMeasure->getValue());
    }
<?php
  } // $field["unit"]
?>

<?php
    if (isset($descriptor->oneToOneRelsMap["${fieldName}"])) {
      $rel = $descriptor->oneToOneRelsMap["${fieldName}"];
?>
    /**
     * Get the relationship field '<?php echo $rel["name"]?>'.
     *
     * @return <?php echo $rel["refType"]?> 
     */
    public function <?php echo $descriptor->getterName($rel) ?>() {
        return $this-><?php echo $rel["name"]?>;
    }

    /**
     * Set the relationship field '<?php echo $rel["name"]?>'.
     * This also sets the field <?php echo $rel["foreignKey"]?> according to its primary key.
     *
     * @param <?php echo $rel["refType"]?> $<?php echo $rel["name"]?> 
     */
    public function <?php echo $descriptor->setterName($rel) ?>($<?php echo $rel["name"]?>) {
        $this-><?php echo $rel["name"]?> = $<?php echo $rel["name"]?>;
        $this-><?php echo $rel["foreignKey"]?> = ($<?php echo $rel["name"]?>) ? $<?php echo $rel["name"]?>->getId() : -1;
    }

    /**
     * Load the relationship field '<?php echo $rel["name"]?>' if it's not loaded yet.
     *
     * @return <?php echo $rel["refType"]?> 
     */
    public function <?php echo $descriptor->loaderName($rel) ?>() {
        if (!$this-><?php echo $rel["name"]?> && $this-><?php echo $rel["foreignKey"]?> > 0) {
            $this-><?php echo $rel["name"]?> = <?php echo $rel["refType"]?>Home::find($this-><?php echo $rel["foreignKey"]?>);
        }
        return $this-><?php echo $rel["name"]?>;
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

<?php
  if (sizeof($descriptor->xml->constant) > 0) {
?>
    /**
     * Get the localized label of this constant bean.
     */
    public function getLabel() {
        $key = strtoupper(self::TABLE_NAME) . '_' . $this->constantName;
        $label = I18nUtil::lookup("constants", $key)->__toString();
        return $label;
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
        $user = Transaction::getInstance()->getUser();
        $inputConverter = new InputConverter($user->getTimezone(), $user->getLocale());        

        $map = array();
<?php
  foreach ($descriptor->xml->field as $field) {
      $value = '$this->' . $field["name"];
      $inputConverterMethodCall = null;
      $type = $field['type'];
      $fieldConstant = 'self::' . $descriptor->fieldConstant($field);
      if ($field['unit']) {
          $inputConverterMethodCall = 'setMeasure($map, ' . $fieldConstant . ', $this->' . $descriptor->unitGetterName($field) . '())';
      }
      else if ($type == 'String') {
          $inputConverterMethodCall = 'setString($map, ' . $fieldConstant . ', ' . $value . ')';
      }
      else if ($type == 'Boolean') {
          $inputConverterMethodCall = 'setBoolean($map, ' . $fieldConstant . ', ' . $value . ')';
      }
      else if ($type == 'Date') {
          $inputConverterMethodCall = 'setDate($map, ' . $fieldConstant . ', ' . $value . ')';
      }
      else if ($type == 'time') {
          $inputConverterMethodCall = 'setTime($map, ' . $fieldConstant . ', ' . $value . ')';
      }
      else if ($type == "long" || $type == 'id') {
          $inputConverterMethodCall = 'setLong($map, ' . $fieldConstant . ', ' . $value . ')';
      }
      else if ($type == "double") {
          $inputConverterMethodCall = 'setDouble($map, ' . $fieldConstant . ', ' . $value . ')';
      }
      else {
          throw new IllegalStateException("Unsupported type: $type");
      }

?>
        $inputConverter-><?php echo $inputConverterMethodCall ?>;
<?php
  }
?>
        return $map;
    }

    public function setAttributes($map, $prefix='') {
        $converter = DataConverter::getInstance();
        $user = Transaction::getInstance()->getUser();
        $inputConverter = new InputConverter($user->getTimezone(), $user->getLocale());
<?php
  foreach ($descriptor->xml->field as $field) {
      $constantName = 'self::' . $descriptor->fieldConstant($field);
      $key = '$prefix . ' . $constantName;
      $constant = "\$map[\$prefix . " . $constantName . "]";
      $converterMethodCall = null;
      $setterName = $descriptor->setterName($field);
      $type = $field['type'];
      if ($type == 'long' || $type == 'id') {
          $converterMethodCall = 'getLong($map, ' . $key . ')';
      }
      if ($type == 'double') {
          $converterMethodCall = 'getDouble($map, ' . $key . ')';
      }
      if ($type == 'Date') {
          $converterMethodCall = 'getDate($map, ' . $key . ')';
          $parsedConstant = "\$converter->parseDate($constant)";
      }
      else if (strtolower($type) == 'time') {
          $parsedConstant = "\$converter->parseTime($constant)";
      } 
      else {
          $parsedConstant = $constant;
      }

      if ($field['unit']) {
          $converterMethodCall = 'getMeasure($map, ' . $key . ')';
          $setterName = $descriptor->unitSetterName($field);
      }
?>
        if (isset(<?php echo $constant?>)) {
<?php if ($converterMethodCall) {?>
            $this-><?php echo $setterName?>($inputConverter-><?php echo $converterMethodCall?>);
<?php } else {?>
            $this-><?php echo $setterName?>(<?php echo $parsedConstant?>);
<?php }?>
        }
<?php
  }
?>
    }

    /**
     * Serialize the content of the bean. For debug and logging purposes.
     *
     * @return String 
     */
    public function __toString() {
        $format = Formatter::getInstance();
        return ''
<?php
  foreach ($descriptor->xml->field as $field) {
      $fieldName = $field['name'];
      $getterName = $descriptor->getterName($field);
      $q = ($field['type'] == 'String') ? '"' : '';
      $valueExpression = '$this->' . $getterName . '()';
      if (($field['type'] == 'Date')) {
          $valueExpression = '$format->dateTime(' . $valueExpression . ')';
      }
      if ($field['type'] == 'time') {
          $valueExpression = '$format->secondsToTime(' . $valueExpression . ')';
      }
?>
            . '<?php echo $fieldName ?>=<?php echo $q ?>' . <?php echo $valueExpression ?> . '<?php echo $q ?>; '
<?php
  }
?>;
    }
}