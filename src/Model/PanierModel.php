<?php
declare(strict_types = 1);
namespace MyApp\Model;
use MyApp\Entity\Panier;
use PDO;

class PanierModel{
private PDO $db;
public function __construct(PDO $db){
$this->db = $db;
}
public function getAllPanier():array{
$sql = "SELECT * FROM Panier";
$stmt = $this->db->query($sql);
$panier=[];
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
$panier[] = new Panier($row['id'], $row['label']);
}
return $panier;
}

public function getOnePanier(int $id): ?Panier
    {
        $sql = "SELECT * from Panier where id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Panier($row['id'], $row['label']);

    }

    public function updatePanier(Panier $panier): bool
    {
        $sql = "UPDATE Panier SET label = :label WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':label', $panier->getLabel(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $panier->getId(), PDO::PARAM_INT);
        return $stmt->execute();

    }
    public function createPanier(Panier $panier): bool
    {
        $sql = "INSERT INTO Panier (label) VALUES (:label)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':label', $panier->getLabel(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deletePanier(int $id): bool
    {
        $sql = "DELETE FROM Panier WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getPanierById(int $id): ?Panier
    {

        $sql = "SELECT * FROM Panier WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Panier($row['id'], $row['label']);
    }


}
