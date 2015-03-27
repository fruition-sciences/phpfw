<?php
/**
 * Created on May 13 2015
 * @author Sidiki Coulibaly
 */
namespace tests\units;
use Aura\Router\RouterFactory;
require "FSUIManager.php";
/**
 * Test class for Context
 */
class ContextTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Context
     */
    protected $ctx;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->ctx = new \Context();
    }

    /**
     * @covers Context::getForm
     * @covers Context::__construct
     * @covers Context::newUIManager
     */
    public function testGetForm() {
        $actual = $this->ctx->getForm();
        $this->assertInstanceOf("Form", $actual);
    }

    /**
     * @covers Context::getRequest
     * @covers Context::__construct
     * @covers Context::newUIManager
     */
    public function testGetRequest() {
        $actual = $this->ctx->getRequest();
        $this->assertInstanceOf("Request", $actual);
    }

    /**
     * @covers Context::getSession
     * @covers Context::__construct
     * @covers Context::newUIManager
     */
    public function testGetSession() {
        $actual = $this->ctx->getSession();
        $this->assertInstanceOf("Session", $actual);
    }

    /**
     * @covers Context::setRouter
     * @covers Context::getRouter
     */
    public function testSetRouter() {
        $factory = new RouterFactory;
        $router = $factory->newInstance();
        $actual = $this->ctx->getRouter();
        $this->assertNull($actual);
        $this->ctx->setRouter($router);
        $actual = $this->ctx->getRouter();
        $this->assertSame($router, $actual);
    }

    /**
     * @covers Context::getAttributes
     */
    public function testGetAttributes() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['name'] = "fruition sciences";
        $_POST['value'] = "Californie, Montpellier";
        $actual = $this->ctx->getAttributes();
        $excepted = array(
            "name" => "fruition sciences",
            "value" => "Californie, Montpellier");
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Context::actionIs
     */
    public function testActionIs() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['_ac'] = "goto";
        $actual = $this->ctx->actionIs("goto");
        $this->assertTrue($actual);
    }

    /**
     * @covers Context::actionIs
     */
    public function testActionIsFalse() {
        $actual = $this->ctx->actionIs("not define");
        $this->assertFalse($actual);
    }

    /**
     * @covers Context::validateConstraints
     */
    public function testValidateConstraintsNotSet() {
        $actual = $this->ctx->validateConstraints();
        $this->assertTrue($actual);
    }

    /**
     * @covers Context::validateConstraints
     */
    public function testValidateConstraintsEmpty() {
        $_REQUEST['_constraints'] = "";
        $actual = $this->ctx->validateConstraints();
        $this->assertTrue($actual);
    }



    /**
     * @covers Context::forward
     * @expectedException ForwardViewException
     */
    public function testForward() {
        $this->ctx->forward("view");
    }

    /**
     * @covers Context::normalizePath
     */
    public function testNormalizePath() {
        $actual = $this->ctx->normalizePath("/vmms/vineyard/id=23");
        $excepted = "/tests/vmms/vineyard/id=23";
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Context::normalizePath
     */
    public function testNormalizePathRoot() {
        $actual = $this->ctx->normalizePath("/vineyard/id=23");
        $excepted = "/tests/vineyard/id=23";
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Context::normalizePath
     */
    public function testNormalizePathRelativePath() {
        $actual = $this->ctx->normalizePath("vineyard/id=23");
        $excepted = "vineyard/id=23";
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Context::getUIManager
     */
    public function testGetUIManager() {
        $actual = $this->ctx->getUIManager();
        $this->assertInstanceOf("FSUIManager", $actual);
    }

    /**
     * @covers Context::getAppAlias
     */
    public function testGetAppAlias() {
        $actual = $this->ctx->getAppAlias();
        $excepted = "/tests/";
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Context::setControllerAlias
     * @covers Context::getControllerAlias
     */
    public function testSetControllerAlias() {
        $alias = "AdminController";
        $actual = $this->ctx->getControllerAlias();
        $this->assertNull($actual);
        $this->ctx->setControllerAlias($alias);
        $actual = $this->ctx->getControllerAlias();
        $this->assertEquals($alias, $actual);
    }

    /**
     * @covers Context::setUser
     * @covers Context::getUser
     */
    public function testSetUser() {
        $user = new \User();
        $actual = $this->ctx->getUser();
        $this->assertNull($actual);
        $this->ctx->setUser($user);
        $actual = $this->ctx->getUser($user);
        $this->assertEquals($user, $actual);
    }

    /**
     * @covers Context::isUserLoggedIn
     */
    public function testIsUserLoggedIn() {
        $user = new \User();
        $user->setId(2);
        $this->ctx->setUser($user);
        $actual = $this->ctx->isUserLoggedIn();
        $this->assertTrue($actual);
    }
}
