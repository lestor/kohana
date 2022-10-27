<?php

/**
 * A version of the stock PHPUnit testcase that includes some extra helpers
 * and default settings
 */
abstract class Kohana_Unittest_TestCase extends \PHPUnit\Framework\TestCase {
	
	/**
	 * Make sure PHPUnit backs up globals
	 * @var boolean
	 */
	// @codingStandardsIgnoreStart
	protected $backupGlobals = FALSE;
	// @codingStandardsIgnoreEnd

	/**
	 * A set of unittest helpers that are shared between normal / database
	 * testcases
	 * @var Kohana_Unittest_Helpers
	 */
	protected $_helpers = NULL;

	/**
	 * A default set of environment to be applied before each test
	 * @var array
	 */
	// @codingStandardsIgnoreStart
	protected $environmentDefault = array();
	// @codingStandardsIgnoreEnd

	/**
	 * Creates a predefined environment using the default environment
	 *
	 * Extending classes that have their own setUp() should call
	 * parent::setUp()
	 */
	// @codingStandardsIgnoreStart
	public function setUp() : void
	// @codingStandardsIgnoreEnd
	{
		$this->_helpers = new Unittest_Helpers;

		$this->setEnvironment($this->environmentDefault);
	}

	/**
	 * Restores the original environment overriden with setEnvironment()
	 *
	 * Extending classes that have their own tearDown()
	 * should call parent::tearDown()
	 */
	// @codingStandardsIgnoreStart
	public function tearDown() : void
	// @codingStandardsIgnoreEnd
	{
		$this->_helpers->restore_environment();
	}

	/**
	 * Removes all kohana related cache files in the cache directory
	 */
	// @codingStandardsIgnoreStart
	public function cleanCacheDir()
	// @codingStandardsIgnoreEnd
	{
		return Unittest_Helpers::clean_cache_dir();
	}

	/**
	 * Helper function that replaces all occurences of '/' with
	 * the OS-specific directory separator
	 *
	 * @param string $path The path to act on
	 * @return string
	 */
	// @codingStandardsIgnoreStart
	public function dirSeparator($path)
	// @codingStandardsIgnoreEnd
	{
		return Unittest_Helpers::dir_separator($path);
	}

	/**
	 * Allows easy setting & backing up of enviroment config
	 *
	 * Option types are checked in the following order:
	 *
	 * * Server Var
	 * * Static Variable
	 * * Config option
	 *
	 * @param array $environment List of environment to set
	 */
	// @codingStandardsIgnoreStart
	public function setEnvironment(array $environment)
	// @codingStandardsIgnoreEnd
	{
		return $this->_helpers->set_environment($environment);
	}

	/**
	 * Check for internet connectivity
	 *
	 * @return boolean Whether an internet connection is available
	 */
	// @codingStandardsIgnoreStart
	public function hasInternet()
	// @codingStandardsIgnoreEnd
	{
		return Unittest_Helpers::has_internet();
	}
}
