# Usage

The PHPUnit command-line test runner can be invoked through the `phpunit` command.

The following code shows how to run tests with the command-line:

	$ phpunit

PHPUnit will look for the configuration file in the current working directory. If phpunit.xml or phpunit.xml.dist (in that order) exist in the current working directory and `--configuration` is not used, the configuration will be automatically read from that file.

You can also specify a configuration file name or a specific test suite name:

	$ phpunit --configuration phpunit.xml --testsuite default

Please see the [PHPUnit Manual](https://phpunit.readthedocs.io/en/9.5/) for more details.

## Writing tests

If you're writing a test for your application, place it in "application/tests".  Similarly, if you're writing a test for a module place it in modules/[modulefolder]/tests

Rather than tell you how to write tests I'll point you in the direction of the [PHPUnit Manual](http://www.phpunit.de/manual/3.4/en/index.html).  One thing you should bear in mind when writing tests is that testcases should extend Unittest_Testcase rather than PHPUnit_Framework_TestCase, doing so gives you access to useful kohana specific helpers such as `setEnvironment()`.

Here's a taster of some of the cool things you can do with phpunit:

### Data Providers

Sometimes you want to be able to run a specific test with different sets of data to try and test every eventuality

Ordinarily you could use a foreach loop to iterate over an array of test data, however PHPUnit already can take care of this for us rather easily using "Data Providers".  A data provider is a function that returns an array of arguments that can be passed to a test.

	<?php

	Class ReallyCoolTest extends Unittest_TestCase
	{
		function providerStrLen()
		{
			return array(
				array('One set of testcase data', 24),
				array('This is a different one', 23),
			);
		}

		/**
		 * @dataProvider providerStrLen
		 */
		function testStrLen($string, $length)
		{
			$this->assertSame(
				$length,
				strlen($string)
			);
		}
	}

The key thing to notice is the `@dataProvider` tag in the doccomment, this is what tells PHPUnit to use a data provider.  The provider prefix is totally optional but it's a nice standard to identify providers.

For more info see:

* [Data Providers in PHPUnit 3.2](http://sebastian-bergmann.de/archives/702-Data-Providers-in-PHPUnit-3.2.html)
* [Data Providers](http://www.phpunit.de/manual/3.4/en/writing-tests-for-phpunit.html#writing-tests-for-phpunit.data-providers)


### Grouping tests

To allow users to selectively run tests you need to organise your tests into groups.  Here's an example test showing how to do this:


	<?php

	/**
	 * This is a description for my testcase
	 *
	 * @group somegroup
	 * @group somegroup.morespecific
	 */
	Class AnotherReallyCoolTest extends Unittest_TestCase
	{
		/**
		 * Tests can also be grouped too!
		 *
		 * @group somegroup.morespecific.annoyingstuff
		 */
		function testSomeAnnoyingCase()
		{
			// CODE!!
		}
	}

Our convention is to use lowercase group names, with more specific levels in a group seperated by periods. i.e. The Validate helper tests are part of the following groups:

	kohana
	kohana.validation
	kohana.validation.helpers

To actually limit your testing to the "somegroup" group, use:

	$ phpunit --group=somegroup

This functionality can be used to record which bug reports a test is for:

	/**
	 *
	 * @group bugs.1477
	 */
	function testAccountCannotGoBelowZero()
	{
		// Some arbitary code
	}

To see all groups that are available in your code run:

	$ phpunit --list-groups

*Note:* the `--list-groups` switch should appear before the path to the test suite loader

You can also exclude groups while testing using the `--exclude-group` switch.  This can be useful if you want to ignore all kohana tests:

	$ phpunit --exclude-group=kohana

For more info see:

* [Better PHPUnit Group Annotations](http://mikenaberezny.com/2007/09/04/better-phpunit-group-annotations/)
* [TestNG style Grouping of Tests in PHPUnit 3.2](http://sebastian-bergmann.de/archives/697-TestNG-style-Grouping-of-Tests.html)
