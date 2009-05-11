<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 *
 */

require_once("controls/Checkbox.php");
require_once("controls/Dropdown.php");
require_once("controls/Password.php");
require_once("controls/TextArea.php");
require_once("controls/Textbox.php");
require_once("controls/Datebox.php");
require_once("controls/Hidden.php");
require_once("controls/FileUpload.php");
require_once("constraints/ConstraintFactory.php");


class Form {
    private $controls = array(); // control name -> Control
    private $labels = array(); // control name -> Label (field title)
    private $field_errors = array(); // field name -> Error (Currently used as a Set. Values don't matter)
    private $errors = array(); // Currently list of codes. Later on, list of Localized strings?
    private $values = array();
    private $errorCodes = array(); // code -> String
    private $readonly = false;
    private $constraints = array();
    private $checkboxes = array();
    private $forUpload = false;
    private $calendarShown = false;

    public function textbox($name) {
        $control = new TextBox($name);
        $this->registerControl($control);
        return $control;
    }

    public function date($name) {
        $control = new Datebox($name);
        $this->registerControl($control);
        $this->addConstraint($name, "date");
        return $control;
    }

    public function password($name) {
    	$control = new Password($name);
        $this->registerControl($control);
        return $control;
    }

    public function textarea($name) {
        $control = new TextArea($name);
        $this->registerControl($control);
        return $control;
    }

    public function dropdown($name) {
        $control = new Dropdown($name);
        $this->registerControl($control);
        return $control;
    }

    public function checkbox($name) {
        $control = new Checkbox($name);
        $this->registerControl($control);
        $this->checkboxes[] = $control;
        return $control;
    }

    public function hidden($name) {
        $control = new Hidden($name);
        $this->registerControl($control);
        return $control;
    }

    /**
     * Create a new file upload control.
     */
    public function fileUpload($name) {
        $control = new FileUpload($name);
        $this->registerControl($control);
        return $control;
    }

    public function registerControl($control) {
        $control->setForm($this);
        $this->addControl($control->getName(), $control);
    }

    public function label($name, $title) {
        $this->labels[$name] = $this->removeEndColon($title);
        $span = new HtmlElement("label");
        $span->setBody($title);
        if (isset($this->field_errors[$name])) {
            $styleClass = "error";
            $span->set("class", "error");
        }
        else {
            $styleClass = "";
        }
        return $span;
    }

    private function removeEndColon($label) {
        if (endswith($label, ':')) {
            return substr($label, 0, strlen($label)-1);
        }
        return $label;
    }

    public function addFieldError($field_name, $message) {
        $this->field_errors[$field_name] = $message;
    }

    /**
     * Add an error, to be displayed as part of the view.
     *
     * @param String $code the error code. This code can either be defined in the
     *        view template using the $form->setErrorCode() method, or as part
     *        of the localized strings.
     * @param String $field a field associated with this error.
     * @param map $attributes attribues to be used for localized strings
     */
    public function addError($code, $field='', $attributes=null) {
        $this->errors[] = $code;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getError($index) {
        $errorCode = $this->errors[$index];
        $errorStr = $this->errorCodes[$errorCode];
        return $errorStr;
    }

    /**
     * Set an error code and its string value. This method can be called from
     * the view template, in order to assign error messages to error codes. The
     * error code will normally be added to the form from the controller, which
     * will trigger the error message to show up.
     *
     * @param String $code The error code
     * @param String $msg The error message to show.
     */
    public function setErrorCode($code, $msg) {
        $this->errorCodes[$code] = $msg;
    }

    public function hasErrors() {
        return count($this->field_errors) > 0;
    }

    public function setValue($name, $value) {
        $this->values[$name] = $value;
    }

    public function getValue($name) {
        if (!isset($this->values[$name])) {
            return null;
        }
        return $this->values[$name];
    }

    public function setValues($map, $prefix="") {
        foreach ($map as $key=>$val) {
            $this->values[$prefix . $key] = $val;
        }
    }

    public function addControl($name, $control) {
        $this->controls[$name] = $control;
        if (isset($this->values[$name])) {
            $control->setValue($this->values[$name]);
        }
    }

    public function isReadonly() {
        return $this->readonly;
    }

    public function setReadonly($readonly) {
        $this->readonly = $readonly;
    }

    public function addConstraint($name, $type, $forAction=null) {
        $constraint = ConstraintFactory::newConstraint($name, $type, $forAction);
        // If is readonly, constraint will not be in form (and thus, ignored)
        if (!$this->isReadonly()) {
            $this->constraints[] = $constraint;
        }
        return $constraint;
    }

    /**
     * Find an existing constraing for the given field name and of the given
     * type.
     *
     * @param String name The field name
     * @param String  type the constraint type
     * @deprecated seems that we don't really need this method for now...
     */
    private function findConstraint($name, $type) {
        foreach ($this->constraints as $constraint) {
            if ($constraint->getName() == $name && $constraint->getType() == $type) {
                return $constraint;
            }
        }
        return null;
    }

    public function getConstraintsHidden() {
        $hidden = new Hidden("_constraints");
        // Set the labels on the constraints
        foreach ($this->constraints as $constraint) {
            if ($this->labels[$constraint->getName()]) {
                $constraint->setLabel($this->labels[$constraint->getName()]);
            }
        }
        $hidden->setValue(base64_encode(serialize($this->constraints)));
        return $hidden;
    }

    public function getCheckboxesHidden() {
        $val = "";
        foreach ($this->checkboxes as $checkbox) {
        	$val .= $checkbox->getName() . ";";
        }
        $hidden = new Hidden("_checkboxes");
        $hidden->setValue($val);
        return $hidden;
    }

    public function begin() {
    	echo "<form name=\"theForm\" id=\"theForm\" method=\"post\" action=\".\"" ;
        if ($this->forUpload) {
            echo " enctype=\"multipart/form-data\"";
        }
        echo " onKeyPress=\"return formKeyPress(event);\"";
        echo ">";
    }

    public function end() {
        echo "</form>";
    }

    public function setForUpload($forUpload) {
        $this->forUpload = $forUpload;
    }

    /**
     * Mark that the calendar control is shown.
     */
    public function setCalendarShown($calendarShown=true) {
        $this->calendarShown = $calendarShown;
    }

    public function isCalendarShown() {
        return $this->calendarShown;
    }
}
