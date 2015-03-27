<?php
/**
 * created on May 12 2015
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for Request.
 */
class RequestTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Request
     */
    protected $req;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $ctx = $this->getMockBuilder("Context")
                ->disableOriginalConstructor()
                ->getMock();
        $this->req = new \Request($ctx);
    }

    public static function setUpBeforeClass() {
        $transaction = \Transaction::getInstance();
        $user = new \User();
        $user->setTimezone("Europe/Paris");
        $transaction->setUser($user);

        $zend_locale = new \Zend_Locale("en_US");
        \Zend_Registry::set('Zend_Locale', $zend_locale);
    }

    /**
     * @covers Request::getAttributes
     * @covers Request::loadAttributes
     * @covers Request::copyAttributes
     * @covers Request::__construct
     */
    public function testGetAttributesEmpty() {
        $attributes = $this->req->getAttributes();
        $this->assertEmpty($attributes);
    }

    /**
     * @covers Request::getAttributes
     * @covers Request::loadAttributes
     * @covers Request::copyAttributes
     */
    public function testGetAttributes() {
        $_REQUEST['_checkboxes'] = "dateChoice;Resolution;size";
        $_GET['name'] = "fruition sciences";
        $_POST['value'] = "Californie, Montpellier";
        $attributes = $this->req->getAttributes();
        $excepted = array(
                        "name" => "fruition sciences",
                        "value" => "Californie, Montpellier",
                        "dateChoice" => "0", "Resolution" => "0",
                        "size" => "0");
        $this->assertEquals($excepted, $attributes);
    }

    /**
     * @covers Request::containsKey
     */
    public function testContainsKey() {
        $_REQUEST['_checkboxes'] = "dateChoice;Resolution;size";
        $actual = $this->req->containsKey("_checkboxes");
        $this->assertTrue($actual);
    }

    /**
     * @covers Request::containsKey
     */
    public function testContainsKeyFalse() {
        $actual = $this->req->containsKey("anything");
        $this->assertFalse($actual);
    }

    /**
     * @covers Request::getString
     */
    public function testGetString() {
        $default = "default";
        $actual = $this->req->getString("notExists", $default);
        $this->assertEquals($default, $actual);
    }

    /**
     * @covers Request::getString
     * @expectedException UndefinedKeyException
     * @expectedExceptionMessage Missing argument: default
     */
    public function testGetStringException() {
        $default = "default";
        $actual = $this->req->getString($default);
        $this->assertNull($actual);
    }

    /**
     * @covers Request::getString
     */
    public function testGetStringGET() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['name'] = "fruition sciences";
        $key = "name";
        $actual = $this->req->getString($key);
        $excepted = "fruition sciences";
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Request::isPost
     */
    public function testIsPostFalse() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $actual = $this->req->isPost();
        $this->assertFalse($actual);
    }

    /**
     * @covers Request::isPost
     */
    public function testIsPost() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $actual = $this->req->isPost();
        $this->assertTrue($actual);
    }

    /**
     * @covers Request::isGet
     */
    public function testIsGetFalse() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $actual = $this->req->isGet();
        $this->assertFalse($actual);
    }

    /**
     * @covers Request::isGet
     */
    public function testIsGet() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $actual = $this->req->isGet();
        $this->assertTrue($actual);
    }

    /**
     * @covers Request::getLong
     */
    public function testGetLong() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_GET['id'] = 12;
        $key = "id";
        $actual = $this->req->getLong($key);
        $excepted = 12;
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Request::getBoolean
     */
    public function testGetBoolean() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_GET['valid'] = true;
        $key = "valid";
        $actual = $this->req->getBoolean($key);
        $this->assertTrue($actual);
    }

    /**
     * @covers Request::getBoolean
     */
    public function testGetBooleanFalse() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['valid'] = false;
        $key = "valid";
        $actual = $this->req->getBoolean($key);
        $this->assertFalse($actual);
    }

    /**
     * @covers Request::getDate
     */
    public function testGetDate() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['flyDate'] = "02-02-2015";
        $key = "flyDate";
        $excepted = 1422831600;
        $actual = $this->req->getDate($key, "default");
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Request::getDate
     */
    public function testGetDateWithTimeZone() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['flyDate'] = "02-02-2015";
        $key = "flyDate";
        $excepted = 1422864000;
        $actual = $this->req->getDate($key, "default", "America/Los_Angeles");
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Request::getDate
     * @expectedException UndefinedKeyException
     * @expectedExceptionMessage Missing argument: undef
     */
    public function testGetDateException() {
        $key = "undef";
        $actual = $this->req->getDate($key);
        $this->assertNull($actual);
    }

    /**
     * @covers Request::getDate
     */
    public function testGetDefault() {
        $key = "undef";
        $actual = $this->req->getDate($key, "default", "America/Los_Angeles");
        $excepted = "default";
        $this->assertEquals($excepted, $actual);
    }
}
?>
