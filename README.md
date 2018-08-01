# Unit_Tester
Simple class to perform unit tests from php scripts.
=================================

[![License](https://poser.pugx.org/leaphly/cart-bundle/license.png)](https://packagist.org/packages/leaphly/cart-bundle)


## Author

Mauro Caffaratto

## License

licensed under the MIT and GPL licenses


## Dependencies
php 5.3 or > 

# description
  Unit_tester is a small class that performs unit tests from php scripts. While in production and big projects I would stick 	to   PHPunit, this is an easier class to use for newbye php programmers, or use with small php scripts, since doesn't relay in any
  .phar or depends on command line php, and doesn`t need setup at all.
  
# usage:
	
```php

$tester = new Unit_tester();

$saviour_of_all_humans = 'Son Goku';

//make some test using our tester:
$tester->set_current_method(array('strlen'));
$tester->set_current_params(array($saviour_of_all_humans));

$tester->assert_equals(8);


$tester->set_current_method(array('strpos'));
$tester->set_current_params(array($saviour_of_all_humans , 'egeta'));

$tester->assert_non_equals();

//fetch the results:
$results = $tester->get_results();

//fetch errors if finded
if($results->failed() )
	$data = $results->get_failures();
	
//and voilá.
//see the examples.php file to get used to all the tester methods. 




	
	
 
