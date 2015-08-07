<?php
/*
 * Created on Feb 11, 2011
 * Author: Yoni Rosenbaum
 *
 */

class Control extends HtmlElement {
    private $form;
    private $readonly; // if set, overwrites form's 'readonly' flag.

    /**
     * @param Form $form
     */
    public function setForm($form) {
        $this->form = $form;
    }

    /**
     * @return Form
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * Set to null to use Form's 'readonly' flag. Or set to true/false to override.
     */
    public function setReadonly($readonly) {
        $this->readonly = $readonly;
    }

    /**
     * Get the 'readonly' flag.
     * Note: Does not check the Form's 'readonly' flag.
     *
     * @return Boolean the 'readonly' flag. null value means 'not set'.
     */
    public function isReadonly() {
        return $this->readonly;
    }

    public function __toString()
    {
        // Use 'readonly' field, if set
        if ($this->readonly !== null) {
            $readonly = $this->readonly;
        }
        else {
            // Otherwise, use 'readonly' flag from form, or false if there is no form.
            $readonly = isset($this->form) ? $this->form->isReadonly() : false;
        }
        if ($readonly) {
            return (string)$this->toString();
        }
        else {
            return $this->toInput();
        }
    }

    /**
     * Return the string representatoin of this control, as it should be shown
     * in read-only mode.
     *
     * @return String
     */
    public function toString() {
        $value = $this->getValue();
        return $value != null ? $value : "";
    }

    /**
     * Return the string representation of this control, as it should be shown
     * in edit mode.
     *
     * @return string
     */
    public function toInput() {
        return parent::__toString();
    }
}