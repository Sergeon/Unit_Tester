<?php

require('unit_tester.php');

$str = "Anaander Mianaai";

$tester = new Unit_Tester();

//examples passing simple function names:
$tester->set_current_method('strlen');
$tester->set_current_params(array($str));

$tester->assert_non_equals(3);


$tester->set_current_method('strpos');
$tester->set_current_params(array($str , 'xyz' ));

$tester->assert_equals(false);

$tester->set_current_method('strpos');
$tester->set_current_params(array($str , 'aai' ));

$tester->assert_equals(true);


$now = new DateTime();
//examples passing objects:
$tester->set_current_method(array($now , 'format'));
$tester->set_current_params(array("Y-m-d"));

$tester->assert_non_equals( "1999-12-12");

//just for example purposes:
class StrTool {
	
	public static function contains($haystack , $value){
		
		$pos = strpos( $haystack , $value );
		if($pos === false)
			return false;
		
		return true;
	}
	
	
	public static function exception(){
		throw new Exception("foo : bar");
	}
	
	
	
}

//passing class Name and static method:
$tester->set_current_method( 'StrTool::contains' );
$tester->set_current_params(array($str , 'ander' ));

$tester->assert_equals( true );

$tester->set_current_method(array('StrTool' , 'exception'));
//test method throws Exception:
$tester->assert_throws_exception();


$absurd_object = new StrTool();
//is-a test:
$tester->assert_is_a($absurd_object, 'StrTool');



//fetch all the tests results.
$results = $tester->get_results();


if( $results->failed() )
	$data = $results->get_failures();
	


