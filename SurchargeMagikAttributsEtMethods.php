<?php


class SurchargeMagikAttributsEtMethods
{

    private $attributs = array();
    private $autreAttribut;

    // __SET
    public function __set($nom, $valeur): SurchargeMagikAttributsEtMethods
    {
        $this->attributs[][$nom] = $valeur;
        return $this;
    }

    // __GET
    public function __get($nom): string
    {
        foreach($this->attributs as $id => $value) {
            if (isset($this->attributs[$id][$nom])) {
                return "L'attribut : <mark>" . $nom . "</mark> a été précédemment SETtait (index du tableau : " . $id . " :) : <br>";
            }
        }
        return 'Impossible d\'accéder à l\'attribut <strong>' . $nom . '</strong>, désolé! <br />';
    }

    public function afficherAttributs() {
        echo '<pre>', print_r($this->attributs, true), '</pre>';
    }
}

$obj = new SurchargeMagikAttributsEtMethods();
// Accès à la surcharge magique __SET
$obj->unAttribut = "Attribut SETtait magiquement.";
$obj->unAttributDos = "Lui non plus n'existe pas !";
$obj->unAttributTres = "Tien encore un qui n'existe pas !";
$obj->unAutreAttribut = "Bon bah un autre qui n'existe toujours pas";
// afficher la liste des attributs "SETaient" magiquement
$obj->afficherAttributs();

// Accès à la surcharge magique __GET
echo $obj->unAttribut;
echo $obj->pouetpouet;
echo $obj->unAutreAttribut;

include('backHome.php');