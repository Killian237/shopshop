<?php
declare(strict_types = 1);
namespace MyApp\Model;
use MyApp\Entity\Avis;
use PDO;
class AvisModel{
private PDO $db;
public function __construct(PDO $db){
$this->db = $db;
}
public function getAllAvis():array{
$sql = "SELECT * FROM Avis";
$stmt = $this->db->query($sql);
$avis=[];
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
$avis[] = new Avis($row['id'], $row['commentaire'], $row['notes'], $row['name'],);
}
return $avis;
}
public function createAvis(Avis $avis): bool {
    $sql = "INSERT INTO Type (commentaire) VALUES (:commentaire)";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':commentaire', $avis->getCommentaire(), PDO::PARAM_STR);
    return $stmt->execute();
    }
}
