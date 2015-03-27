<?php
/**
 * created on Apr 22 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for SQLBuilder.
 */
class SQLBuilderTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \SQLBuilder
     */
    protected $sql;

    /**
     * Sets up the fixture, for example, opens a network connection.
     */
    protected function setUp() {
        $this->sql = new \SQLBuilder();
    }

    /**
     * @covers SQLBuilder::select
     */
    public function testSelect() {
        $this->sql->select('users', 'u', array('username', 'email'));
        $actual = $this->sql->__toString();
        $excepted = "select u.username u_username,u.email u_email from users u";
        $this->assertEquals($actual, $excepted);
    }

    /**
     * @covers SQLBuilder::select
     */
    public function testSelectWithFunction() {
        $this->sql->select('users', 'u', array('username', 'email'), array('max', 'avg'));
        $actual = $this->sql->__toString();
        $excepted = "select max(u.username) max_u_username,avg(u.email) avg_u_email from users u";
        $this->assertEquals($actual, $excepted);
    }

    /**
     * @covers SQLBuilder::delete
     */
    public function testDelete() {
        $this->sql->delete('users');
        $actual = $this->sql->__toString();
        $excepted = "delete  from users ";
        $this->assertEquals($actual, $excepted);
    }

    /**
     * @covers SQLBuilder::addColumns
     */
    public function testAddColumns() {
        $this->sql->addColumns('u', array('username', 'email'));
        $actual = $this->sql->__toString();
        $excepted = "select u.username u_username,u.email u_email from ";
        $this->assertSame($excepted, $actual);
    }
    /**
     * @covers SQLBuilder::addColumns
     */
    public function testAddColumnsWithFunction() {
        $this->sql->addColumns('u', array('username', 'email'), array('max', 'avg'));
        $actual = $this->sql->__toString();
        $excepted = "select max(u.username) max_u_username,avg(u.email) avg_u_email from ";
        $this->assertSame($excepted, $actual);
    }
    /**
     * @covers SQLBuilder::addColumns
     */
    public function testAddColumnsWithLessFunctions() {
        $this->sql->addColumns('u', array('username', 'email'), array('max'));
        $actual = $this->sql->__toString();
        $excepted = "select u.username u_username,u.email u_email from ";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::column
     */
    public function testColumn() {
        $this->sql->column("username", "us");
        $actual = $this->sql->__toString();
        $excepted ="select (username) us from ";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::from
     */
    public function testFrom() {
        $this->sql->from("users", "us");
        $actual = $this->sql->__toString();
        $excepted ="select  from users us";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::filter
     * @covers SQLBuilder::getBindVariableType
     * @covers SQLBuilder::expandArrayVars
     */
    public function testFilter() {
        $condition = "id in ? OR email like ?";
        $this->sql->select('users', 'u', array('id', 'username', 'email'));
        $this->sql->filter($condition, 'as', array(1,2,3), 'fr');
        $actual = $this->sql->__toString();
        $excepted = "select u.id u_id,u.username u_username,u.email u_email from users u where (id in ?,?,? OR email like ?)";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::filter
     * @covers SQLBuilder::getBindVariableType
     * @covers SQLBuilder::expandArrayVars
     * @expectedException IllegalArgumentException
     */
    public function testFilterException() {
        $condition = "id in ? OR email like ?";
        $this->sql->select('users', 'u', array('id', 'username', 'email'));
        $this->sql->filter($condition, 'a', array(1,2,3), 'fr');
    }

    /**
     * @covers SQLBuilder::filter
     * @covers SQLBuilder::getBindVariableType
     * @covers SQLBuilder::expandArrayVars
     * @expectedException IllegalArgumentException
     */
    public function testFilterArrayException() {
        $condition = "id in ? OR email like ?";
        $this->sql->select('users', 'u', array('id', 'username', 'email'));
        $this->sql->filter($condition, 'as', 3, 'fr');
    }

    /**
     * @covers SQLBuilder::join
     */
    public function testJoin() {
        $this->sql->select('users', 'u', array('id', 'username', 'email'));
        $this->sql->join("account", "acc", "acc_id=u_id", array("id"));
        $actual = $this->sql->__toString();
        $excepted = "select u.id u_id,u.username u_username,u.email u_email,acc.id acc_id from users u  join account acc on (acc_id=u_id)";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::join
     */
    public function testJoinNoColumn() {
        $this->sql->select('users', 'u', array('id', 'username', 'email'));
        $this->sql->join("account", "acc", "acc_id=u_id");
        $actual = $this->sql->__toString();
        $excepted = "select u.id u_id,u.username u_username,u.email u_email from users u  join account acc on (acc_id=u_id)";
        $this->assertSame($excepted, $actual);
    }


    /**
     * @covers SQLBuilder::leftJoin
     */
    public function testLeftJoinNoColumn() {
        $this->sql->select('users', 'u', array('id', 'username', 'email'));
        $this->sql->leftJoin("account", "acc", "acc_id=u_id");
        $actual = $this->sql->__toString();
        $excepted = "select u.id u_id,u.username u_username,u.email u_email from users u left join account acc on (acc_id=u_id)";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::leftJoin
     */
    public function testLeftJoin() {
        $this->sql->select('users', 'u', array('id', 'username', 'email'));
        $this->sql->leftJoin("account", "acc", "acc_id=u_id", array("id"));
        $actual = $this->sql->__toString();
        $excepted = "select u.id u_id,u.username u_username,u.email u_email,acc.id acc_id from users u left join account acc on (acc_id=u_id)";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::explicitJoin
     */
    public function testExplicitJoin() {
        $this->sql->select('users', 'u', array('id', 'username', 'email'));
        $this->sql->explicitJoin("account", "acc", "acc_id=u_id", \SQLJoin::LEFT_JOIN, array("id"));
        $actual = $this->sql->__toString();
        $excepted = "select u.id u_id,u.username u_username,u.email u_email,acc.id acc_id from users u left join account acc on (acc_id=u_id)";
        $this->assertSame($excepted, $actual);
    }
    /**
     * @covers SQLBuilder::explicitJoin
     */
    public function testExplicitJoinNoColumn() {
        $this->sql->select('users', 'u', array('id', 'username', 'email'));
        $this->sql->explicitJoin("account", "acc", "acc_id=u_id", \SQLJoin::INNER_JOIN);
        $actual = $this->sql->__toString();
        $excepted = "select u.id u_id,u.username u_username,u.email u_email from users u  join account acc on (acc_id=u_id)";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::orderBy
     */
    public function testOrderBy() {
        $this->sql->select('users', 'u', array('id', 'username', 'email'));
        $this->sql->orderBy("email");
        $actual = $this->sql->__toString();
        $excepted = "select u.id u_id,u.username u_username,u.email u_email from users u order by email";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::groupBy
     */
    public function testGroupBy() {
        $this->sql->select('users', 'u', array('id', 'username', 'email'));
        $this->sql->groupBy("email");
        $actual = $this->sql->__toString();
        $excepted = "select u.id u_id,u.username u_username,u.email u_email from users u group by email";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::getColumnsString
     */
    public function testGetColumnsString() {
        $this->sql->select('users', 'u', array('id', 'username', 'email'));
        $actual = $this->sql->getColumnsString();
        $excepted = "u.id u_id,u.username u_username,u.email u_email";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::__toString
     * @covers SQLBuilder::tablesToString
     */
    public function test__toString() {
        $this->sql->select('users', 'u', array('id', 'username', 'email'));
        $this->sql->groupBy('id');
        $this->sql->orderBy('email');
        $this->sql->setLimit(10);
        $this->sql->setPredicate("predicat");
        $actual = $this->sql->__toString();
        $excepted = "select predicat u.id u_id,u.username u_username,u.email u_email from users u group by id order by email limit 10";
        $this->assertSame($excepted, $actual);

    }

    /**
     * @covers SQLBuilder::__toString
     */
    public function test__toStringWhere() {
        $this->sql->select('users', 'u', array('id', 'username', 'email'));
        $this->sql->filter("id > 11");
        $actual = $this->sql->__toString();
        $excepted = "select u.id u_id,u.username u_username,u.email u_email from users u where (id > 11)";
        $this->assertSame($excepted, $actual);

    }

    /**
     * @covers SQLBuilder::setPredicate
     */
    public function testSetPredicate() {
        $this->assertEmpty($this->sql->getPredicate());
        $this->sql->setPredicate("predicate");
        $actual = $this->sql->getPredicate();
        $excepted = "predicate";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::getPredicate
     */
    public function testGetPredicate() {
        $this->sql->setPredicate("predicate");
        $actual = $this->sql->getPredicate();
        $excepted = "predicate";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::setLimit
     */
    public function testSetLimit() {
        $this->assertEmpty($this->sql->getLimit());
        $this->sql->setLimit(10);
        $actual = $this->sql->getLimit();
        $excepted = 10;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::getLimit
     */
    public function testGetLimit() {
        $this->sql->setLimit(10);
        $actual = $this->sql->getLimit();
        $excepted = 10;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers SQLBuilder::hasParams
     */
    public function testHasParams() {
        $this->assertFalse($this->sql->hasParams());
        $condition = "id in ? OR email like ?";
        $this->sql->filter($condition, 'as', array(1,2,3), 'fr');
        $this->assertTrue($this->sql->hasParams());
    }

    /**
     * @covers SQLBuilder::getParamList
     */
    public function testGetParamList() {
        $condition = "id in ? OR email like ?";
        $this->sql->filter($condition, 'as', array(1,2), 'fr');
        $params = $this->sql->getParamList();
        $this->assertContains('fr', $params);
    }

    /**
     * @covers SQLBuilder::getParamTypes
     */
    public function testGetParamTypes() {
        $condition = "id in ? OR email like ?";
        $this->sql->filter($condition, 'as', array(1,2), 'fr');
        $params = $this->sql->getParamTypes();
        $this->assertEquals('iis', $params);
    }
}
?>
