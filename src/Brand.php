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
			$GLOBALS['DB']->exec("INSERT INTO brands (name) VALUES ('{$this->getName()}');");
			$this->id = $GLOBALS['DB']->lastInsertId();
		}
		
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
		
		static function deleteAll()
		{
			$GLOBALS['DB']->exec("DELETE FROM brands;");
		}
		
	}
	
	
	
?>