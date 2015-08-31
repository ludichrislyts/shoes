<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Brand.php";
    require_once __DIR__."/../src/Store.php";

    $app = new Silex\Application();
    $app['debug'] = true;

    $server = 'mysql:host=localhost;dbname=shoes';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'));

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    //HOME PAGE
    $app->get("/", function() use($app){
        return $app['twig']->render('index.html.twig');
    });
    // DISPLAY BRANDS AND STORES THAT CARRY THEM // ADD BRAND OPTION
    // if user clicks on a brand from this page, take them to that brand page
    $app->get("/brands", function() use ($app){
        $brands = Brand::getAll();
        return $app['twig']->render("brands.html.twig", array('brands' => $brands));
    });
    // DISPLAY ALL STORES WITH BRANDS THEY CARRY // ADD STORE OPTION
    $app->get("/stores", function() use ($app){
        $stores = Store::getAll();
        return $app['twig']->render("stores.html.twig", array('stores' => $stores));
    });
    // SINGLE BRAND ROUTE - DISPLAY SINGLE BRAND PAGE AND OPTION TO ADD A STORE
    // if user adds a store to this brand, add store and reload this page
     $app->get("/brand/{id}", function($id) use($app){
       $message = null;// need this for the already added user input
        $brand = Brand::findById($id);
        $brand_stores = $brand->getStores();
        return $app['twig']->render("brand_info.html.twig", array('brand' => $brand, 'stores' => $brand_stores, 'message' => $message));
        });
    // INDIVIDUAL STORE ROUTE - DISPLAY THIS PAGE WHEN A USER CLICKS ON A STORE
    // Displays brands this store carries, option to add a brand
    $app->get("/store/{id}", function($id) use ($app){
       $message = null;// need this for the already added user message
       $store = Store::findById($id);
       $brands = $store->getBrands();
       return $app['twig']->render("store_info.html.twig", array('store' => $store, 'brands' => $brands, 'message' => $message));
    });
     // BRAND ADDED ROUTE - DISPLAY MAIN BRAND PAGE
     // this page renders after user enters a new brand from individual brand page
    $app->post("/brand_added", function() use ($app){
        $raw_name = $_POST['name'];
        // account for an "'" in the user input (SQL no likey)
        $name = str_replace(["'"], "''", $raw_name);
        $brand_to_add = new Brand($name);
        // get id# if brand already exists.
        $id = $brand_to_add->save();
        if($id == false){
            $brands = Brand::getAll();
            return $app['twig']->render("brands.html.twig", array('brands' => $brands));
        }else{
            $dummy_store = [];
            $brand = Brand::findById($id);
            return $app['twig']->render("entry_exists.html.twig", array('brand' => $brand, 'store' => $dummy_store));
        }
    });
      // STORE ADDED ROUTE - DISPLAY MAIN STORE PAGE
     // this page renders after user enters a new store
    $app->post("/store_added", function() use ($app){
        $raw_name = $_POST['name'];
        // account for an "'" in the user input (SQL no likey)
        $name = str_replace(["'"], "''", $raw_name);
        $new_store = new Store($name);
        $id = $new_store->save();
        if($id == false){
            $stores = Store::getAll();
            return $app['twig']->render("stores.html.twig", array('stores' => $stores));
        }else{
            $dummy_brand = [];
            $store = Store::findById($id);
            return $app['twig']->render("entry_exists.html.twig", array('store' => $store, 'brand' => $dummy_brand));
        }
    });
    // ADDED STORE TO BRAND ROUTE - ADD STORE FROM USER AND REFRESH SINGLE BRAND PAGE
    // if user adds a store to this brand, add store and reload this page
    //checks for multiple entries
    $app->post("/brand/{id}/add_store", function($id) use ($app){
        $already_exists_message = false; // flags to see if in the join table
        $raw_name = $_POST['name'];
        // account for an "'" in the user input (SQL no likey)
        $name = str_replace(["'"], "''", $raw_name);
        $new_store = new Store($name);
        $brand = Brand::findById($id);
        //getting id if already saved
        $exists = $new_store->save();
        // if new entry, proceed as usual
        if($exists == false){
            $brand->addStore($new_store);
        }else{
        // if already saved, check for existence in join table as well
            $db_brands_stores = $brand->getStores();
            // making sure array is not empty so page won't crash
            if(count($db_brands_stores) != 0){
                foreach($db_brands_stores as $sto){
                    $id = $sto->getId();
                    if($exists == $id){ //
                        $already_exists_message = true;
                    }
                }
            }
        }
        // check for not a new store, but not in the join table
        // add to join table
        if($already_exists_message == false && $exists != FALSE){
            $store_to_add_to_join = Store::findById($exists);
            $brand->addStore($store_to_add_to_join);
        }
        $stores = $brand->getStores();
        return $app['twig']->render("brand_info.html.twig", array('stores' => $stores, 'brand' => $brand, 'message' => $already_exists_message));
        });
    // ADD BRAND TO STORE ROUTE
    // ADDS BRAND TO STORE, REFRESHES INDIVIDUAL STORE PAGE
    // checks for multiple entries
    $app->post("/store/{id}/add_brand", function($id) use ($app){
        $already_exists_message = false; //flags to see if in the join table
        $raw_name = $_POST['name'];
        // account for an "'" in the user input (SQL no likey)
        $name = str_replace(["'"], "''", $raw_name);
        $new_brand = new Brand($name);
        $store = Store::findById($id);
        //getting id if already saved
        $exists = $new_brand->save();
        // if new entry, proceed as usual
        if($exists == false){
            $store->addBrand($new_brand);
        }else{
            // if already saved, check for existence in join table as well
            $db_brands_stores = $store->getBrands();
            // making sure array is not empty
            if(count($db_brands_stores) != 0){
                foreach($db_brands_stores as $bnd){
                    $id = $bnd->getId();
                    if($exists == $id){
                        $already_exists_message = true;
                    }
                }
            }
        }
        // check for not a new brand, but also not in the join table
        // add to join table
        if($already_exists_message == false && $exists != false){
            $brand_to_add_to_join = Brand::findById($exists);
            $store->addBrand($brand_to_add_to_join);
        }
        $brands = $store->getBrands();
        return $app['twig']->render("store_info.html.twig", array('store' => $store, 'brands' => $brands, 'message' => $already_exists_message));
    });
    // UPDATE BRAND ROUTE
    // updates name and refreshes individual brand page with updated name
    // must have 'message' variable to send to brand.info page
    $app->patch("/brand/{id}/update", function($id) use ($app){
        $message = null;
        $brand_to_update = Brand::findById($id);
        $brand_to_update->updateName($_POST['new_name']);
        $stores = $brand_to_update->getStores();
        return $app['twig']->render("brand_info.html.twig", array('brand' => $brand_to_update, 'stores' => $stores, 'message' => $message));
    });
    // UPDATE STORE ROUTE
    // updates name and refreshes individual store page with updated name
    // must have 'message' variable to send to store.info page
    $app->patch("/store/{id}/update", function($id) use ($app){
        $message = null;
        $store_to_update = Store::findById($id);
        $store_to_update->updateName($_POST['new_name']);
        $brands = $store_to_update->getBrands();
        return $app['twig']->render("store_info.html.twig", array('store' => $store_to_update, 'brands' => $brands, 'message' => $message));
    });
    // DELETE BRAND ROUTE
    // deletes brand and takes user to confimation page with main links
    $app->delete("brand/{id}/delete", function($id) use ($app){
        $brand_to_delete = Brand::findById($id);
        $name_to_echo = $brand_to_delete->getName();
        $brand_to_delete->delete();
        return $app['twig']->render("confirm_delete.html.twig", array('name' => $name_to_echo, 'brands' => TRUE, 'stores' => FALSE));
    });
    // DELETE STORE ROUTE
    // deletes store and takes user to confimation page with main links
    $app->delete("store/{id}/delete", function($id) use ($app){
        $store_to_delete = Store::findById($id);
        $name_to_echo = $store_to_delete->getName();
        $store_to_delete->delete();
        return $app['twig']->render("confirm_delete.html.twig", array('name' => $name_to_echo, 'stores' => TRUE, 'brands' => FALSE));
    });
    return $app;
?>
