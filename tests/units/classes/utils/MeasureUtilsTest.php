<?php
/**
 * Created on Mar 29 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for MeasureUtils.
 */
class MeasureUtilsTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers MeasureUtils::newMeasure
     */
    public function testNewMeasure() {
        $measure = \MeasureUtils::newMeasure('Zend_Measure_Weight::TON');
        $this->assertInstanceOf("Zend_Measure_Weight", $measure);
    }

    /**
     * @covers MeasureUtils::getUnitInfo
     * @todo Implement testGetUnitInfo().
     */
    public function testGetUnitInfo() {
        $actual = \MeasureUtils::getUnitInfo('Zend_Measure_Temperature::CELSIUS');
        $excepted = array(
                        'className' => 'Zend_Measure_Temperature',
                        'constantName' => 'CELSIUS'
                        );
        $this->assertEquals($excepted, $actual);
    }
}
?>
