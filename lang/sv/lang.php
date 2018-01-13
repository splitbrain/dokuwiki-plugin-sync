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
$lang['localdelfail']          = 'lokal borttagning misslyckades:';
$lang['js']['file']                  = 'Sida eller mediafil';
$lang['js']['local']                 = 'Lokal wiki';
$lang['js']['remote']                = 'Fjärrwiki';
$lang['js']['diff']                  = 'Skillnader';
$lang['js']['push']                  = 'Push lokal version till fjärrwikin.';
$lang['js']['pushdel']               = 'Ta bort version på fjärrwikin.';
$lang['js']['pull']                  = 'Pull fjärrversion till lokal wiki.';
$lang['js']['pulldel']               = 'Ta bort lokal version.';
$lang['js']['keep']                  = 'Skippa denna fil och behåll både versionerna som de är.';
$lang['js']['syncdone']              = 'Synkroniseringen avslutad.';
$lang['timeout']               = 'Timeout';

$lang['js']['list'] = 'En lista var som skiljer mellan din lokala och fjärrwiki visas nedan. Du behöver besluta vilken version du vill behålla.';
$lang['js']['insync'] = 'Inga skillnader kunde påvisas mellan din lokala och fjärrwikin. Inget behov av synkronisering.';
