<?php
require_once("classes/AccesAuxDonneesDev.classe.php");
$filter=$_POST["filter"];
$maBase = AccesAuxDonneesDev::recupAccesDonneesDev();
$result=$maBase->tagIllustrateur($filter);
foreach ($result as $row){
	$array[]=$row[NOM_ILLUSTRATEUR];
}
echo json_encode($array);
?>