<?php
/**
 * Created Marc 16 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for Form.
 */
class FormTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Form
     */
    protected $myForm;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->myForm = new \Form();
    }

    /**
     * @covers Form::textbox
     */
    public function testTextbox() {
        $textBox =  $this->myForm->textbox('text_input');
        $this->assertInstanceOf('Textbox', $textBox);
        $this->assertEquals('text_input', $textBox->getName());
        $this->assertSame($this->myForm, $textBox->getForm());
    }

    /**
     * @covers Form::measure
     */
    public function testMeasure() {
        $measure =  $this->myForm->measure('measure_input', 'CELSIUS');
        $this->assertInstanceOf('MeasureTextbox', $measure);
        $this->assertEquals('measure_input', $measure->getName());
        $this->assertSame($this->myForm, $measure->getForm());
    }

    /**
     * @covers Form::date
     */
    public function testDate() {
        $dateBox =  $this->myForm->date('date_input');
        $this->assertInstanceOf('Datebox', $dateBox);
        $this->assertEquals('date_input', $dateBox->getName());
        $this->assertSame($this->myForm, $dateBox->getForm());
    }

    /**
     * @covers Form::password
     */
    public function testPassword() {
        $pass =  $this->myForm->password('password');
        $this->assertInstanceOf('Password', $pass);
        $this->assertEquals('password', $pass->getName());
        $this->assertSame($this->myForm, $pass->getForm());
    }

    /**
     * @covers Form::textarea
     */
    public function testTextarea() {
        $textZone =  $this->myForm->textarea('text');
        $this->assertInstanceOf('TextArea', $textZone);
        $this->assertEquals('text', $textZone->getName());
        $this->assertSame($this->myForm, $textZone->getForm());
    }

    /**
     * @covers Form::dropdown
     */
    public function testDropdown() {
        $dropdown =  $this->myForm->dropdown('list dropdown');
        $this->assertInstanceOf('Dropdown', $dropdown);
        $this->assertEquals('list dropdown', $dropdown->getName());
        $this->assertSame($this->myForm, $dropdown->getForm());
    }

    /**
     * @covers Form::checkbox
     */
    public function testCheckbox() {
        $check =  $this->myForm->checkbox('choices');
        $this->assertInstanceOf('Checkbox', $check);
        $this->assertEquals('choices', $check->getName());
        $this->assertSame($this->myForm, $check->getForm());
    }

    /**
     * @covers Form::radio
     */
    public function testRadio() {
        $radios =  $this->myForm->radio('radio button', 'resolution');
        $this->assertInstanceOf('Radio', $radios);
        $this->assertEquals('radio button', $radios->getName());
        $this->assertSame($this->myForm, $radios->getForm());
    }

    /**
     * @covers Form::hidden
     */
    public function testHidden() {
        $hidden =  $this->myForm->hidden('find out');
        $this->assertInstanceOf('Hidden', $hidden);
        $this->assertEquals('find out', $hidden->getName());
        $this->assertSame($this->myForm, $hidden->getForm());
    }

    /**
     * @covers Form::fileUpload
     */
    public function testFileUpload() {
        $upload =  $this->myForm->FileUpload('find out');
        $this->assertInstanceOf('FileUpload', $upload);
        $this->assertEquals('find out', $upload->getName());
        $this->assertSame($this->myForm, $upload->getForm());
    }

    /**
     * @covers Form::registerControl
     */
    public function testRegisterControl() {
        $cont = new \Textbox('Mask');
        $this->assertNull($cont->getForm());
        $this->myForm->registerControl($cont);
        $this->assertSame($this->myForm, $cont->getForm());
    }

    /**
     * @covers Form::label
     * @covers Form::removeEndColon
     */
    public function testLabel() {
        $elt = $this->myForm->label('mask', 'my label:');
        $this->assertInstanceOf('HtmlElement', $elt);
        $this->assertEquals('<label >my label:</label>', $elt->__toString());
    }

    /**
     * @covers Form::label
     * @covers Form::removeEndColon
     * @covers Form::addFieldError
     */
    public function testLabelRequired() {
        $this->myForm = new \Form();
        $this->myForm->addConstraint('login', \ConstraintFactory::REQUIRED);
        $this->myForm->addFieldError("login", "Wrong login");
        $elt = $this->myForm->label('login', 'my label');
        $this->assertInstanceOf('HtmlElement', $elt);
        $this->assertEquals('<label  class="required error">my label</label>', $elt->__toString());
    }

    /**
     * @covers Form::addError
     */
    public function testAddError() {
        $this->assertEmpty($this->myForm->getErrors());
        $this->myForm->addError('code', 'find out');
        $this->assertCount(1, $this->myForm->getErrors());
    }

    /**
     * @covers Form::getError
     */
    public function testGetError() {
        $this->myForm->addError('fail');
        $this->myForm->setErrorCode('fail', 'This field is required');
        $this->assertEquals('This field is required', $this->myForm->getError(0));
    }

    /**
     * @covers Form::getErrors
     */
    public function testGetErrors() {
        $this->myForm->addError('fail');
        $this->myForm->setErrorCode('fail', 'This field is required');
        $this->assertNotEmpty($this->myForm->getErrors());
    }

    /**
     * @covers Form::setErrorCode
     */
    public function testSetErrorCode() {
        $this->myForm->addError('fail');
        $this->myForm->setErrorCode('fail', 'This field is required');
        $this->assertEquals('This field is required', $this->myForm->getError(0));
    }

    /**
     * @covers Form::hasErrors
     */
    public function testHasErrors() {
        $this->assertFalse($this->myForm->hasErrors());
        $textBox =  $this->myForm->textbox('text_input');
        $this->myForm->addFieldError('text_input', 'Required input');
        $this->assertTrue($this->myForm->hasErrors());
    }

    /**
     * @covers Form::setValue
     */
    public function testSetValue() {
        $this->assertNull($this->myForm->getValue('valueIndex'));
        $this->myForm->setValue('valueIndex', 'value');
        $this->assertEquals('value', $this->myForm->getValue('valueIndex'));
        $this->myForm->setValue('valueIndex', array('value', 'secondValue', 'thirdValue'));
        $this->assertSame(array('value', 'secondValue', 'thirdValue'), $this->myForm->getValue('valueIndex[]'));
    }

    /**
     * @covers Form::getValue
     */
    public function testGetValue() {
        $this->assertNull($this->myForm->getValue('abscent'));
        $this->myForm->setValue('present', 'valeur');
        $this->assertEquals('valeur', $this->myForm->getValue('present'));
    }

    /**
     * @covers Form::setValues
     */
    public function testSetValues() {
        $this->assertEmpty($this->myForm->getValues());
        $this->myForm->setValues(array('fail, sent'));
        $this->assertNotEmpty($this->myForm->getValues());
    }

    /**
     * @covers Form::getValues
     */
    public function testGetValues() {
        $this->myForm->setValues(array('fail, sent'));
        $this->assertNotEmpty($this->myForm->getValues());
    }

    /**
     * @covers Form::addControl
     */
    public function testAddControl() {
        $this->myForm->setValue('verify', 'required');
        $this->myForm->addControl('verify', new \Textbox('phone'));
        $this->assertArrayHasKey('verify', $this->myForm->getControls());
        $this->assertArrayHasKey('verify', $this->myForm->getValues());
    }

    /**
     * @covers Form::getControls
     */
    public function testAddControls() {
        $this->myForm->setValue('verify', 'required');
        $this->myForm->addControl('verify', new \Textbox('phone'));
        $this->assertArrayHasKey('verify', $this->myForm->getControls());
    }

    /**
     * @covers Form::setReadonly
     */
    public function testSetReadonly() {
        $this->assertFalse($this->myForm->isReadonly());
        $this->myForm->setReadonly(true);
        return $this->myForm;
    }

    /**
     * @covers Form::isReadonly
     * @depends testSetReadonly
     */
    public function testIsReadonly(\Form $form) {
        $this->assertTrue($form->isReadonly());
    }

    /**
     * @covers Form::addConstraint
     */
    public function testAddConstraint() {
        $hidden = $this->myForm->getConstraintsHidden();
        $this->assertInstanceOf('Hidden', $hidden);
        $this->assertEquals('_constraints', $hidden->getName());
        $this->myForm->addConstraint('forced', \ConstraintFactory::REQUIRED);
        $hiddenAfter = $this->myForm->getConstraintsHidden();
        $this->assertInstanceOf('Hidden', $hiddenAfter);
        $this->assertEquals('_constraints', $hiddenAfter->getName());
        $this->assertNotEquals($hidden->getValue(), $hiddenAfter->getValue());
    }

    /**
     * @covers Form::findConstraint
     */
    public function testFindConstraint() {
        $find = self::getMethod('findConstraint');
        $myConstraint = $find->invokeArgs($this->myForm, array('forced', \ConstraintFactory::REQUIRED));
        $this->assertNull($myConstraint);
        $this->myForm->addConstraint('forced', \ConstraintFactory::REQUIRED);
        $myConstraint = $find->invokeArgs($this->myForm, array('forced', \ConstraintFactory::REQUIRED));
        $this->assertNotNull($myConstraint);
    }

    /**
     * @covers Form::getConstraintsHidden
     */
    public function testGetConstraintsHidden() {
        $this->myForm->addConstraint("login", \ConstraintFactory::REQUIRED);
        $hidden = $this->myForm->getConstraintsHidden();
        $this->assertInstanceOf('Hidden', $hidden);
        $this->assertEquals('_constraints', $hidden->getName());
    }

    /**
     * @covers Form::getCheckboxesHidden
     */
    public function testGetCheckboxesHidden() {
        $cb = $this->myForm->getCheckboxesHidden();
        $this->assertEmpty($cb->getValue());
        $this->myForm->checkbox('choice');
        $cb = $this->myForm->getCheckboxesHidden();
        $this->assertInstanceOf('Hidden', $cb);
        $this->assertEquals('_checkboxes', $cb->getName());
        $this->myForm->checkbox('resolution');
        $cb = $this->myForm->getCheckboxesHidden();
        $this->assertEquals('choice;resolution;', $cb->getValue());
    }

    /**
     * @covers Form::begin
     */
    public function testBegin() {
        $exceptedStr = "<form name=\"theForm\" id=\"theForm\" method=\"post\" action=\".\">";
        $this->myForm->begin();
        $this->expectOutputString($exceptedStr);
        $this->myForm->setForUpload(true);
        $strFile = $exceptedStr. "<form name=\"theForm\" id=\"theForm\" method=\"post\" action=\".\" enctype=\"multipart/form-data\">";
        $this->myForm->begin();
        $this->expectOutputString($strFile);
        return $this->myForm;
    }

    /**
     * @covers Form::end
     */
    public function testEnd() {
        $this->myForm->end();
        $this->expectOutputString('</form>');
    }

    /**
     * @covers Form::setForUpload
     */
    public function testSetForUpload() {
        $this->myForm->setForUpload(true);
        $exceptedStr = "<form name=\"theForm\" id=\"theForm\" method=\"post\" action=\".\" enctype=\"multipart/form-data\">";
        $this->myForm->begin();
        $this->expectOutputString($exceptedStr);
    }

    /**
     * @covers Form::setCalendarShown
     */
    public function testSetCalendarShown() {
        $this->assertFalse($this->myForm->isCalendarShown());
        $this->myForm->setCalendarShown();
        $this->assertTrue($this->myForm->isCalendarShown());
        return $this->myForm;
    }

    /**
     * @covers Form::isCalendarShown
     * @depends testSetCalendarShown
     */
    public function testIsCalendarShown(\Form $form) {
        $this->assertTrue($form->isCalendarShown());
    }

    /**
     * @param $name
     * @return ReflectionMethod
     */
    protected static function getMethod($name) {
        $class = new \ReflectionClass('\Form');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}