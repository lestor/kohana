<?php

/**
 * Tests Kohana Logging API
 *
 * @group kohana
 * @group kohana.core
 * @group kohana.core.logging
 *
 * @package    Kohana
 * @category   Tests
 * @author     Kohana Team
 * @author     Matt Button <matthew@sigswitch.com>
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_LogTest extends Unittest_TestCase {

	/**
	 * Tests that when a new logger is created the list of messages is initially
	 * empty
	 *
	 * @test
	 * @covers Log
	 * @throws ReflectionException
	 */
	public function test_messages_is_initially_empty()
	{
		$logger = new Log;

		$logger_reflection_property = new ReflectionProperty($logger, '_messages');
		$logger_reflection_property->setAccessible(TRUE);

		$this->assertSame(array(), $logger_reflection_property->getValue($logger));
	}

	/**
	 * Tests that when a new logger is created the list of writers is initially
	 * empty
	 *
	 * @test
	 * @covers Log
	 * @throws ReflectionException
	 */
	public function test_writers_is_initially_empty()
	{
		$logger = new Log;

		$logger_reflection_property = new ReflectionProperty($logger, '_writers');
		$logger_reflection_property->setAccessible(TRUE);

		$this->assertSame(array(), $logger_reflection_property->getValue($logger));
	}

	/**
	 * Test that attaching a log writer using an array of levels adds it to the array of log writers
	 *
	 * @TODO Is this test too specific?
	 *
	 * @test
	 * @covers Log::attach
	 * @throws ReflectionException
	 */
	public function test_attach_attaches_log_writer_and_returns_this()
	{
		$logger = new Log;
		$writer = $this->getMockForAbstractClass('Log_Writer');

		$this->assertSame($logger, $logger->attach($writer));

		$logger_reflection_property = new ReflectionProperty($logger, '_writers');
		$logger_reflection_property->setAccessible(TRUE);

		$this->assertSame(
			array(spl_object_hash($writer) => array('object' => $writer, 'levels' => array())),
			$logger_reflection_property->getValue($logger)
		);
	}

	/**
	 * Test that attaching a log writer using a min/max level adds it to the array of log writers
	 *
	 * @TODO Is this test too specific?
	 *
	 * @test
	 * @covers Log::attach
	 * @throws ReflectionException
	 */
	public function test_attach_attaches_log_writer_min_max_and_returns_this()
	{
		$logger = new Log;
		$writer = $this->getMockForAbstractClass('Log_Writer');

		$this->assertSame($logger, $logger->attach($writer, Log::NOTICE, Log::CRITICAL));

		switch (getenv('os'))
		{
			case 'Windows_NT':
				// https://bugs.php.net/bug.php?id=18090
				$levels = range(1, 6);
				break;
			default:
				$levels = array(Log::CRITICAL, Log::ERROR, Log::WARNING, Log::NOTICE);
		}

		$logger_reflection_property = new ReflectionProperty($logger, '_writers');
		$logger_reflection_property->setAccessible(TRUE);

		$this->assertSame(
			array(spl_object_hash($writer) => array('object' => $writer, 'levels' => $levels)),
			$logger_reflection_property->getValue($logger)
		);
	}

	/**
	 * When we call detach() we expect the specified log writer to be removed
	 *
	 * @test
	 * @covers Log::detach
	 * @throws ReflectionException
	 */
	public function test_detach_removes_log_writer_and_returns_this()
	{
		$logger = new Log;
		$writer = $this->getMockForAbstractClass('Log_Writer');

		$logger->attach($writer);

		$this->assertSame($logger, $logger->detach($writer));

		$logger_reflection_property = new ReflectionProperty($logger, '_writers');
		$logger_reflection_property->setAccessible(TRUE);

		$this->assertSame(array(), $logger_reflection_property->getValue($logger));
	}


}
