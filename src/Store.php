<?php 
	class Store
	{
		private $name;
		private $id;
		
		function __construct($name, $id = null)
		{
			$this->name = $name;
			$this->id = $id;
		}
		
		function setName($new_name)
		{
			$this->name = (string) $new_name;
		}
	
		function getName()
		{
			return $this->name;
		}
	
		function setId($new_id)
		{
			$this->id = $new_id;
		}
		
		function getId()
		{
			return $this->id;
		}
	
		function save()
		{
			$GLOBALS['DB']->exec("INSERT INTO stores (name) VALUES ('{$this->getName()}');");
			$this->id = $GLOBALS['DB']->lastInsertId();
		}
		//////////////////////////////////////////////////////////
		//////////////// need to change variable names ///////////
		//////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////		
		function addBrand($brand_to_add)
		{
			$GLOBALS['DB']->exec("INSERT INTO brands_stores (brand_id, store_id) VALUES ({$brand_to_add->getId()}, {$this->getId()});");
		}
		
		function getBrands()
		{
			$returned_brands = $GLOBALS['DB']->query("SELECT brands.* FROM stores JOIN brands_stores ON (stores.id = brands_stores.store_id) JOIN brands ON (brands_stores.brand_id = brands.id) WHERE stores.id = {$this->getId()};");
			$brands = [];
			foreach($returned_brands as $brand)
			{
				$name = $brand['name'];
				$id = $brand['id'];
				$new_brand = new Brand($name, $id);
				array_push($brands, $new_brand);
			}
			return $brands;
		}
		//////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////
		static function getAll()
		{
			$returned_stores = $GLOBALS['DB']->query("SELECT * FROM stores;");
			$all_stores = [];
			foreach($returned_stores as $store){
				$name = $store['name'];
				$id = $store['id'];
				$new_store = new Store($name, $id);
				array_push($all_stores, $new_store);
			}
			return $all_stores;
		}
		
		static function findById($id)
		{
			$found = null;
			$all_stores = Store::getAll();
			foreach($all_stores as $store){
				if($store->getId() == $id){
					$found = $store;
				}
			}
			return $found;
		}		
		
		static function deleteAll()
		{
			$GLOBALS['DB']->exec("DELETE FROM stores;");
		}
		
	}
	
	
	
?>