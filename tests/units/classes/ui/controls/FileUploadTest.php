<?php
/**
 * Date: 05/07/15
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for FileUpload.
 */
class FileUploadTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers FileUpload::__construct
     */
    public function test__construct() {
        $upload = new \FileUpload("sensorsUpload");
        $this->assertSame("input", $upload->getType());
        $this->assertSame("file", $upload->get("type"));
    }
}
