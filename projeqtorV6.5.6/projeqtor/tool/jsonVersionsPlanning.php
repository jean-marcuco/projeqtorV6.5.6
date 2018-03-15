<?php
/*
 * @author: qCazelles 
 */
require_once "../tool/projeqtor.php";
scriptLog('   ->/tool/jsonVersionsPlanning.php');

echo '{"identifier":"id", "items":[';

$pvsArray = array();
//CHANGE qCazelles - Correction GANTT - Ticket #100
//Old
// if (isset($_REQUEST['productVersionsListId'])) {
//   $pvsArray = $_REQUEST['productVersionsListId'];
// }
// else {
//   for ($i = 0; $i < $_REQUEST['nbPvs']; $i++) {
//     $pvsArray[$i] = $_REQUEST['pvNo'.$i];
//   }
// }
//New
if (isset($_REQUEST['productVersionsListId'])) {
  if ( strpos($_REQUEST['productVersionsListId'], '_')!==false) {
    $pvsArray=explode('_', $_REQUEST['productVersionsListId']);
  }
  else {
    $pvsArray[]=$_REQUEST['productVersionsListId'];
  }
}
//END CHANGE qCazelles - Correction GANTT - Ticket #100
else {
	for ($i = 0; $i < $_REQUEST['nbPvs']; $i++) {
		$pvsArray[$i] = $_REQUEST['pvNo'.$i];
	}
}

foreach ($pvsArray as $idProductVersion) {
	$productVersion = new ProductVersion($idProductVersion);
	$productVersion->displayVersion();
	
	foreach (ProductVersionStructure::getComposition($productVersion->id) as $idComponentVersion) {
		$componentVersion = new ComponentVersion($idComponentVersion);
		$componentVersion->treatmentVersionPlanning($productVersion);
	}
}

echo ']}';
