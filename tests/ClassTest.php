<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    //All tests passed
    
    require_once 'src/.php';
    require_once 'src/.php';

    $server = 'mysql:host=localhost;dbname=univ_registrar_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class CourseTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
		{
			
		}
	}
?>