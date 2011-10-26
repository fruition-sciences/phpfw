<?php
/*
 * Created on Jul 6, 2007
 * Author: Yoni Rosenbaum
 *
 */

require_once("classes/utils/StringUtils.php");

class BeanDescriptor {
    private $xmlFile;
    public $xml;
    public $oneToOneRelsMap = array(); // primitive field name -> relationship item
    public $oneToManyRelsList = array(); // relationship items

    public function __construct($xmlFile) {
        $this->xmlFile = $xmlFile;
        $this->xml = simplexml_load_file($xmlFile);
        $this->addRequiredDefaultValues();
        $this->addBasicFields();
        $this->generateRelsMap();
        $this->markPrimaryKeyField();
    }

    public function fieldConstant($field) {
        if (isset($field["primaryKey"])) {
            return "ID";
        }
        return self::constantFromFieldName($field["name"]);
    }

    public function getterName($field) {
        $verb = ($field["type"] == "Boolean") ? "is" : "get";
        return $verb . ucfirst($field["name"]);
    }

    public function unitGetterName($field) {
        if (!isset($field['unit'])) {
            throw new IllegalArgumentException("The field " . $field["name"] . " does not have a 'unit' attribute");
        }
        return $this->getterName($field) . 'Measure';
    }

    public function adderName($field) {
        $noun = $this->getSingular($field["name"]);
        return "add" . ucfirst($noun);
    }

    private function getSingular($name) {
        $lowerName = strtolower($name);
        $count = strlen($name);
        if (endsWith($lowerName, "list")) {
            return substr($name, 0, $count-4);
        }
        if (endsWith($lowerName, "s")) {
            return substr($name, 0, $count-1);
        }
        return $name;
    }

    public function setterName($field) {
        return "set" . ucfirst($field["name"]);
    }
    
    public function unitSetterName($field) {
        if (!isset($field['unit'])) {
            throw new IllegalArgumentException("The field " . $field["name"] . " does not have a 'unit' attribute");
        }
        return $this->setterName($field) . 'Measure';
    }

    public function loaderName($field) {
        return "load" . ucfirst($field["name"]);
    }

    public function escapedField($field) {
        switch ($field["type"]) {
            case "String":
                return "SQLUtils::escapeString(\$this->${field['name']})";
                //return "\"'\" . mysql_escape_string(\$this->${field['name']}) . \"'\"";
            case "long":
                return "SQLUtils::convertLong(\$this->${field['name']})";
            case "id":
                return "SQLUtils::convertId(\$this->${field['name']})";
            case "double":
                return "SQLUtils::convertDouble(\$this->${field['name']})";
            case "Date":
                $extraParam = "";
                if ($field['timezone']) {
                    $extraParam = ", '" . $field['timezone'] . "'";
                }
                return "SQLUtils::convertDate(\$this->${field['name']}$extraParam)";
                //return "\"'\" . mysql_escape_string(\$this->${field['name']}) . \"'\"";
            case "time":
                return "SQLUtils::convertTime(\$this->${field['name']})";
            case "Boolean":
                 return "SQLUtils::convertBoolean(\$this->${field['name']})";
            case "Polygon":
                 return "SQLUtils::convertGeom(\$this->${field['name']})";
            case "Point":
                return "SQLUtils::convertGeom(\$this->${field['name']})";
            default:
                throw new Exception("Unknown field type: " . $field["type"]);
        }
    }

    private function getPrimaryKeyField() {
        return $this->xml->field[0];
    }

    /**
     * Adds the following 4 fields to the xml:
     * - create_date
     * - mod_date
     * - create_user_id
     * - mod_user_id
     */
    private function addBasicFields() {
        $this->addField("createDate", "Date", "create_date");
        $this->addField("modDate", "Date", "mod_date");
        $this->addField("createUserId", "id", "create_user_id");
        $this->addField("modUserId", "id", "mod_user_id");
    }

    private function addField($name, $type, $columnName) {
        $field = $this->xml->addChild("field");
        $field->addAttribute("name", $name);
        $field->addAttribute("type", $type);
        $field->addAttribute("column", $columnName);
        if ($type == "Date") {
            $field->addAttribute("defaultValue", "null");
        }
        return $field;
    }

    private function generateRelsMap() {
        if (!isset($this->xml->relationship)) {
            return;
        }
        foreach ($this->xml->relationship as $rel) {
            if ($rel['type'] == 'one-to-one') {
                $fieldName = $rel['foreignKey'];
                $this->oneToOneRelsMap["${fieldName}"] = $rel;
                $refType = (string)$rel['refType'];
                $refBeanDescriptor = $this->getDescriptorFromBeanClassName($refType);
                if ($refBeanDescriptor->xml['cache']) {
                    // Mark that this is a reference to a constant table
                    $rel['isConstant'] = true;
                }
            }
            if ($rel['type'] == 'one-to-many') {
                $this->oneToManyRelsList[] = $rel;
            }
        }
    }

    private function addRequiredDefaultValues() {
    	foreach ($this->xml->field as $field) {
    		if ($field['type'] == "Boolean" && !isset($field['defaultValue'])) {
    			$field['defaultValue'] = "false";
    		}
    	}
    }

    /**
     * Current implementation marks the first field as the 'primaryKey'.
     * This means that the constant for this field will be named ID.
     */
    private function markPrimaryKeyField() {
        $primaryKeyField = $this->getPrimaryKeyField();
        $primaryKeyField['primaryKey'] = 1;
    }

    /**
     * Given a field name, generate a constant name for this field.
     * This transforms a mixed case word to an upper case delimited with
     * underscores.
     *
     * @param String $fieldName
     * @return String constant name
     */
    private static function constantFromFieldName($fieldName) {
        $items = preg_split("/([A-Z])/", $fieldName, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $elements = array();
        $accumulativeSingleChars = '';
        $lastSingleChar = '';
        foreach ($items as $item) {
            if (strlen($item) == 1) {
                if ($lastSingleChar) {
                    $accumulativeSingleChars .= $lastSingleChar;
                }
                $lastSingleChar = $item;
                continue;
            }
            if ($accumulativeSingleChars) {
                $elements[] = $accumulativeSingleChars;
                $accumulativeSingleChars = '';
            }
            $elements[] = $lastSingleChar . $item;
            $lastSingleChar = '';
        }
        if ($lastSingleChar) {
            $accumulativeSingleChars .= $lastSingleChar;
        }
        if ($accumulativeSingleChars) {
            $elements[] = $accumulativeSingleChars;
        }
        $text = implode('_', $elements);
        return strtoupper($text);
    }

    /**
     * Given a bean class name, get its BeanDescriptor.
     * Assumes that the bean descriptor file is in the same directory of the
     * current (this) bean descriptor.
     * 
     * @param $beanClassName
     * @return BeanDescriptor
     */
    private function getDescriptorFromBeanClassName($beanClassName) {
        $beanDesciptorDir = dirname($this->xmlFile);
        if (!endsWith($beanClassName, 'Bean')) {
            throw new IllegalArgumentException("Invalid bean class name: $beanClassName");
        }
        $fileName = substr($beanClassName, 0, -4); // Remove 'Bean' from end
        $fileName .= '.xml';
        $file = "$beanDesciptorDir/$fileName";
        return new BeanDescriptor($file); 
    }
}
