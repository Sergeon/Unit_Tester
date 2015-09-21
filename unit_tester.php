<?php


/**
 * @author Mauro
*@Version 1.0 09/2015
*
*Class with unit testing facilities.
*Usage: set proper method and param to instance using set_current_method and set_current_params methods, and 
*then execute some tests agains that method using the assertion methods.
*
* Change method and params whenever you need. 
* 
* At the end, retrieve tests with get_results();
* 
* see internal Test_Wrapper class documention to properly manage the results.
*
*/
class Unit_Tester {

	//***********************************************************************************************************************************************
	//Class members
	//***********************************************************************************************************************************************
	/**
	 * Array
	 * Unit_Test_Result collection*/
	protected $tests;

	/**
	 * PHP Callable with the method definition to be tested.
	 */
	protected $method;


	/**
	 * @var array
	 */
	protected $params = array();

	//***********************************************************************************************************************************************
	//Public interface methods
	//***********************************************************************************************************************************************
	/**
	 * Sets the current method to execute tests against, given a callable.
	 * @param callable $method
	 * return void
	 */
	public function set_current_method( callable $method ){
		$this->method = $method;

	}

	/**
	 * Adds parameters to apply with the current method.
	 * @param array $params
	 * return void
	 */
	public function set_current_params( array $params ){

		$this->params = $params;
	}


	/**
	 * clean the paraments
	 * return void
	 */
	public function reset_params(){
		$this->params = array();
	}

	//the tests:

	/**
	 *Add an assert equals test to this, asserting the current method outputs $expected_result.
	 * @param  mixed $expected_results 
	 */
	public function assert_equals( $expected_result ){

		$callback_result = call_user_func_array($this->method, $this->params);
		$this->push_result(new Assert_Equals_Result($this->method ,$this->params, $expected_result, $callback_result) );

	}//end assert_equals

	/**
	 *Add an assert non-equals test to this, asserting the current method doesn't output $expected_result.
	 * @param  mixed $expected_results
	 */
	public function assert_non_equals($expected_result){

		$callback_result = call_user_func_array($this->method, $this->params);
		$this->push_result(new Assert_non_Equals_Result($this->method ,$this->params, $expected_result, $callback_result) );
	}

	/**
	 * Assert $instance if instanceof $classname
	 * @param string $instance
	 * @param string $classname
	 */
	public function assert_is_a( $instance , $classname){

		$result = is_a($instance , $classname );
		$this->push_result(  new Assert_Is_A_Result(  $instance , $classname ) );

	}


	/**
	 * asserts wether the current method throws an Exception
	 */
	public function assert_throws_exception(){

		try{
			$callback_result = call_user_func_array($this->method, $this->params);
			$this->push_result(new Assert_Throws_Exception_Result( $this->method, $this->params, null) );
		}
		catch( Exception $ex){
				
			$this->push_result( new Assert_Throws_Exception_Result( $this->method, $this->params, $ex) );
		}
	}

	/**
	 * Pushes a result to internal $tests
	 * @param Test_Result $result
	 */
	private function push_result( Test_Result $result ){

		$this->tests[] = $result;
	}


	/**
	 * Returns a Test_Wrapper object with all the tests.
	 * @return Test_Wrapper
	 */
	public function get_results(){
		return new Test_Wrapper( $this->tests );
	}


}//end class Testunit


/**
 * Every Test_Result has a statement(), wich outputs a printable elemente, an a passed() function, 
 * asserting the function call executed in the test did behave as expected.
 * @author Mauro
 *
 */
interface Test_Result{

	public function statement();

	public function passed();

}


/**
 * Represents results from a generic unit test. 
 *
 *  This class provides very little implementation
 * @author Sergeon
 */
abstract class Unit_Test_Result implements Test_Result {


	protected $passed;

	/**
	 * @return string a statement with the data of a unit test.
	 *
	 */
	public function statement(){

		if ($this->passed)
			return $this->passed_test_statement();
		else
			return $this->failed_test_statement();

	}


	public abstract function passed_test_statement();

	public abstract function failed_test_statement();



	public function passed(){
		return $this->passed;
	}

}//end unit_test_results


/**
 * Represents a typical assertion test result.
 * @author Mauro
 *
 */
abstract class Assert_Callback_Result extends Unit_Test_Result{

	protected $function_name;
	protected $params;
	protected $expected_result;
	protected $given_result;

	protected $statement_generator;

	protected $passed;

	public function __construct($function_name , $params,  $expected_result, $given_result){

		$this->function_name = $function_name;
		$this->params = $params;
		$this->expected_result = $expected_result;
		$this->given_result = $given_result;

		$this->passed = $this->expected_result == $this->given_result;

	}//end construct

