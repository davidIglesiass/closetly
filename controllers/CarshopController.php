<?php

require_once 'models/ProductRepository.php';

class CarshopController {
    public function index() {
        $carshop = isset($_SESSION["carshop"]) && count($_SESSION["carshop"]) > 0 ? $_SESSION["carshop"] : array() ;
        require_once 'views/carshop/index.php';
    }

    public function add(){
        if(!isset($_GET["id"])){
            header("Location: /");
            exit;
        }
        $idProduct = $_GET["id"];
        if(isset($_SESSION["carshop"])){
            $i = 0;
            foreach($_SESSION["carshop"] as $index => $product){
                if($product["id"] == $idProduct){
                    $_SESSION["carshop"][$index]["units"]++;
                    $i++;
                }
            }
        }
        if(!isset($i) || $i == 0){
            $product = (new ProductRepository())->find($idProduct);

            $_SESSION["carshop"][] = array(
                "id" => $product->id,
                "name" => $product->name,
                "price" => $product->price,
                "units" => 1,
                "product" => $product
            ); 
        }

        header("Location: /carshop/index");
    }

    public function remove(){
        if(isset($_GET["index"], $_SESSION["carshop"][$_GET["index"]])){
            unset($_SESSION["carshop"][$_GET["index"]]);
        }
        header("Location: /carshop/index");
    }

    public function delete(){
        unset($_SESSION["carshop"]);
        header("Location: /carshop/index");
        return;
    }

    public function up(){
        $index = $_GET["index"] ?? null;
        if(isset($_SESSION["carshop"][$index])){
            $_SESSION["carshop"][$index]["units"]++;
        }
        header("Location: /carshop/index");
    }

    public function down(){
        $index = $_GET["index"] ?? null;
        if(isset($_SESSION["carshop"][$index])){
            $_SESSION["carshop"][$index]["units"]--;
            if($_SESSION["carshop"][$index]["units"] <= 0) unset($_SESSION["carshop"][$index]);
        }
        header("Location: /carshop/index");
    }
}