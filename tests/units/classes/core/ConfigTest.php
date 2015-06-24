<?php
/**
 * Date: 05/11/15
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for Config.
 */
class ConfigTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Config
     */
    protected $config;
    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->config = \Config::getInstance(true);
    }


    /**
     * @covers Config::getInstance
     * @covers Config::getConfigFilePath
     * @covers XMLConfig::__construct
     * @covers XMLConfig::load
     */
    public function testGetInstance() {
        $this->config = \Config::getInstance();
        $this->assertInstanceOf("Config", $this->config);
    }

    /**
     * @covers Config::getInstance
     * @covers Config::getConfigFilePath
     */
    public function testGetInstanceTestContext() {
        $this->config = \Config::getInstance(true);
        $this->assertInstanceOf("Config", $this->config);
    }

    /**
     * $configValue must be defined in config.xml
     * @covers XMLConfig::getString
     */
    public function testGetString() {
        $configValue = "webapp/defaultURL";
        $actual = $this->config->getString($configValue);
        $excepted  = "/login/home";
        $this->assertEquals($excepted, $actual);
    }

    /**
     * $configValue must be defined in config.xml
     * @covers XMLConfig::getString
     */
    public function testGetStringDefaultValue() {
        $configValue = "database/hos";
        $actual = $this->config->getString($configValue, "localhost");
        $excepted  = "localhost";
        $this->assertEquals($excepted, $actual);
    }

    /**
     * $configValue must be defined in config.xml
     * @covers XMLConfig::getString
     * @expectedException ConfigurationException
     * @expectedExceptionMessage Missing configuration value 'webap/defaultURL' in tests/config/config.xml
     */
    public function testGetStringException() {
        $configValue = "webap/defaultURL";
        $actual = $this->config->getString($configValue);
        $excepted  = "/login/home";
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers XMLConfig::getInt
     * @todo can be use to fetch string (like getString)
     */
    public function testGetInt() {
        $configValue = "tablePaging/rowsPerPage";
        $actual = $this->config->getInt($configValue);
        $excepted  = 40;
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers XMLConfig::getBoolean
     */
    public function testGetBoolean() {
        $configValue = "accessControl/ZendAcl/enable";
        $actual = $this->config->getBoolean($configValue);
        $this->assertTrue($actual);
    }

    /**
     * @covers XMLConfig::getBoolean
     */
    public function testGetBooleanFalse() {
        $configValue = "webapp/minify/yui";
        $actual = $this->config->getBoolean($configValue);
        $this->assertFalse($actual);
    }

    /**
     * @covers XMLConfig::getBoolean
     */
    public function testGetBooleanOthers() {
        $configValue = "webapp/defaultURL";
        $actual = $this->config->getBoolean($configValue);
        $this->assertFalse($actual);
    }

    /**
     * @covers XMLConfig::get
     */
    public function testGet() {
        $configValue = "properties/translator";
        $actual = $this->config->get($configValue);
        $excepted  = "GettextZendTranslator";
        $this->assertContains($excepted, $actual);
    }
}
?>
