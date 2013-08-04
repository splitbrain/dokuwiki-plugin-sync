<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * 
 * @author Dominik Eckelmann <deckelmann@gmail.com>
 * @author Andreas Gohr <andi@splitbrain.org>
 */
$lang['menu']                  = 'Wikis abgleichen';
$lang['syncstart']             = 'Abgleich starten';
$lang['lastsync']              = 'Letzter Abgleich:';
$lang['remotever']             = 'Entfernte Wiki Version:';
$lang['xmlerr']                = 'Fehler bei der Kommunikation mit dem entfernten Wiki. Stellen Sie sicher, dass es XML-RPC Zugriffe erlaubt und der den Endpunkt korrekt angegeben wurde.';
$lang['loginerr']              = 'Fehler beim Einloggen am entfernten Wiki. Bitte überprüfen Sie die eingegebenen Zugangsdaten.';
$lang['versionerr']            = 'Die XML-RPC-API Version des entfernten Wikis ist zu alt. Sie müssen das entfernte Wiki aktualisieren, um das Sync Plugin verwenden zu können.';
$lang['neversync']             = 'Dieses Profil wurde noch nie verwendet.';
$lang['profile']               = 'Abgleich-Profil';
$lang['newprofile']            = 'Neues Profil...';
$lang['select']                = 'Auswählen';
$lang['create']                = 'Neues Abgleich-Profil erstellen';
$lang['edit']                  = 'Abgleich-Profil bearbeiten';
$lang['delete']                = 'Lösche Synchronisationsprofil';
$lang['server']                = 'XMLRPC URL';
$lang['user']                  = 'Nutzername';
$lang['pass']                  = 'Passwort';
$lang['ns']                    = 'Namensraum';
$lang['depth']                 = 'Abgleich-Tiefe';
$lang['level0']                = 'Alle Unternamensräume';
$lang['level1']                = 'Keine Unternamensräume';
$lang['level2']                = 'Namensraum + 1 Unternamensraum';
$lang['level3']                = 'Namensraum + 2 Unternamensräume';
$lang['type']                  = 'Was soll abgeglichen werden?';
$lang['type0']                 = 'Alles';
$lang['type1']                 = 'Nur Seiten';
$lang['type2']                 = 'Medien Dateien';
$lang['save']                  = 'Speichern';
$lang['changewarn']            = 'Erneutes Speichern setzt die Abgleichzeiten für diese Profil zurück. Sie müssen dann die Abgleichrichtung für alle Dateien manuell festlegen.';
$lang['lockfail']              = 'Konnte nicht gelockt werden und wird übersprungen:';
$lang['pullfail']              = 'Runterladen fehlgeschlagen:';
$lang['pullok']                = 'Runterladen erfolgreich:';
$lang['localdelok']            = 'Lokales Löschen erfolgreich:';
$lang['localdelfail']          = 'Lokales Löschen fehlgeschlagen:';
$lang['pushfail']              = 'Hochladen fehlgeschlagen:';
$lang['pushok']                = 'Hochladen erfolgreich:';
$lang['remotedelok']           = 'Entferntes Löschen erfolgreich:';
$lang['remotedelfail']         = 'Entferntes Löschen fehlgeschlagen:';
$lang['skipped']               = 'Übersprungen:';
$lang['file']                  = 'Seite oder Mediendatei';
$lang['local']                 = 'Lokales Wiki';
$lang['remote']                = 'Entferntes Wiki';
$lang['diff']                  = 'Unterschied';
$lang['push']                  = 'Lade lokale Version in entferntes Wiki hoch.';
$lang['pushdel']               = 'Lösche die Version im entfernten Wiki.';
$lang['pull']                  = 'Lade die Version aus dem entfernten Wiki in das lokale Wiki.';
$lang['pulldel']               = 'Lösche die lokale Version.';
$lang['keep']                  = 'Überspringe diese Datei und behalte beide Versionen bei.';
$lang['syncdone']              = 'Abgleich abgeschlossen.';
$lang['timeout']               = 'Timeout';
