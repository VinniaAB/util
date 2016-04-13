<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-27
 * Time: 13:12
 */
namespace Vinnia\Util\Tests\Database;

use Vinnia\Util\Database\PDODatabase;
use Vinnia\Util\Database\Helper;
use Vinnia\Util\Database\SqliteQuoter;
use Vinnia\Util\Tests\AbstractTest;

class HelperTest extends AbstractTest
{
    /**
     * @var PDODatabase
     */
    public $db;

    /**
     * @var Helper
     */
    public $helper;

    public function setUp()
    {
        parent::setUp();
        $dsn = 'sqlite::memory:';
        $this->db = PDODatabase::build($dsn, '', '');
        $schema = file_get_contents(__DIR__ . '/../../_data/sqlite.sql');
        $this->db->execute($schema);
        $this->helper = new Helper($this->db, new SqliteQuoter());
    }

    public function testInsert()
    {
        $this->helper->insert('car', ['make' => 'volvo', 'model' => 'xc90']);
        $cars = $this->db->queryAll('select * from car');
        var_dump($cars);
        $this->assertCount(1, $cars);
        $this->assertEquals('volvo', $cars[0]['make']);
        $this->assertEquals('xc90', $cars[0]['model']);
    }

    public function testUpdate()
    {
        $this->helper->insert('car', ['make' => 'volvo', 'model' => 'xc90']);
        $this->helper->update('car', ['model' => 'v70'], ['make' => 'volvo']);
        $cars = $this->db->queryAll('select * from car');
        var_dump($cars);
        $this->assertCount(1, $cars);
        $this->assertEquals('volvo', $cars[0]['make']);
        $this->assertEquals('v70', $cars[0]['model']);
    }

    public function testExists()
    {
        $this->helper->insert('car', ['make' => 'volvo', 'model' => 'xc90']);
        $this->assertTrue($this->helper->exists('car', ['make' => 'volvo', 'model' => 'xc90']));
        $this->assertFalse($this->helper->exists('car', ['make' => 'toyota']));
    }

    public function testSelectOne()
    {
        $this->helper->insert('car', ['make' => 'volvo', 'model' => 'xc90']);
        $car = $this->helper->selectOne('car');
        $this->assertEquals('volvo', $car['make']);
        $this->assertEquals('xc90', $car['model']);
    }

    public function testSelect()
    {
        $this->helper->insert('car', ['make' => 'volvo', 'model' => 'xc90']);
        $this->helper->insert('car', ['make' => 'volvo', 'model' => 'v70']);
        $cars = $this->helper->select('car');
        $this->assertCount(2, $cars);
        $this->assertEquals('volvo', $cars[0]['make']);
        $this->assertEquals('xc90', $cars[0]['model']);
        $this->assertEquals('volvo', $cars[1]['make']);
        $this->assertEquals('v70', $cars[1]['model']);
    }

    public function testSelectWithPredicate()
    {
        $this->helper->insert('car', ['make' => 'volvo', 'model' => 'xc90']);
        $this->helper->insert('car', ['make' => 'volvo', 'model' => 'v70']);
        $cars = $this->helper->select('car', ['*'], ['model' => 'v70']);
        $this->assertCount(1, $cars);
        $this->assertEquals('volvo', $cars[0]['make']);
        $this->assertEquals('v70', $cars[0]['model']);
    }

    public function testInsertOrUpdateWithoutPreviousValue()
    {
        $this->helper->insertOrUpdate(
            'car',
            ['make' => 'volvo', 'model' => 'xc90'],
            ['make' => 'volvo']
        );
        $cars = $this->helper->select('car');
        $this->assertCount(1, $cars);
        $this->assertEquals('volvo', $cars[0]['make']);
        $this->assertEquals('xc90', $cars[0]['model']);
    }

    public function testInsertOrUpdateWithPreviousValue()
    {
        $this->helper->insert('car', ['make' => 'volvo', 'model' => 'xc90']);
        $this->helper->insertOrUpdate(
            'car',
            ['make' => 'volvo', 'model' => 'v70'],
            ['make' => 'volvo']
        );
        $cars = $this->helper->select('car');
        $this->assertCount(1, $cars);
        $this->assertEquals('volvo', $cars[0]['make']);
        $this->assertEquals('v70', $cars[0]['model']);
    }
}
