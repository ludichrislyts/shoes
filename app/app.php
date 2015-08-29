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
        $brand = Brand::findById($id);
        $brand_stores = $brand->getStores();
        return $app['twig']->render("brand_info.html.twig", array('brand' => $brand, 'stores' => $brand_stores));
        });
    // INDIVIDUAL STORE ROUTE - DISPLAY THIS PAGE WHEN A USER CLICKS ON A STORE
    // Displays brands this store carries, option to add a brand
    $app->get("/store/{id}", function($id) use ($app){
       $store = Store::findById($id);
       $brands = $store->getBrands();
       return $app['twig']->render("store_info.html.twig", array('store' => $store, 'brands' => $brands)); 
    });
     // BRAND ADDED ROUTE - DISPLAY MAIN BRAND PAGE 
     // this page renders after user enters a new brand   
    $app->post("/brand_added", function() use ($app){
        $name = $_POST['name'];
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
 /////////////////////////////////////////////////////////////////////   
 /////////////////////////////////////////////////////////////////////  
      // STORE ADDED ROUTE - DISPLAY MAIN STORE PAGE 
     // this page renders after user enters a new store   
    $app->post("/store_added", function() use ($app){
        $name = $_POST['name'];
        $new_store = new Store($name);
        $id = $new_store->save();
        var_dump($id);
        if($id == false){
            $stores = Store::getAll();
            return $app['twig']->render("stores.html.twig", array('stores' => $stores));
        }else{
            $dummy_brand = [];
            $store = Store::findById($id);
            return $app['twig']->render("entry_exists.html.twig", array('store' => $store, 'brand' => $dummy_brand));
        }
    }); 
  /////////////////////////////////////////////////////////////////////   
   /////////////////////////////////////////////////////////////////////   
    // ADDED STORE TO BRAND ROUTE - ADD STORE FROM USER AND REFRESH SINGLE BRAND PAGE
    // if user adds a store to this brand, add store and reload this page
    $app->post("/brand/{id}/add_store", function($id) use ($app){
        // create new store from user entry "add store"
        $name = $_POST['name'];
        $new_store = new Store($name);
        $new_store->save();
        $brand = Brand::findById($id);
        $brand->addStore($new_store);
        $stores = $brand->getStores();
        return $app['twig']->render("brand_info.html.twig", array('stores' => $stores, 'brand' => $brand));
        });    
    // ADD BRAND TO STORE ROUTE
    // ADDS BRAND TO STORE, REFRESHES INDIVIDUAL STORE PAGE
    $app->post("/store/{id}/add_brand", function($id) use ($app){
        $name = $_POST['name'];
        $new_brand = new Brand($name);
        $new_brand->save();
        $store = Store::findById($id);
        $store->addBrand($new_brand);
        $brands = $store->getBrands();
        return $app['twig']->render("store_info.html.twig", array('store' => $store, 'brands' => $brands));
    });


    return $app;
?>
