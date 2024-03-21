<?php
declare(strict_types = 1);
namespace MyApp\Entity;
class Avis{
private ?int $id = null;
private string $commentaire;
public function __construct(?int $id, string $name, int $notes, string $commentaire ){
$this->id = $id;
$this->commentaire = $commentaire;
}
public function getId():?int{
    return $this->id;
}
public function setId(?int $id):void{
    $this->id = $id;
}
public function getCommentaire():string{
    return $this->commentaire;
}
public function setCommentaire(string $commentaire):void{
    $this->commentaire = $commentaire;
}
public function getNotes():?int{
    return $this->notes;
    }
public function setNotes(?int $notes):void{
    $this->notes = $notes;
    }
public function getName():string{
    return $this->name;
    }
public function setName(string $name):void{
    $this->name = $name;
    }

}
