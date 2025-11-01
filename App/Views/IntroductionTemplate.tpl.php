<?php
use App\Views\TemplateBasics;

/** Data pro sablonu */
global $tplData;

$tplHeaders = new TemplateBasics();
?>

<?php
$tplHeaders->getHTMLHeader($tplData['title']);

print_r($tplData['stories']);

$tplHeaders->getHTMLFooter();