	/**
	 * @return string a statement with the data of a unit test.
	 *
	 */
	public function statement(){

		if ($this->passed)
			return $this->passed_test_statement();
		else
			return $this->failed_test_statement();
	}

	public function passed(){
		return $this->passed;
	}

	/**
	 * properly outputs the function name
	 */
	protected function output_function_name(){

		return Output_Formatter::output_function_name($this->function_name );

	}


	/**
	 * properly outputs the params
	 */
	protected function output_params(){

		return Output_Formatter::output_params($this->params );
	}

	/**
	 * outputs the current given result of the test
	 * @return Ambigous <multitype, string, mixed>
	 */
	protected function output_given(){

		return $this->output_value($this->given_result);
	}
	
	/**
	 * Outputs the expected result
	 * @return Ambigous <multitype, string, mixed>
	 */
	protected function output_expected(){

		return $this->output_value($this->expected_result);

	}

	/**
	 * Outputs the current given result of the function call
	 * @param Ambigous $value <multitype, string, mixed>
	 * @return Ambigous <multitype, string, mixed>
	 */
	protected function output_value( $value ){

		return Output_Formatter::output_value($value);

	}

}


/**
 * Represents an Assert_Equals unit test result.
 * 
 */
class Assert_Equals_Result extends Assert_Callback_Result {


	/**
	 * (non-PHPdoc)
	 * @see Unit_Test_Result::passed_test_statement()
	 */
	public function passed_test_statement(){

		return "Passed equals test: " . $this->output_function_name() . $this->output_params() . " returned '" . $this->output_given()."'.";
	}

	/**
	 * (non-PHPdoc)
	 * @see Unit_Test_Result::failed_test_statement()
	 */
	public function failed_test_statement(){

		return "FAILED equals test: " . $this->output_function_name() . $this->output_params() . ". Expected  '" . $this->output_expected() . "' but returned  '" . $this->output_given() . "' instead.";
	}


}


/**
 * Represents a non equals unit test 
 * 
 *
 */
class Assert_Non_Equals_Result extends Assert_Callback_Result {

	/**
	 * 
	 * __construct implementation properly setting $passed value.
	 * @param string $function_name
	 * @param string $params
	 * @param mixed $expected_result
	 * @param mixed $given_result
	 */
	public function __construct($function_name, $params, $expected_result, $given_result){

		parent::__construct($function_name, $params, $expected_result, $given_result);

		$this->passed = $this->given_result != $expected_result;

	}

	/**
	 * (non-PHPdoc)
	 * @see Unit_Test_Result::passed_test_statement()
	 */
	public function passed_test_statement(){

		return "Passed non equals test: " . $this->output_function_name() . $this->output_params() . " returned '" . $this->output_given()."' and not: '" . $this->output_expected() . "'.";

	}

	/**
	 * (non-PHPdoc)
	 * @see Unit_Test_Result::failed_test_statement()
	 */
	public function failed_test_statement(){

		return "Failed non equals test: " . $this->output_function_name() . $this->output_params() . " returned '" . $this->output_given()."' when it was expected not to return that.";

	}


}

/**
 * Represents an is-a unit test. 
 *
 */
class Assert_Is_A_Result implements Test_Result{

	protected $passed;
	protected $instance;
	protected $classname;

	
	/**
	 *
	 * __construct implementation properly setting $passed value.
	 * @param string $function_name
	 * @param string $params
	 * @param mixed $expected_result
	 * @param mixed $given_result
	 */
	public function __construct( $instance , $classname ){

		$this->instance = $instance;
		$this->classname = $classname;

		$this->passed = is_a($this->instance , $this->classname);

	}

	/**
	 * (non-PHPdoc)
	 * @see Test_Result::statement()
	 */
	public function statement(){

		if ($this->passed)
			return "IS-A assertion passed: requested object was an instance of $this->classname";

		return "FAILED IS-A assertion: " . print_r($this->instance , true ) ." is not a $this->classname";
	}

	/**
	 * (non-PHPdoc)
	 * @see Test_Result::passed()
	 */
	public function passed(){

		return $this->passed;

	}

}

/**
 * Represents a throws exception unit test 
 *
 */
class Assert_Throws_Exception_Result implements Test_Result {


	protected $method;
	protected $params;
	protected $exception;

