<?php
/*
 * Created on Jul 6, 2007
 * Author: Yoni Rosenbaum
 *
 */

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
        return strtoupper($field["column"]);
    }

    public function getterName($field) {
        $verb = ($field["type"] == "Boolean") ? "is" : "get";
        return $verb . $this->capitalizeFirstLetter($field["name"]);
    }

    public function adderName($field) {
        $noun = $this->getSingular($field["name"]);
        return "add" . $this->capitalizeFirstLetter($noun);
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
        return "set" . $this->capitalizeFirstLetter($field["name"]);
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
            case "Date":
                return "SQLUtils::convertDate(\$this->${field['name']})";
                //return "\"'\" . mysql_escape_string(\$this->${field['name']}) . \"'\"";
            case "Boolean":
                 return "SQLUtils::convertBoolean(\$this->${field['name']})";
            default:
                throw new Exception("Unknown field type: " . $field["type"]);
        }
    }

    private function getPrimaryKeyField() {
        return $this->xml->field[0];
    }

    private function capitalizeFirstLetter($name) {
        $str = "" . $name;
        $first = strtoupper($str[0]);
        return $first . substr($str, 1);
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
}
