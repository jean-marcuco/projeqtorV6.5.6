<?php
/*
 * @author: atrancoso ticket #84
 */
include_once '../tool/projeqtor.php';

include ("../external/pChart/pData.class");
include ("../external/pChart/pChart.class");

$paramProduct = '';
if (array_key_exists ( 'idProduct', $_REQUEST )) {
  $paramProduct = trim ( $_REQUEST ['idProduct'] );
  $paramProduct = Security::checkValidId ( $paramProduct ); // only allow digits
}
;

$paramVersion = '';
if (array_key_exists ( 'idVersion', $_REQUEST )) {
  $paramVersion = trim ( $_REQUEST ['idVersion'] );
  $paramVersion = Security::checkValidId ( $paramVersion ); // only allow digits
}
;

$paramMonth = '';
if (array_key_exists ( 'monthSpinner', $_REQUEST )) {
  $paramMonth = $_REQUEST ['monthSpinner'];
  $paramMonth = Security::checkValidMonth ( $paramMonth );
}
;

$paramProject = '';
if (array_key_exists ( 'idProject', $_REQUEST )) {
  $paramProject = trim ( $_REQUEST ['idProject'] );
  $paramProject = Security::checkValidId ( $paramProject ); // only allow digits
}
;

$paramYear = '';
if (array_key_exists ( 'yearSpinner', $_REQUEST )) {
  $paramYear = $_REQUEST ['yearSpinner'];
  $paramYear = Security::checkValidYear ( $paramYear );
}
;

$paramPriorities = array();
if (array_key_exists ( 'priorities', $_REQUEST )) {
  foreach ( $_REQUEST ['priorities'] as $idPriority => $boolean ) {
    $paramPriorities [] = $idPriority;
  }
}

$periodType = 'year';
// $periodValue=$_REQUEST['periodValue'];
$periodValue = $paramYear;

// Header
$headerParameters = "";

if ($periodType=='year' and $paramMonth!="01") {
  if(!$paramMonth){
    $paramMonth="01";
  }
  $headerParameters.= i18n("startMonth") . ' : ' . i18n(date('F', mktime(0,0,0,$paramMonth,10))) . '<br/>';
}
if ($periodType=='month') {
  $headerParameters.= i18n("month") . ' : ' . $paramMonth . '<br/>';
}
if ( $periodType=='week') {
  $headerParameters.= i18n("week") . ' : ' . $paramWeek . '<br/>';
}

if ($paramProject != "") {
  $headerParameters .= i18n ( "colIdProject" ) . ' : ' . htmlEncode ( SqlList::getNameFromId ( 'Project', $paramProject ) ) . '<br/>';
}

if ($periodType == 'month') {
  $headerParameters .= i18n ( "month" ) . ' : ' . $paramMonth . '<br/>';
}
if ($paramProduct != "") {
  $headerParameters .= i18n ( "colIdProduct" ) . ' : ' . htmlEncode ( SqlList::getNameFromId ( 'Product', $paramProduct ) ) . '<br/>';
}

if ($paramVersion != "") {
  $headerParameters .= i18n ( "colVersion" ) . ' : ' . htmlEncode ( SqlList::getNameFromId ( 'Version', $paramVersion ) ) . '<br/>';
}
if ($periodType == 'year' or $periodType == 'month' or $periodType == 'week') {
  $headerParameters .= i18n ( "year" ) . ' : ' . $paramYear . '<br/>';
}

if (! empty ( $paramPriorities )) {
  $priority = new Priority ();
  $priorities = $priority->getSqlElementsFromCriteria ( null, false, null, 'id asc' );
  
  $prioritiesDisplayed = array();
  for($i = 0; $i < count ( $priorities ); $i ++) {
    if (in_array ( $i + 1, $paramPriorities )) {
      $prioritiesDisplayed [] = $priorities [$i];
    }
  }
  
  $headerParameters .= i18n ( "colPriority" ) . ' : ';
  foreach ( $prioritiesDisplayed as $priority ) {
    $headerParameters .= $priority->name . ', ';
  }
  $headerParameters = substr ( $headerParameters, 0, - 2 );
  
  if (in_array ( 'undefined', $paramPriorities )) {
    $headerParameters .= ', ' . i18n ( 'undefinedPriority' );
  }
}
include "header.php";

if(!$paramMonth){
  $paramMonth="01";
}

$includedReport = true;
// //////////////////////////////////////////////////////////////////////////////////////////////////////
$where = getAccesRestrictionClause ( 'Requirement', false );

