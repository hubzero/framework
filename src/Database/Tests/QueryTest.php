<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use Hubzero\Test\Database;
use Hubzero\Database\Query;
use Hubzero\Database\Value\Basic;
use Hubzero\Database\Value\Raw;

/**
 * Base query tests
 */
class QueryTest extends Database
{
	/**
	 * Test to make sure we can run a basic select statement
	 *
	 * @return  void
	 **/
	public function testBasicFetch()
	{
		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		// Try to actually fetch some rows
		$rows = $query->select('*')
		              ->from('users')
		              ->whereEquals('id', '1')
		              ->fetch();

		// Basically, as long as we don't get false here, we're good
		$this->assertCount(1, $rows, 'Query should have returned one result');
	}

	/**
	 * Test to make sure we can run a basic insert statement
	 *
	 * @return  void
	 **/
	public function testBasicPush()
	{
		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		// Try to add a new row
		$query->push('users', [
			'name'  => 'new user',
			'email' => 'newuser@gmail.com'
		]);

		// There are 4 default users in the seed data, and adding a new one should a rowcount of 5
		$this->assertEquals(5, $this->getConnection()->getRowCount('users'), 'Push did not return the expected row count of 5');
	}

	/**
	 * Test to make sure we can run a basic update statement
	 *
	 * @return  void
	 **/
	public function testBasicAlter()
	{
		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		// Try to update an existing row
		$query->alter('users', 'id', 1, [
			'name'  => 'Updated User',
			'email' => 'updateduser@gmail.com'
		]);

		// Get the current state of the database
		$queryTable = $this->getConnection()->createQueryTable(
		    'users', 'SELECT * FROM users'
		);

		// Get our expected state of the database
		$expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__) . DS . 'Fixtures' . DS . 'updatedUsers.xml')
		                      ->getTable('users');

		// Now assert that updated and expected are the same
		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	/**
	 * Test to make sure we can set a basic value on update
	 *
	 * @return  void
	 **/
	public function testSetWithBasicValue()
	{
		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		// Try to update an existing row
		$query->alter('users', 'id', 1, [
			'name'  => new Basic('Updated User'),
			'email' => new Basic('updateduser@gmail.com')
		]);

		// Get the current state of the database
		$queryTable = $this->getConnection()->createQueryTable(
		    'users', 'SELECT * FROM users'
		);

		// Get our expected state of the database
		$expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__) . DS . 'Fixtures' . DS . 'updatedUsers.xml')
		                      ->getTable('users');

		// Now assert that updated and expected are the same
		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	/**
	 * Test to make sure we can run a basic delete statement
	 *
	 * @return  void
	 **/
	public function testBasicRemove()
	{
		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		// Try to update an existing row
		$query->remove('users', 'id', 1);

		$this->assertEquals(3, $this->getConnection()->getRowCount('users'), 'Remove did not return the expected row count of 3');
	}

	/**
	 * Test to make sure we can build a query with aliased from statements
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithAliasedFrom()
	{
		// Here's the query we're trying to write...
		$expected = "SELECT * FROM `users` AS `u` WHERE `u`.`name` = 'awesome'";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users', 'u')
		      ->whereEquals('u.name', 'awesome');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure we can build a query with where like statements
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithWhereLike()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` WHERE `name` LIKE '%awesome%'";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereLike('name', 'awesome');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure we can build a query with where IS NULL statements
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithWhereIsNull()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` WHERE `name` IS NULL";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereIsNull('name');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure we can build a query with where IS NULL statements
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithWhereIsNotNull()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` WHERE `name` IS NOT NULL";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereIsNotNull('name');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure we can build a query with complex nested where statements
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithNestedWheres()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` WHERE (`name` = 'a' OR `name` = 'b' ) AND (`email` = 'c' OR `email` = 'd' )";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereEquals('name', 'a', 1)
		      ->orWhereEquals('name', 'b', 1)
		      ->resetDepth(0)
		      ->whereEquals('email', 'c', 1)
		      ->orWhereEquals('email', 'd', 1);

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure we can build a query with a raw JOIN statement
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithJoinClause()
	{
		$dbo = $this->getMockDriver();

		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` INNER JOIN posts ON `users`.id = `posts`.user_id";

		$query = new Query($dbo);
		$query->select('*')
		      ->from('users')
		      ->join('posts', '`users`.id', '`posts`.user_id');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'join Query did not build the expected result');

		$query = new Query($dbo);
		$query->select('*')
		      ->from('users')
		      ->innerJoin('posts', '`users`.id', '`posts`.user_id');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'innerJoin Query did not build the expected result');

		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` LEFT JOIN posts ON `users`.id = `posts`.user_id";

		$query = new Query($dbo);
		$query->select('*')
		      ->from('users')
		      ->leftJoin('posts', '`users`.id', '`posts`.user_id');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'leftJoin Query did not build the expected result');

		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` RIGHT JOIN posts ON `users`.id = `posts`.user_id";

		$query = new Query($dbo);

		if ($dbo->getConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'sqlite')
		{
			$this->setExpectedException('\Hubzero\Database\Exception\UnsupportedSyntaxException');

			$query->select('*')
			      ->from('users')
			      ->rightJoin('posts', '`users`.id', '`posts`.user_id');
		}
		else
		{
			$query->select('*')
			      ->from('users')
			      ->rightJoin('posts', '`users`.id', '`posts`.user_id');

			$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'rightJoin Query did not build the expected result');
		}

		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` RIGHT JOIN posts ON `users`.id = `posts`.user_id";

		$query = new Query($dbo);
		$query->select('*')
		      ->from('users')
		      ->rightJoin('posts', '`users`.id', '`posts`.user_id');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'rightJoin Query did not build the expected result');

		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` FULL JOIN posts ON `users`.id = `posts`.user_id";

		$query = new Query($dbo);

		if ($dbo->getConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'sqlite')
		{
			$this->setExpectedException('\Hubzero\Database\Exception\UnsupportedSyntaxException');

			$query->select('*')
			      ->from('users')
			      ->fullJoin('posts', '`users`.id', '`posts`.user_id');
		}
		else
		{
			$query->select('*')
			      ->from('users')
			      ->fullJoin('posts', '`users`.id', '`posts`.user_id');

			$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'fullJoin Query did not build the expected result');
		}
	}

	/**
	 * Test to make sure we can build a query with a raw JOIN statement
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithRawJoinClause()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` INNER JOIN posts ON `users`.id = `posts`.user_id AND `users`.id > 1";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->joinRaw('posts', '`users`.id = `posts`.user_id AND `users`.id > 1');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure that fetch properly caches a query
	 *
	 * @return  void
	 **/
	public function testFetchCachesQueriesByDefault()
	{
		// Mock a database driver
		$dbo = $this->getMockDriver();

		// Mock the query builder and tell it we only want to override the query method
		$query = $this->getMockBuilder('Hubzero\Database\Query')
		              ->setConstructorArgs([$dbo])
		              ->setMethods(['query'])
		              ->getMock();

		// Now set that we should only be calling the query method one time
		// We also tell it to return something from the query method, otherwise
		// the cache will fail.
		$query->expects($this->once())
		      ->method('query')
		      ->willReturn('foo');

		// The query itself here is irrelavent, we just need to make sure
		// that calling the same query twice doesn't hit the driver twice
		$query->fetch();
		$query->fetch();
	}

	/**
	 * Test to make sure that fetch properly caches a query
	 *
	 * @return  void
	 **/
	public function testFetchDoesNotCacheQueries()
	{
		// Mock a database driver
		$dbo = $this->getMockDriver();

		// Mock the query builder and tell it we only want to override the query method
		$query = $this->getMockBuilder('Hubzero\Database\Query')
		              ->setConstructorArgs([$dbo])
		              ->setMethods(['query'])
		              ->getMock();

		// Now set that we should be calling the query exactly 2 times
		// We also tell it to return something from the query method, otherwise
		// the cache will fail and we could get a false positive.
		$query->expects($this->exactly(2))
		      ->method('query')
		      ->willReturn('foo');

		// The query itself here is irrelavent, we just need to make sure
		// that calling fetch results in a call to the query method.
		// We call it twice to ensure that the result is in the cache.
		// If the result were not in the cache, we could get a false positive.
		$query->fetch('rows', true);
		$query->fetch('rows', true);
	}

	/**
	 * Test to make sure we can build a query with where IS NULL statements
	 *
	 * @return  void
	 **/
	public function testBuildQueryClear()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `groups`";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereIsNotNull('name')
		      ->clear('from')
		      ->clear('where')
		      ->from('groups');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}
}
