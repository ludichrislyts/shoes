<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    //All tests passed
    
    require_once 'src/Brand.php';
    require_once 'src/Store.php';

    $server = 'mysql:host=localhost;dbname=shoes';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class BrandTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
		{
			Brand::deleteAll();
		}
    
        function test_save()
        {
            //Arrange
            $name = "Flying Shoes";
            $test_brand = new Brand($name);
            
            //Act
            $test_brand->save();
            
            //Assert
            $result = Brand::getAll();
            $this->assertEquals([$test_brand], $result);
        }
	}
        
?>