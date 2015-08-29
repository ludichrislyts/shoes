<?php 
	class Brand
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
			//check database for existing name. if not returned, add brand.
			// if name is found, return brand id#
			$brand_check = 0;
			$find_brand = $GLOBALS['DB']->query("SELECT * FROM brands WHERE brands.name = '{$this->getName()}';");
			//var_dump($brand_check);
			foreach($find_brand as $bnd){
				$name = $bnd['name'];
				$id = $bnd['id'];
				$brand = new Brand($name, $id);
				++$brand_check; 
			}
			if($brand_check == 0){
				echo "saved";
				$GLOBALS['DB']->exec("INSERT INTO brands (name) VALUES ('{$this->getName()}');");
				$this->id = $GLOBALS['DB']->lastInsertId();
				return false;
			}else{
				echo "already there";
				return $id;
			}			
		}
		
		function addStore($store_to_add)
		{
			$GLOBALS['DB']->exec("INSERT INTO brands_stores (brand_id, store_id) VALUES ({$this->getId()}, {$store_to_add->getId()});");
		}
		
		function getStores()
		{
			$returned_stores = $GLOBALS['DB']->query("SELECT stores.* FROM brands JOIN brands_stores ON (brands.id = brands_stores.brand_id) JOIN stores ON (brands_stores.store_id = stores.id) WHERE brands.id = {$this->getId()};");
			$stores = [];
			foreach($returned_stores as $store)
			{
				$name = $store['name'];
				$id = $store['id'];
				$new_store = new Store($name, $id);
				array_push($stores, $new_store);
			}
			return $stores;
		}
		//////////////////////////////////////////////////////////

		//////////////////////////////////////////////////////////
		static function getAll()
		{
			$returned_brands = $GLOBALS['DB']->query("SELECT * FROM brands;");
			$all_brands = [];
			foreach($returned_brands as $brand){
				$name = $brand['name'];
				$id = $brand['id'];
				$new_brand = new Brand($name, $id);
				array_push($all_brands, $new_brand);
			}
			return $all_brands;
		}
		
		static function findById($id)
		{
			$found = null;
			$all_brands = Brand::getAll();
			foreach($all_brands as $brand){
				if($brand->getId() == $id){
					$found = $brand;
				}
			}
			return $found;
		}
		
		static function deleteAll()
		{
			$GLOBALS['DB']->exec("DELETE FROM brands;");
		}
		
	}
	
	
	
?>