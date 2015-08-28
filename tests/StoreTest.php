<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    //All tests passed
    
    require_once 'src/Store.php';
    require_once 'src/Brand.php';

    $server = 'mysql:host=localhost;dbname=shoes_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class StoreTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
		{
			Store::deleteAll();
            Brand::deleteAll();
		}
    
        function test_save()
        {
            //Arrange
            $name = "Joes Shoe Shack";
            $test_store = new Store($name);
            
            //Act
            $test_store->save();
            
            //Assert
            $result = Store::getAll();
            $this->assertEquals([$test_store], $result);
        }
        function test_addBrand()
        {
            //Arrange
            $name = "Flying Shoes";
            $test_brand = new Brand($name);
            $test_brand->save();
            
            $name = "Joes Shoe Shack";
            $test_store = new Store($name);
            $test_store->save();
            
            //Act
            $test_store->addBrand($test_brand);
            $result = $test_store->getBrands();
            
            //Assert
            $this->assertEquals($test_brand, $result[0]);
        }
    }
?>