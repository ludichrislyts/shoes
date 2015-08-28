<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    //All tests passed
    
    require_once 'src/Store.php';
    require_once 'src/Brand.php';

    $server = 'mysql:host=localhost;dbname=shoes';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class StoreTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
		{
			
		}
	}
?>