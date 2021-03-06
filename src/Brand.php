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
			$brand_check = null;
			$brand_check = Brand::findByName($this->getName());
			if($brand_check == null){
				$GLOBALS['DB']->exec("INSERT INTO brands (name) VALUES ('{$this->getName()}');");
				$this->id = $GLOBALS['DB']->lastInsertId();
				return false;
			}else{
				return $brand_check;
			}
		}

		function updateName($new_name)
		{
			$GLOBALS['DB']->exec("UPDATE brands SET name = '{$new_name}' WHERE id = {$this->getId()};");
			$this->name = $new_name;
		}

		function delete()
		{
			$GLOBALS['DB']->exec("DELETE FROM brands WHERE id = {$this->getid()};");
			$GLOBALS['DB']->exec("DELETE FROM brands_stores
				WHERE brand_id = {$this->getId()};");
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
		/////////////////// STATIC FUNCTIONS /////////////////////
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

		//returns brand id
		static function findByName($search_name)
		{
			// if apostrophe in the name, modify search to account for 
			// single/double quote SQL issue
			$single_quote_name = str_replace(["''"], "'", $search_name);
			$found_brand_id = null;
			$db_brands = $GLOBALS['DB']->query("SELECT * FROM brands WHERE name = '{$search_name}';");
			if(!empty($db_brands)){
				foreach($db_brands as $brand){
					$name = $brand['name'];
					if($name == $single_quote_name){
						$found_brand_id = $brand['id'];
					}
				}				
			}
			return $found_brand_id;
		}

		static function deleteAll()
		{
			$GLOBALS['DB']->exec("DELETE FROM brands;");
		}
	}
?>