	/**
	 * naive implementantion of __construct. Keep in mind given and expected are not required. 
	 * @param unknown $method
	 * @param unknown $params
	 * @param unknown $exception
	 */
	public function __construct( $method , $params , $exception){

		$this->method = $method;
		$this->params = $params;
		$this->exception = $exception;

	}
	/**
	 * Returns wether the function call thrown an Exception
	 * @see Test_Result::passed()
	 */
	public function passed(){
		return isset($this->exception) && $this->exception !== null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Test_Result::statement()
	 */
	public function statement(){


		if ($this->passed() )
			return $this->passed_statement();

		return $this->failed_statement();
	}

	/**
	 * Returns a proper statement for a failed test.
	 * @return string
	 */
	private function passed_statement(){

		return "Throws Exception assertion passed: " . Output_Formatter::output_function_name($this->method) . Output_Formatter::output_params($this->params) . " did throw an Exception: " . $this->exception->getMessage() ;

	}
	
	/**
	 * Returns a proper statement for a succesful test
	 * @return string
	 */
	private function failed_statement(){

		return "Failed throws Exception test: " . Output_Formatter::output_function_name($this->method) . Output_Formatter::output_params($this->params) . " Didnt generate any exception, when it should." ;

	}


}


/**
 * 
 *Provides string construction methods to properly output the
 *results of the unit tests. 
 *
 */
class Output_Formatter{

	/**
	 * makes a ( $args ) representation of the parameters
	 * @param unknown $params
	 * @return string
	 */
	public static function output_params( $params ){

		$result = "( ";

		foreach($params as $param)
			$result .= " " . $param . "  ";

		return $result . " )";

	}

	/**
	 * Returns the proper function name of a Callable. 
	 * @param callable $function_name
	 * @return string
	 */
	public static function output_function_name( $function_name ){

		if (is_string($function_name) )
			return $function_name;

		if (is_array($function_name))
			return $function_name[1];

	}


	/**
	 * returns a proper string representation of the test result value.
	 * @param mixed $value
	 * @return Ambigous <multitype, string, mixed>
	 */
	public static function output_value( $value ){

		$helper = new Output_Formatter_Helper($value );

		return $helper->get_output();

	}


}

/**
 * 
 * Generates string outputs based on the types of the unit tests results. 
 *
 */
class Output_Formatter_Helper{


	/**
	 * @var string a data type
	 */
	protected $type;

	/**
	 * function result
	 * @var multitype
	 */
	protected $result;

	/**
	 * Instance generated on $result type
	 * @param mixed $result
	 */
	public function __construct( $result ){

		$this->result = $result;
		$this->type = gettype($this->result);

	}
	/**
	 * generates string output for a boolean value.
	 * @return string
	 */
	private function bool_output(  ){

		return $this->result ? "true" : "false";

	}
	/**
	 * Generates string output for an array value. 
	 * @return mixed
	 */
	private function array_output ( ){

		return print_r($this->result , true);
	}
	
	/**
	 * Discriminates the output method based on $this->type
	 * @return string|mixed|multitype
	 */
	public function get_output(){

		switch ($this->type){
				
			case  'boolean' :
				return $this->bool_output();
			case 'array':
				return $this->array_output();
			default :
				return $this->result;
					
		}

	}

}


/**
 * A class to wrap tests for the testunit controller.
 *
 */
class Test_Wrapper {

	/**
	 * collection of successful unit tests.
	 * @var array
	 */
	public $passed_results = array();

	
	/**
	 * collection of failed unit tests.
	 * @var array
	 */
	public $failed_results = array();

	/**
	 * Was all the test succesful?
	 * @var boolean
	 */
	public $passed  = true;

	/**
	 * 
	 * @param array $results a bunch of results from a Unit_Tester
	 */
	public function __construct( array $results ){
			
		foreach ($results as $result){
				
			if ($result->passed() )
				$this->passed_results[] = $result->statement();
			else{
				$this->failed_results[] = $result->statement();
				$this->passed = false;
			}
				
		}//end foreach
	}


	/**
	 * Provides all the test in an array
	 * @return array with 'passed' and 'failed' tests. 
	 */
	public function get(){

		return array( 'passed' => $this->passed_results , 'failed' => $this->failed_results );

	}
	
	/**
	 * Returns an array of failed tests.
	 * @return array
	 */
	public function get_failures(){
		
		return $this->failed_results;
	}
	
	/**
	 * returns an array of passed tests.
	 * @return array
	 */
	public function get_successes(){
		
		return $this->passed_results;
	}

	/**
	 * returns wether all the test did perform succesfully or not. 
	 * @return boolean
	 */
	public function passed(){

		return $this->passed;
	}

	/**
	 * returns whether there is a failed test 
	 * @return boolean
	 */
	public function failed(){
		return ! $this->passed;
		
	}
}



?>


