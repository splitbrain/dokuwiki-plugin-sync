<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @author Tor Härnqvist <tor@harnqvist.se>
 */
$lang['menu']                  = 'Synkronisera wikis';
$lang['syncstart']             = 'Påbörja synkronisering';
$lang['lastsync']              = 'Senaste synkronisering:';
$lang['remotever']             = 'Fjärrwiki-version';
$lang['xmlerr']                = 'Misslyckades att kommunicera med fjärrwikin. Kontrollera att fjärrwikin tillåter XMLRPC-begäran och att du ställt in slutlig-URL korrekt.';
$lang['loginerr']              = 'Misslyckades att logga in på fjärrwikin. Kontrollera att ifyllda inloggningsuppgifter för fjärrwikin stämmer.';
$lang['versionerr']            = 'Fjärrwikins XMLRPC API-version är för gammal. Du behöver uppgradera fjärrwikin för att använda synkroniseringspluginet.';
$lang['neversync']             = 'Denna profil har aldrig tidigare synkroniserats.';
$lang['profile']               = 'Synkroniseringsprofil';
$lang['newprofile']            = 'Ny profil...';
$lang['select']                = 'Välj';
$lang['create']                = 'Skapa ny synkroniseringsprofil';
$lang['edit']                  = 'Redigera synkroniseringsprofil';
$lang['delete']                = 'Ta bort synkroniseringsprofil';
$lang['server']                = 'XMLRPC URL';
$lang['user']                  = 'Användarnamn';
$lang['pass']                  = 'Lösenord';
$lang['ns']                    = 'Namnrymd';
$lang['depth']                 = 'Synkroniseringsdjup';
$lang['level0']                = 'Alla underliggande namnrymder';
$lang['level1']                = 'Inga underliggande namnrymder';
$lang['level2']                = 'Namnrymd + 1 underliggande namnrymd';
$lang['level3']                = 'Namnrymd + 2 underliggande namnrymd';
$lang['type']                  = 'Välj vad som ska synkroniseras';
$lang['type0']                 = 'Allt';
$lang['type1']                 = 'Enbart sidor';
$lang['type2']                 = 'Mediafiler';
$lang['save']                  = 'Spara';
$lang['changewarn']            = 'Att återspara denna profil kommer att nollställa synkroniseringstiderna. Du kommer manuellt att behöva välja synkroniseringsriktning vid nästa synkronisering.';
$lang['lockfail']              = 'kunde inte låsa, kommer hoppas över:';
$lang['pullfail']              = 'pull misslyckades:';
$lang['pullok']                = 'pull lyckades:';
$lang['localdelok']            = 'lokal borttagning lyckades:';
$lang['localdelfail']          = 'lokal borttagning misslyckades:';
$lang['pushfail']              = 'push misslyckades:';
$lang['pushok']                = 'push lyckades:';
$lang['remotedelok']           = 'fjärr-borttagning lyckades:';
$lang['remotedelfail']         = 'fjärr-borttagning misslyckades:';
$lang['skipped']               = 'hoppades över:';
$lang['file']                  = 'Sida eller mediafil';
$lang['local']                 = 'Lokal wiki';
$lang['remote']                = 'Fjärrwiki';
$lang['diff']                  = 'Skillnader';
$lang['push']                  = 'Push lokal version till fjärrwikin.';
$lang['pushdel']               = 'Ta bort version på fjärrwikin.';
$lang['pull']                  = 'Pull fjärrversion till lokal wiki.';
$lang['pulldel']               = 'Ta bort lokal version.';
$lang['keep']                  = 'Skippa denna fil och behåll både versionerna som de är.';
$lang['syncdone']              = 'Synkroniseringen avslutad.';
$lang['timeout']               = 'Timeout';
