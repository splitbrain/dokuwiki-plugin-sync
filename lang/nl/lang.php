<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * 
 * @author mprins <mprins@users.sf.net>
 */
$lang['menu']                  = 'Synchroniseer Wikis';
$lang['syncstart']             = 'Start Synchronisatie';
$lang['lastsync']              = 'Laaste Synchronisatie:';
$lang['remotever']             = 'Externe Wiki Versie:';
$lang['xmlerr']                = 'Communicatie met de externe wiki is mislukt. Zorg ervoor dat de externe wiki XMLRPC aanvragen toestaat en dat de eindpunt URL correct is.';
$lang['loginerr']              = 'Inloggen op de externe wiki is mislukt. Zorg ervoor dat ingevoerde referenties geldig zijn voor de externe wiki.';
$lang['versionerr']            = 'De externe wiki XMLRPC API-versie is te oud. U moet de externe wiki upgraden om de sync plugin te gebruiken.';
$lang['neversync']             = 'Dit profiel is niet eerder gesynchroniseerd.';
$lang['profile']               = 'Sync Profiel';
$lang['newprofile']            = 'Nieuw Profiel...';
$lang['select']                = 'Selecteer';
$lang['create']                = 'Maak een nieuw Sync Profiel';
$lang['edit']                  = 'Sync Profiel aanpassen';
$lang['delete']                = 'Sync Profiel verwijderen';
$lang['server']                = 'XMLRPC URL';
$lang['user']                  = 'Gebruikersnaam';
$lang['pass']                  = 'Wachtwoord';
$lang['ns']                    = 'Naamruimte';
$lang['depth']                 = 'Sync Diepte';
$lang['level0']                = 'Alle sub-naamruimten';
$lang['level1']                = 'Geen sub-naamruimten';
$lang['level2']                = 'Naamruimte + 1 sub-naamruimte';
$lang['level3']                = 'Naamruimte + 2 sub-naamruimten';
$lang['type']                  = 'Wat te synchroniseren';
$lang['type0']                 = 'Alles';
$lang['type1']                 = 'Alleen pagina\'s';
$lang['type2']                 = 'Media bestanden';
$lang['save']                  = 'Opslaan';
$lang['changewarn']            = 'Opnieuw opslaan van dit profiel reset de synchronisatie tijd. U moet handmatig opnieuw de selectie instellen voor alle bestanden bij een volgende synchronisatie.';
$lang['lockfail']              = 'kon niet vergrendelen en wordt overgeslagen:';
$lang['pullfail']              = 'ophalen mislukt:';
$lang['pullok']                = 'ophalen gelukt:';
$lang['localdelok']            = 'lokaal verwijderen gelukt:';
$lang['localdelfail']          = 'lokaal verwijderen mislukt:';
$lang['pushfail']              = 'uploaden mislukt:';
$lang['pushok']                = 'uploaden gelukt:';
$lang['remotedelok']           = 'extern verwijderen gelukt:';
$lang['remotedelfail']         = 'extern verwijderen mislukt:';
$lang['skipped']               = 'overgeslagen:';
$lang['file']                  = 'Pagina of Media bestand';
$lang['local']                 = 'Lokale Wiki';
$lang['remote']                = 'Externe Wiki';
$lang['diff']                  = 'Verschil';
$lang['push']                  = 'Upload de lokale revisie naar de externe wiki.';
$lang['pushdel']               = 'Verwijder revisie van de externe wiki.';
$lang['pull']                  = 'Download externe revisie naar de lokale wiki.';
$lang['pulldel']               = 'Verwijder de lokale revisie.';
$lang['keep']                  = 'Sla dit bestand over en hou beide revisies zoals ze zijn.';
$lang['syncdone']              = 'Synchronisatie finished.';
$lang['timeout']               = 'Timeout';