$where .= " and ( (    creationDateTime>= '" . $paramYear . "-$paramMonth-01'";
$where .= "        and creationDateTime<='" . ($paramYear + 1) . "-" . ($paramMonth - 1) . "-31' ) )";
if ($paramProject != "") {
  $where .= " and idProject in " . getVisibleProjectsList ( false, $paramProject );
}

if (isset ( $paramProduct ) and $paramProduct != "") {
  $where .= " and idProduct='" . Sql::fmtId ( $paramProduct ) . "'";
}

if (isset ( $paramVersion ) and $paramVersion != "") {
  $where .= " and idOriginalProductVersion='" . Sql::fmtId ( $paramVersion ) . "'";
}

$filterByPriority = false;
if (! empty ( $paramPriorities ) and $paramPriorities [0] != 'undefined') {
  $filterByPriority = true;
  $where .= " and idPriority in (";
  foreach ( $paramPriorities as $idDisplayedPriority ) {
    if ($idDisplayedPriority == 'undefined')
      continue;
    $where .= $idDisplayedPriority . ', ';
  }
  $where = substr ( $where, 0, - 2 ); // To remove the last comma and space
  $where .= ")";
}
if ($filterByPriority and in_array ( 'undefined', $paramPriorities )) {
  $where .= " or idPriority is null";
} else if (in_array ( 'undefined', $paramPriorities )) {
  $where .= " and idPriority is null";
} else if ($filterByPriority) {
  $where .= " and idPriority is not null";
}
// ////////////////////////////////////////////////////////////////////////////////////////////////////////////
$whereC = getAccesRestrictionClause ( 'Requirement', false );

$whereC .= " and ( (    idleDate>= '" . $paramYear . "-$paramMonth-01'";
$whereC .= "        and idleDate<='" . ($paramYear + 1) . "-" . ($paramMonth - 1) . "-31' ) )";
if ($paramProject != "") {
  $whereC .= " and idProject in " . getVisibleProjectsList ( false, $paramProject );
}

if (isset ( $paramProduct ) and $paramProduct != "") {
  $whereC .= " and idProduct='" . Sql::fmtId ( $paramProduct ) . "'";
}

if (isset ( $paramVersion ) and $paramVersion != "") {
  $whereC .= " and idOriginalProductVersion='" . Sql::fmtId ( $paramVersion ) . "'";
}

$filterByPriority = false;
if (! empty ( $paramPriorities ) and $paramPriorities [0] != 'undefined') {
  $filterByPriority = true;
  $whereC .= " and idPriority in (";
  foreach ( $paramPriorities as $idDisplayedPriority ) {
    if ($idDisplayedPriority == 'undefined')
      continue;
    $whereC .= $idDisplayedPriority . ', ';
  }
  $whereC = substr ( $where, 0, - 2 ); // To remove the last comma and space
  $whereC .= ")";
}
if ($filterByPriority and in_array ( 'undefined', $paramPriorities )) {
  $whereC .= " or idPriority is null";
} else if (in_array ( 'undefined', $paramPriorities )) {
  $whereC .= " and idPriority is null";
} else if ($filterByPriority) {
  $whereC .= " and idPriority is not null";
}
// /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$whereD = getAccesRestrictionClause ( 'Requirement', false );

$whereD .= " and ( (    doneDate >= '" . $paramYear . "-$paramMonth-01'";
$whereD .= "        and doneDate <= '" . ($paramYear + 1) . "-" . ($paramMonth - 1) . "-31' ) )";
if ($paramProject != "") {
  $whereD .= " and idProject in " . getVisibleProjectsList ( false, $paramProject );
}

if (isset ( $paramProduct ) and $paramProduct != "") {
  $whereD .= " and idProduct='" . Sql::fmtId ( $paramProduct ) . "'";
}

if (isset ( $paramVersion ) and $paramVersion != "") {
  $whereD .= " and idOriginalProductVersion='" . Sql::fmtId ( $paramVersion ) . "'";
}

