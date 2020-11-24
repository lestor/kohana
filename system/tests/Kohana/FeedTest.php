<?php

/**
 * Test for feed helper
 *
 * @group kohana
 * @group kohana.core
 * @group kohana.core.feed
 *
 * @package    Kohana
 * @category   Tests
 * @author     Kohana Team
 * @author     Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_FeedTest extends Unittest_TestCase
{

	/**
	 * Sets up the environment
	 */
	// @codingStandardsIgnoreStart
	public function setUp() : void
	// @codingStandardsIgnoreEnd
	{
		parent::setUp();
		Kohana::$config->load('url')->set('trusted_hosts', array('localhost'));
	}

	/**
	 * Provides test data for test_parse()
	 *
	 * @return array
	 */
	public function provider_parse()
	{
		return array(
			// $source, $expected
			array(realpath(__DIR__.'/../test_data/feeds/activity.atom'), array('Proposals (Political/Workflow) #4839 (New)', 'Proposals (Political/Workflow) #4782')),
			array(realpath(__DIR__.'/../test_data/feeds/example.rss20'), array('Example entry')),
		);
	}

	/**
	 * Tests that Feed::parse gets the correct number of elements
	 *
	 * @test
	 * @dataProvider provider_parse
	 * @covers feed::parse
	 * @param string  $source   URL to test
	 * @param integer $expected Count of items
	 */
	public function test_parse($source, $expected_titles)
	{
		$titles = array();
		foreach (Feed::parse($source) as $item)
		{
			$titles[] = $item['title'];
		}

		$this->assertSame($expected_titles, $titles);
	}

	/**
	 * Provides test data for test_create()
	 *
	 * @return array
	 */
	public function provider_create()
	{
		$info = array('pubDate' => 123, 'image' => array('link' => 'http://kohanaframework.org/image.png', 'url' => 'http://kohanaframework.org/', 'title' => 'title'));

		return array(
			// $source, $expected
			array($info, array('foo' => array('foo' => 'bar', 'pubDate' => 123, 'link' => 'foo')), array('_SERVER' => array('HTTP_HOST' => 'localhost')+$_SERVER),
				file_get_contents(realpath(__DIR__.'/../test_data/feeds/test.rss')),
			),
		);
	}

	/**
	 * @test
	 *
	 * @dataProvider provider_create
	 *
	 * @covers feed::create
	 *
	 * @param array $info  Info to pass
	 * @param array $items  Items to add
	 * @param array $environment  Server environment
	 * @param text $expected  Output for Feed::create
	 * @throws Kohana_Exception
	 */
	public function test_create($info, $items, $environment, $expected)
	{
		$this->setEnvironment($environment);

		$expected = str_replace("\r\n","\n", $expected);

		$this->assertSame($expected, Feed::create($info, $items));
	}
}
