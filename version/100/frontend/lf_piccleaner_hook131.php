<?php

// Anzahl Bilder ermitteln und Array mit Bildern in Session speichern
if (verifyGPDataString("cleanup") == "init") {

    $oFiles_arr = init_cleanup();

    $oReturn_arr = array();
    $oReturn_arr['nZeit'] = sprintf("%0.5f Sekunden", ($end - $start));
    $oReturn_arr['nAnzahl'] = count($oFiles_arr);
    $oReturn_arr['finished'] = false;

    die(json_encode($oReturn_arr));
    exit;
}


if (verifyGPDataString("cleanup") == "clean") {
    $oFiles_arr = init_cleanup();

    $bFinished = FALSE;
    $nActionsPerCall = 10;
    $nPicsDeleted = verifyGPCDataInteger("nPicsDel");

    $nStart = verifyGPCDataInteger("nPointer");
    $nPicsChecked = $nStart;

    for ($i=$nStart;$i<($nStart+$nActionsPerCall);$i++) {
        if ($i<count($oFiles_arr)) {
            $nPicsChecked++;
            // Datenbank auf Bild prüfen
            $oDbPic = $GLOBALS["DB"]->executeQuery("SELECT * FROM tartikelpict
                                                    WHERE cPfad='" . trim($oFiles_arr[$i]) . "'", 1);

            if (!is_object($oDbPic) && file_exists(PFAD_ROOT."bilder/produkte/gross/".trim($oFiles_arr[$i])) && strlen(trim($oFiles_arr[$i]))>0) {
                $nPicsDeleted++;
                // Das Bild wurde nicht in "tartikelpict" gefunden -> löschen einleiten
                @unlink(PFAD_ROOT."bilder/produkte/gross/".trim($oFiles_arr[$i]));
                @unlink(PFAD_ROOT."bilder/produkte/normal/".trim($oFiles_arr[$i]));
                @unlink(PFAD_ROOT."bilder/produkte/klein/".trim($oFiles_arr[$i]));
                @unlink(PFAD_ROOT."bilder/produkte/mini/".trim($oFiles_arr[$i]));

                // Logging
                Jtllog::writeLog("lf_piccleaner: Lösche Bild '" . $oFiles_arr[$i], JTLLOG_LEVEL_DEBUG, false, "kPlugin", $oPlugin->kPlugin);


                // Zusätzliches Textlog
                $handle = fopen(PFAD_LOGFILES . "lf_piccleaner.log", "a+");
                if ($handle !== FALSE) {
                    fwrite($handle, date("d.m.Y H:i:s") . " - Lösche Bild " . $oFiles_arr[$i] . "");
                }
                fclose($handle);
            }
        }
        else {
            $bFinished = TRUE;
        }
    }

    $oReturn_arr = array();

    if ($bFinished) {
        $oReturn_arr['finished'] = true;
        @unlink(PFAD_LOGFILES."lf_piccleaner.del");
    }
    else {
        $oReturn_arr['finished'] = false;
    }

    $oReturn_arr['nPointer'] = ($nStart+$nActionsPerCall);
    $oReturn_arr['lf_piccleaner_deleted'] = $_SESSION['lf_piccleaner_deleted'];
    $oReturn_arr['nPicsDeleted'] = $nPicsDeleted;
    $oReturn_arr['nPicsChecked'] = $nPicsChecked;

    die(json_encode($oReturn_arr));
    exit;
}


if (verifyGPDataString("cleanup") == "variationeninit") {

    $oFiles_arr = init_variationencleanup();

    $oReturn_arr = array();
    $oReturn_arr['nZeit'] = sprintf("%0.5f Sekunden", ($end - $start));
    $oReturn_arr['nAnzahl'] = count($oFiles_arr);
    $oReturn_arr['finished'] = false;

    die(json_encode($oReturn_arr));
    exit;
}


if (verifyGPDataString("cleanup") == "variationenclean") {

    $oFiles_arr = init_variationencleanup();

    $bFinished = FALSE;
    $nActionsPerCall = 10;
    $nPicsDeleted = verifyGPCDataInteger("nPicsDel");

    $nStart = verifyGPCDataInteger("nPointer");
    $nPicsChecked = $nStart;

    for ($i=$nStart;$i<($nStart+$nActionsPerCall);$i++) {
        if ($i<count($oFiles_arr) && strlen($oFiles_arr[$i])>0) {
            $nPicsChecked++;
            // Datenbank auf Bild prüfen
            $oDbPic = $GLOBALS["DB"]->executeQuery("SELECT * FROM teigenschaftwertpict
                                                    WHERE cPfad='" . trim($oFiles_arr[$i]) . "'", 1);

            if (!is_object($oDbPic) && file_exists(PFAD_ROOT."bilder/variationen/gross/".trim($oFiles_arr[$i])) && strlen(trim($oFiles_arr[$i]))>0) {
                $nPicsDeleted++;
                // Das Bild wurde nicht in "tartikelpict" gefunden -> löschen einleiten
                @unlink(PFAD_ROOT."bilder/variationen/gross/".trim($oFiles_arr[$i]));
                @unlink(PFAD_ROOT."bilder/variationen/normal/".trim($oFiles_arr[$i]));
                @unlink(PFAD_ROOT."bilder/variationen/klein/".trim($oFiles_arr[$i]));

                // Logging
                Jtllog::writeLog("lf_piccleaner: Lösche Variations-Bild '" . $oFiles_arr[$i], JTLLOG_LEVEL_DEBUG, false, "kPlugin", $oPlugin->kPlugin);


                // Zusätzliches Textlog
                $handle = fopen(PFAD_LOGFILES . "lf_piccleaner_variationen.log", "a+");
                if ($handle !== FALSE) {
                    fwrite($handle, date("d.m.Y H:i:s") . " - Lösche Variations-Bild " . $oFiles_arr[$i] . "");
                }
                fclose($handle);
            }
        }
        else {
            $bFinished = TRUE;
        }
    }

    $oReturn_arr = array();

    if ($bFinished) {
        $oReturn_arr['finished'] = true;
        @unlink(PFAD_LOGFILES."lf_piccleaner.del");
    }
    else {
        $oReturn_arr['finished'] = false;
    }

    $oReturn_arr['nPointer'] = ($nStart+$nActionsPerCall);
    $oReturn_arr['lf_piccleaner_deleted'] = $_SESSION['lf_piccleaner_deleted'];
    $oReturn_arr['nPicsDeleted'] = $nPicsDeleted;
    $oReturn_arr['nPicsChecked'] = $nPicsChecked;

    die(json_encode($oReturn_arr));
    exit;
}


function init_cleanup() {
    if (!is_file(PFAD_LOGFILES."lf_piccleaner.del")) {
        $start = microtime();
        $delFile = fopen(PFAD_LOGFILES."lf_piccleaner.del", "w+");
        if ($handle = opendir(PFAD_ROOT . "bilder/produkte/normal/")) {
            $oCheckPics_arr = array();
            while (false !== ($file = readdir($handle))) {
                if (preg_match("(^(([a-zA-Z0-9-_ ])+\.(jpg|png))$)", $file) && strlen($file)>0) {
                    fwrite($delFile, $file."\n");
                }
            }
            fclose($delFile);
            closedir($handle);
        }
        $end = microtime();
    }

    $handle = fopen(PFAD_LOGFILES."lf_piccleaner.del", "r");

    while (!feof($handle)) {
        $line = fgets($handle, 819200);

        if (strlen($line)>0)
            $oFiles_arr[] = $line;
    }
    fclose($handle);

    return $oFiles_arr;
}


function init_variationencleanup() {
    if (!is_file(PFAD_LOGFILES."lf_piccleaner.del")) {
        $start = microtime();
        $delFile = fopen(PFAD_LOGFILES."lf_piccleaner.del", "w+");
        if ($handle = opendir(PFAD_ROOT . "bilder/variationen/normal/")) {
            $oCheckPics_arr = array();
            while (false !== ($file = readdir($handle))) {
                if (preg_match("(^(([a-zA-Z0-9-_ ])+\.(jpg|png))$)", $file) && strlen($file)>0) {
                    fwrite($delFile, $file."\n");
                }
            }
            fclose($delFile);
            closedir($handle);
        }
        $end = microtime();
    }

    $handle = fopen(PFAD_LOGFILES."lf_piccleaner.del", "r");

    while (!feof($handle)) {
        $line = fgets($handle, 819200);

        if (strlen($line)>0)
            $oFiles_arr[] = $line;
    }
    fclose($handle);

    return $oFiles_arr;
}