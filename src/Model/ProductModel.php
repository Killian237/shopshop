<?php

declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Product;
use MyApp\Entity\Type;


use PDO;
class ProductModel{
    private PDO $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }

    public function getAllProduct():array{
        $sql = "SELECT p.id as idProduit, name, description, price, stock, t.id as id_Type, label FROM Product p inner join Type t on p.id_Type = t.id order by name";
        $stmt = $this->db->query($sql);
        $product=[];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $type = new Type($row['id_Type'], $row['label']);
            $product[]= new Product($row['idProduit'], $row['name'], $row['description'], $row['price'], $row['stock'], $type);
        }

        return $product;
    }

    public function getAllProduitByType(Type $type): array
    {
        $sql = "SELECT p.id as idProduit, name, description, price, image, t.id as id_Type, label FROM produits p inner join type t on p.type = t.id where type = :type order by name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':type', $type->getId(), PDO::PARAM_INT);
        $stmt -> execute();
        $produits = [];

        while ($row = $stmt->fetch()) {
            $type = new Type($row['id_Type'], $row['label']);
            $produits[] = new Produit($row['idProduit'], $row['name'], $row['description'], $row['price'], $row['image'], $type);
        }

        return $produits;
    } 


    public function createProduct(Product $product): bool{
        $sql = "INSERT INTO Product (name, description, price, stock, id_Type) VALUES (:name, :description, :price, :stock, :id_Type)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $product->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':description', $product->getDescription(), PDO::PARAM_STR);
        $stmt->bindValue(':price', $product->getPrice(), PDO::PARAM_STR);
        $stmt->bindValue(':stock', $product->getStock(), PDO::PARAM_INT);
        $stmt->bindValue(':id_Type', $product->getType()->getId(), PDO::PARAM_INT);
 
        return $stmt->execute();
    }
    

}