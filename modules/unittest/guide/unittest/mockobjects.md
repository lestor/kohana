# Mock objects

Sometimes when writing tests you need to test something that depends on an object being in a certain state.

Say for example you're testing a model - you want to make sure that the model is running the correct query, but you don't want it to run on a real database server.  You can create a mock database connection which responds in the way the model expects, but doesn't actually connect to a physical database.

PHPUnit has a built in mock object creator which can generate mocks for classes (inc. abstract ones) on the fly.  
It creates a class that extends the one you want to mock.  You can also tell PHPUnit to override certain functions to return set values / assert that they're called in a specific way.

## Creating an instance of a mock class

You create mocks from within testcases using the `createMock()` function, which is defined in `\PHPUnit\Framework\TestCase` like so:

    $this->createMock($originalClassName);

`$originalClassName`
: The name of the class that you want to mock

When the defaults used by the `createMock()` method to generate the test double do not match your needs then you can use the `getMockBuilder($type)` method to customize the test double generation using a fluent interface. 

Here is a list of methods provided by the Mock Builder: 

`setMethods(array $methods)`
: Can be called on the Mock Builder object to specify the methods that are to be replaced with a configurable test double. The behavior of the other methods is not changed. If you call `setMethods(NULL)`, then no methods will be replaced.

`setMethodsExcept(array $methods)`
: Can be called on the Mock Builder object to specify the methods that will not be replaced with a configurable test double while replacing all other public methods. This works inverse to `setMethods()`.

`setConstructorArgs(array $args)`
: Can be called to provide a parameter array that is passed to the original class' constructor (which is not replaced with a dummy implementation by default).

`setMockClassName()`
: Can be used to specify a class name for the generated test double class.

`disableOriginalConstructor()`
: Can be used to disable the call to the original class' constructor.

`disableOriginalClone()`
: Can be used to disable the call to the original class' clone constructor.

`disableAutoload()`
: Can be used to disable `__autoload()` during the generation of the test double class.

