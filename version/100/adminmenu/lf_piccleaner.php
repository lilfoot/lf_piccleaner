<?php
// Variablen die bei jedem Plugin existieren
// $GLOBALS['smarty']               Smarty Template Engine Object
// $GLOBALS['oPlugin']              Plugin Object
global $smarty, $oPlugin;

require_once(PFAD_ROOT . PFAD_INCLUDES . "tools.Global.php");

$step = "overview";
$notice = "";
$error = "";

$smarty->assign("notice", $notice);
$smarty->assign("error", $error);
$smarty->assign("URL_SHOP", URL_SHOP);
$smarty->assign("PFAD_ROOT", PFAD_ROOT);
$smarty->assign("URL_ADMINMENU", URL_SHOP . "/" . PFAD_PLUGIN . $oPlugin->cVerzeichnis . "/" . PFAD_PLUGIN_VERSION . $oPlugin->nVersion . "/" . PFAD_PLUGIN_ADMINMENU);
$smarty->assign("step", $step);
print($smarty->fetch($oPlugin->cAdminmenuPfad . "template/lf_piccleaner.tpl"));
?>