$filterByPriority = false;
if (! empty ( $paramPriorities ) and $paramPriorities [0] != 'undefined') {
  $filterByPriority = true;
  $whereD .= " and idPriority in (";
  foreach ( $paramPriorities as $idDisplayedPriority ) {
    if ($idDisplayedPriority == 'undefined')
      continue;
      $whereD .= $idDisplayedPriority . ', ';
  }
  $whereD = substr ( $where, 0, - 2 ); // To remove the last comma and space
  $whereD .= ")";
}
if ($filterByPriority and in_array ( 'undefined', $paramPriorities )) {
  $whereD .= " or idPriority is null";
} else if (in_array ( 'undefined', $paramPriorities )) {
  $whereD .= " and idPriority is null";
} else if ($filterByPriority) {
  $whereD .= " and idPriority is not null";
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
$order = "";
$req = new Requirement ();
$lstReq = $req->getSqlElementsFromCriteria ( null, false, $where, $order );
$created = array();
$closed = array();
for($i = 1; $i <= 13; $i ++) {
  $created [$i] = 0;
  $closed [$i] = 0;
  $done[$i] = 0;
}
$sumProj = array();
foreach ( $lstReq as $t ) {
  if (substr ( $t->creationDateTime, 0, 4 ) == $paramYear or substr ( $t->creationDateTime, 0, 4 ) == ($paramYear + 1)) {
    $month = intval ( substr ( $t->creationDateTime, 5, 2 ) );
    if (substr ( $t->creationDateTime, 0, 4 ) == $paramYear) {
      $created [$month - $paramMonth + 1] += 1;
    } else if (substr ( $t->creationDateTime, 0, 4 ) == $paramYear + 1) {
      if (($month - $paramMonth) > 0) {
        $created [$month - $paramMonth] += 1;
      } else {
        $created [$month + 13 - $paramMonth] += 1;
      }
    }
  }
}

$orderC = "";
$reqC = new Requirement ();
$lstReqC = $reqC->getSqlElementsFromCriteria ( null, false, $whereC, $orderC );
foreach ( $lstReqC as $k ) {
  $month = intval ( substr ( $k->idleDate, 5, 2 ) );
  if (substr ( $k->idleDate, 0, 4 ) == $paramYear or substr ( $k->idleDate, 0, 4 ) == ($paramYear + 1)) {
    if (substr ( $k->idleDate, 0, 4 ) == $paramYear and $month >= $paramMonth) {
      $closed [$month - $paramMonth + 1] += 1;
    } else if (substr ( $k->idleDate, 0, 4 ) == $paramYear + 1) {
      if (($month - $paramMonth) > 0) {
        $closed [$month - $paramMonth] += 1;
      } else {
        $closed [$month + 13 - $paramMonth] += 1;
      }
    }
  }
}

  $orderD = "";
  $reqD = new Requirement ();
  $lstReqD = $reqD->getSqlElementsFromCriteria ( null, false, $whereD, $orderD );
  foreach ( $lstReqD as $d ) {
    $month = intval ( substr ( $d->doneDate, 5, 2 ) );
    if (substr ( $d->doneDate, 0, 4 ) == $paramYear or substr ( $d->doneDate, 0, 4 ) == ($paramYear + 1)) {
      if (substr ( $d->doneDate, 0, 4 ) == $paramYear and $month >= $paramMonth) {
        $done [$month - $paramMonth + 1] += 1;
      } else if (substr ( $d->doneDate, 0, 4 ) == $paramYear + 1) {
        if (($month - $paramMonth) > 0) {
          $done[$month - $paramMonth] += 1;
        } else {
          $done[$month + 13 - $paramMonth] += 1;
        }
      }
    }
  }
  if (checkNoData ( $lstReq ) and checkNoData ( $lstReqC) and checkNoData ( $lstReqD))
    return;

// title;

$arrMonth [0] = getMonth ( 4, ($paramMonth - 1) % 12, true );
$arrMonth [1] = getMonth ( 4, ($paramMonth + 0) % 12, true );
$arrMonth [2] = getMonth ( 4, ($paramMonth + 1) % 12, true );
$arrMonth [3] = getMonth ( 4, ($paramMonth + 2) % 12, true );
$arrMonth [4] = getMonth ( 4, ($paramMonth + 3) % 12, true );
$arrMonth [5] = getMonth ( 4, ($paramMonth + 4) % 12, true );
$arrMonth [6] = getMonth ( 4, ($paramMonth + 5) % 12, true );
$arrMonth [7] = getMonth ( 4, ($paramMonth + 6) % 12, true );
$arrMonth [8] = getMonth ( 4, ($paramMonth + 7) % 12, true );
$arrMonth [9] = getMonth ( 4, ($paramMonth + 8) % 12, true );
$arrMonth [10] = getMonth ( 4, ($paramMonth + 9) % 12, true );
$arrMonth [11] = getMonth ( 4, ($paramMonth + 10) % 12, true );
$arrMonth [13] = i18n ( 'sum' );
$sum = 0;
for($line = 1; $line <= 2; $line ++) {
  if ($line == 1) {
    $tab = $created;
    $caption = i18n ( 'created' );
    $serie = "created";
  } else if ($line == 2) {
    $tab = $closed;
    $caption = i18n ( 'closed' );
    $serie = "closed";
  }
  else if ($line == 3) {
    $tab = $done;
    $caption = i18n ( 'done' );
    $serie = "done";
  }
}

// Render graph
// pGrapg standard inclusions
if (! testGraphEnabled ()) {
  return;
}

$dataSet = new pData ();
$createdSum = array('', '', '', '', '', '', '', '', '', '', '', '', $created [13]);
$created [13] = "";
$closedSum = array('', '', '', '', '', '', '', '', '', '', '', '', $closed [13]);
$closed [13] = "";
$doneSum = array('', '', '', '', '', '', '', '', '', '', '', '', $done [13]);
$done [13] = "";
$rightScale = array('', '', '', '', '', '', '', '', '', '', '', '', i18n ( 'sum' ));
$dataSet->AddPoint ( $created, "created" );
$dataSet->SetSerieName ( i18n ( "created" ), "created" );
$dataSet->AddSerie ( "created" );
$dataSet->AddPoint ( $closed, "closed" );
$dataSet->SetSerieName ( i18n ( "closed" ), "closed" );
$dataSet->AddSerie ( "closed" );
$dataSet->AddPoint ( $done, "done" );
$dataSet->SetSerieName ( i18n ( "done" ), "done" );
$dataSet->AddSerie ( "done" );
$arrMonth [13] = "";
$dataSet->AddPoint ( $arrMonth, "months" );
$dataSet->SetAbsciseLabelSerie ( "months" );
// Initialise the graph
$width = 700;

$graph = new pChart ( $width, 230 );
$graph->setFontProperties ( "../external/pChart/Fonts/tahoma.ttf", 10 );
// $graph->drawFilledRoundedRectangle(7,7,$width-7,223,5,240,240,240);
$graph->drawRoundedRectangle ( 5, 5, $width - 5, 225, 5, 230, 230, 230 );

$graph->setColorPalette ( 0, 200, 100, 100 );
$graph->setColorPalette ( 1, 100, 200, 100 );
$graph->setColorPalette ( 2, 100, 100, 200 );
$graph->setColorPalette ( 3, 200, 100, 100 );
$graph->setColorPalette ( 4, 100, 200, 100 );
$graph->setColorPalette ( 5, 100, 100, 200 );
$graph->setGraphArea ( 40, 30, $width - 140, 200 );
$graph->drawGraphArea ( 252, 252, 252 );
$graph->setFontProperties ( "../external/pChart/Fonts/tahoma.ttf", 8 );
$graph->drawScale ( $dataSet->GetData (), $dataSet->GetDataDescription (), SCALE_START0, 0, 0, 0, TRUE, 0, 1, true );
$graph->drawGrid ( 5, TRUE, 230, 230, 230, 255 );

// Draw the line graph
$graph->drawFilledLineGraph ( $dataSet->GetData (), $dataSet->GetDataDescription (), 30, true );
$graph->drawLineGraph ( $dataSet->GetData (), $dataSet->GetDataDescription () );
$graph->drawPlotGraph ( $dataSet->GetData (), $dataSet->GetDataDescription (), 3, 2, 255, 255, 255 );

// Finish the graph
$graph->setFontProperties ( "../external/pChart/Fonts/tahoma.ttf", 8 );
$graph->drawLegend ( $width - 100, 35, $dataSet->GetDataDescription (), 240, 240, 240 );
// $graph->setFontProperties("../external/pChart/Fonts/tahoma.ttf",10);
// $graph->drawTitle(60,22,"graph",50,50,50,585);

$graph->clearScale ();
$dataSet->RemoveSerie ( "created" );
$dataSet->RemoveSerie ( "closed" );
$dataSet->RemoveSerie ( "done" );
$dataSet->RemoveSerie ( "month" );
$dataSet->SetYAxisName ( i18n ( "sum" ) );
$graph->setFontProperties ( "../external/pChart/Fonts/tahoma.ttf", 8 );
$dataSet->AddPoint ( $rightScale, "scale" );
$dataSet->SetAbsciseLabelSerie ( "scale" );
$graph->drawBarGraph ( $dataSet->GetData (), $dataSet->GetDataDescription (), true );

$imgName = getGraphImgName ( "requirement cumulated anual" );
$graph->Render ( $imgName );
echo '<table width="95%" align="center"><tr><td align="center">';
echo '<img src="' . $imgName . '" />';
echo '</td></tr></table>';