See detailed documentation on the [official website](https://phpunit.de/manual/7.0/en/test-doubles.html).

Most of the time you will need to use only a few parameters:

	$mock = $this->createMock('ORM');

`$mock` now contains a mock of ORM and can be handled as though it were a vanilla instance of `ORM`

	$mock = $this
		->getMockBuilder('ORM')
		->setMethods(array('check'))
		->getMock();

`$mock` now contains a mock of ORM, but this time we're also mocking the check() method.

## Mocking methods

Assuming we've created a mock object like so:

	$mock = $this
		->getMockBuilder('ORM')
		->setMethods(array('check'))
		->getMock();

We now need to tell PHPUnit how to mock the check function when its called.

### How many times should it be called?

You start off by telling PHPUnit how many times the method should be called by calling expects() on the mock object:

	$mock->expects($matcher);

`expects()` takes one argument, an invoker matcher which you can create using factory methods defined in `\PHPUnit\Framework\TestCase`:

#### Possible invoker matchers:

`$this->any()`
: Returns a matcher that matches when the method it is evaluated for is executed zero or more times.

`$this->never()`
: Returns a matcher that matches when the method it is evaluated for is never executed.

`$this->once()`
: Returns a matcher that matches when the method it is evaluated for is executed exactly once.

`$this->atLeastOnce()`
: Returns a matcher that matches when the method it is evaluated for is executed at least once.

`$this->exactly($count)`
: Returns a matcher that matches when the method it is evaluated for is executed exactly `$count` times.

`$this->at($index)`
: Returns a matcher that matches when the method it is evaluated for is invoked at the given `$index`.

In our example we want `check()` to be called once on our mock object, so if we update it accordingly:

	$mock = $this
    	->getMockBuilder('ORM')
    	->setMethods(array('check'))
    	->getMock();

	$mock->expects($this->once());

### What is the method we're mocking?

Although we told PHPUnit what methods we want to mock, we haven't actually told it what method these rules we're specifiying apply to.  
You do this by calling `method()` on the returned from `expects()`:

	$mock->expects($matcher)
		->method($methodName);

As you can probably guess, `method()` takes one parameter, the name of the method you're mocking.  
There's nothing very fancy about this function.

	$mock = $this
    	->getMockBuilder('ORM')
    	->setMethods(array('check'))
    	->getMock();

	$mock->expects($this->once())
		->method('check');


### What parameters should our mock method expect?

There are two ways to do this, either

* Tell the method to accept any parameters
* Tell the method to accept a specific set of parameters

The former can be achieved by calling `withAnyParameters()` on the object returned from `method()`

	$mock->expects($matcher)
		->method($methodName)
		->withAnyParameters();

To only allow specific parameters you can use the `with()` method which accepts any number of parameters.  
The order in which you define the parameters is the order that it expects them to be in when called.

	$mock->expects($matcher)
		->method($methodName)
		->with($param1, $param2);

Calling `with()` without any parameters will force the mock method to accept no parameters.

PHPUnit has a fairly complex way of comparing parameters passed to the mock method with the expected values, which can be summarised like so - 

* If the values are identical, they are equal
* If the values are of different types they are not equal
* If the values are numbers they they are considered equal if their difference is equal to zero (this level of accuracy can be changed)
* If the values are objects then they are converted to arrays and are compared as arrays
* If the values are arrays then any sub-arrays deeper than x levels (default 10) are ignored in the comparision
* If the values are arrays and one contains more than elements that the other (at any depth up to the max depth), then they are not equal

#### More advanced parameter comparisions

Sometimes you need to be more specific about how PHPUnit should compare parameters, i.e. if you want to make sure that one of the parameters is an instance of an object, yet isn't necessarily identical to a particular instance.

In PHPUnit, the logic for validating objects and datatypes has been refactored into "constraint objects".  If you look in any of the assertX() methods you can see that they are nothing more than wrappers for associating constraint objects with tests.

If a parameter passed to `with()` is not an instance of a constraint object (one which extends `\PHPUnit\Framework\Constraint`) then PHPUnit creates a new `IsEqual` comparision object for it.

i.e., the following methods produce the same result:

	->with('foo', 1);

	->with($this->equalTo('foo'), $this->equalTo(1));


See also`\PHPUnit\Framework\Assert` and `\PHPUnit\Framework\Constraint` or [official documentation](https://phpunit.de/manual/7.0/en/appendixes.assertions.html) for more information about this.

If we continue our example, we have the following:  
	
	$mock->expects($this->once())
		->method('check')
		->with();

So far PHPUnit knows that we want the `check()` method to be called once, with no parameters.  Now we just need to get it to return something...

### What should the method return?

This is the final stage of mocking a method.

By default PHPUnit can return either

* A fixed value
* One of the parameters that were passed to it
* The return value of a specified callback

Specifying a return value is easy, just call `will()` on the object returned by either `method()` or `with()`.

The function is defined like so:

	public function will(\PHPUnit\Framework\MockObject\Stub $stub)

PHPUnit provides some MockObject stubs out of the box, you can access them via (when called from a testcase):

`$this->returnValue($value)`
: Returns `$value` when the mocked method is called

`$this->returnArgument($argumentIndex)`
: Returns the `$argumentIndex`th argument that was passed to the mocked method

`$this->returnCallback($callback)`
: Returns the value of the callback, useful for more complicated mocking.  
: `$callback` should a valid callback (i.e. `is_callable($callback) === TRUE`).  PHPUnit will pass the callback all of the parameters that the mocked method was passed, in the same order / argument index (i.e. the callback is invoked by `call_user_func_array()`).
: You can usually create the callback in your testcase, as long as doesn't begin with "test"

Obviously if you really want to you can create your own MockObject stub, but these three should cover most situations.

Updating our example gives:

	$mock->expects($this->once())
		->method('check')
		->with()
		->will($this->returnValue(TRUE));

And we're done!

If you now call `$mock->check()` the value TRUE should be returned.

If you don't call a mocked method and PHPUnit expects it to be called then the test the mock was generated for will fail.


<!--
### What about if the mocked method should change everytime its called?

-->
