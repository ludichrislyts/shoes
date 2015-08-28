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
    
    // SINGLE BRAND ROUTE - DISPLAY SINGLE BRAND PAGE AND OPTION TO ADD A STORE
    // if user adds a store to this brand, add store and reload this page
     $app->get("/brand/{id}", function($id) use($app){
        $brand = Brand::findById($id);
        $brand_stores = $brand->getStores();
        return $app['twig']->render("brand_info.html.twig", array('brand' => $brand, 'stores' => $brand_stores));
        });
     // BRAND ADDED ROUTE - DISPLAY MAIN BRAND PAGE 
     // this page renders after user enters a new brand   
    $app->post("/brand_added", function() use ($app){
        $name = $_POST['name'];
        $new_brand = new Brand($name);
        $new_brand->save();
        $brands = Brand::getAll();
        return $app['twig']->render("brands.html.twig", array('brands' => $brands));
    });
    // ADDED STORE TO BRAND ROUTE - ADD STORE FROM USER AND REFRESH SINGLE BRAND PAGE
    // if user adds a store to this brand, add store and reload this page
    $app->post("/brand/{id}/add_store", function($id) use ($app){
        // create new store from user entry "add store"
        $name = $_POST['name'];
        $new_store = new Store($name);
        $new_store->save();
        // create new author from user entry "add store"
        // possibly check that the author is already in the database - NOT NOW
        //$new_store->addAuthor($new_author);
        $brand = Brand::findById($id);
        $brand->findById($id);
        $stores = $brand->getStores();
        //var_dump($authors);
        return $app['twig']->render("brand_info.html.twig", array('stores' => $stores, 'brand' => $brand));
        });    

    return $app;
?>
