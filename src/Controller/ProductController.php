<?php

declare (strict_types = 1);

namespace MyApp\Controller;

use MyApp\Model\ProductModel;
use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Product;
use MyApp\Model\TypeModel;

class ProductController{
    private $twig;
    private ProductModel $productModel;
    private TypeModel $typeModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer){
        $this->twig = $twig;
        $this->productModel = $dependencyContainer->get('ProductModel');
        $this->typeModel = $dependencyContainer->get('TypeModel');
    }

    public function listProducts(){
        $products = $this->productModel->getAllProducts();
        echo $this->twig->render('productController/listProducts.html.twig',['products'=>$products]);
    }

    public function addProduct(){
        $types = $this->typeModel->getAllTypes();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT);
        $idType = filter_input(INPUT_POST, 'id_Type', FILTER_SANITIZE_NUMBER_INT);
        if (!empty($name) && !empty($description) && !empty($price) && !empty($stock) && !empty($idType)) {
            $type = $this->typeModel->getTypeById(intVal($idType));
            if ($type == null) {
                $_SESSION['message'] = 'Erreur sur le type.';
            } else {
                $product = new Product(null, $name, $description, floatVal($price),intVal($stock), $type);
                $success = $this->productModel->createProduct($product);
            }
        } else {
            $_SESSION['message'] = 'Veuillez saisir toutes les données.';
        }
        } 
        echo $this->twig->render('productController/addProduct.html.twig', ['types' => $types]);
    }





}