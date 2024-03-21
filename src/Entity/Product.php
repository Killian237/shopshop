<?php

declare(strict_types = 1);

namespace MyApp\Entity;


use MyApp\Entity\Type;

class Product{

    private ?int $id = null;
    private string $name;
    private string $description;
    private float $price;
    private int $stock;
    private Type $type;


    public function __construct(?int $id, string $name, string $description, float $price, int $stock, Type $type){
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->stock = $stock;
        $this->type = $type;

    }

    public function setId(?int $id):void{
        $this->id = $id;
    }
    public function getId():?int{
        return $this->id;
    }

    public function getname():string{
        return $this->name;
    }
    public function setname(string $name):void{
        $this->name = $name;
    }
    public function getdescription():string{
        return $this->description;
    }
    public function setdescription(string $description):void{
        $this->description = $description;
    }
    public function getprice():float{
        return $this->price;
    }
    public function setprice(float $price):void{
        $this->price = $price;
    }
    public function getStock(): int{
        return $this->stock;
    }
    public function setStock(int $stock): void{
        $this->stock = $stock;
    }
    public function getType(): Type{
        return $this->type;
    }
    public function setType(Type $type): void{
        $this->type = $type;
    }
    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }
}