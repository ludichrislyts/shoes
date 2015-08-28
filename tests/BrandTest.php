<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    //All tests passed
    
    require_once 'src/Brand.php';
    require_once 'src/Store.php';

    $server = 'mysql:host=localhost;dbname=shoes_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class BrandTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
		{
			Brand::deleteAll();
            Store::deleteAll();
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
        
        function test_addStore()
        {
            //Arrange
            $name = "Flying Shoes";
            $test_brand = new Brand($name);
            $test_brand->save();
            
            $name = "Joes Shoe Shack";
            $test_store = new Store($name);
            $test_store->save();
            
            //Act
            $test_brand->addStore($test_store);
            $result = $test_brand->getStores();
            
            //Assert
            $this->assertEquals($test_store, $result[0]);
        }
	}
        
?>