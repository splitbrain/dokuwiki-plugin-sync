<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @author Nicolas Charpenel <n.charpenel@laposte.net>
 */
$lang['menu']                  = 'Wiki Synchronisation';
$lang['syncstart']             = 'Lancer la Synchronisation';
$lang['lastsync']              = 'Dernière Synchronisation:';
$lang['remotever']             = 'Version du Wiki distant:';
$lang['loginerr']              = 'Échec de la connexion au Wiki distant. Assurez-vous que les informations d\'identification fournies sont valables sur le wiki distant.';
$lang['versionerr']            = 'La version de l\'API XMLRPC du Wiki distant est trop ancienne. Vous devez mettre à jour le wiki distant pour utiliser le plugin synchronisation.';
$lang['neversync']             = 'Ce profil n\'a jamais été synchronisés avant.';
$lang['profile']               = 'Profil de syncronisation';
$lang['newprofile']            = 'Nouveau Profil...';
$lang['select']                = 'Sélection';
$lang['create']                = 'Créer un nouveau Profil de Sync.';
$lang['edit']                  = 'Editer le Profil de Sync.';
$lang['delete']                = 'Effacer le profil de syncronisation';
$lang['server']                = 'XMLRPC URL';
$lang['user']                  = 'Nom d\'usage';
$lang['pass']                  = 'Mot de passe';
$lang['ns']                    = 'Espace de Nom';
$lang['depth']                 = 'Mode de Sync.';
$lang['level0']                = 'Tout les espaces de nom';
$lang['level1']                = 'Aucun des espaces de noms';
$lang['level2']                = 'Espace de noms + 1 sous espace de noms';
$lang['level3']                = 'Espace de noms + 2 sous espace de noms';
$lang['type']                  = 'Synchroniser quoi';
$lang['type0']                 = 'Tout';
$lang['type1']                 = 'Pages seulement';
$lang['type2']                 = 'Fichiers Multimédia';
$lang['save']                  = 'Sauvegarder';
$lang['changewarn']            = 'Réenregistrer ce profil réinitialisera les dates de synchronisation. Vous devrez choisir manuellement le mode de synchronisation pour tous les fichiers sur la prochaine synchronisation.';
$lang['lockfail']              = 'Impossible de verrouiller et donc ignoré:';
$lang['localdelfail']          = 'Suppression locale échouée:';
$lang['js']['file']                  = 'Page ou fichier multimédia';
$lang['js']['local']                 = 'Wiki Local';
$lang['js']['remote']                = 'Wiki Distant';
$lang['js']['diff']                  = 'Diff';
$lang['js']['push']                  = 'Envoi de la révision locale sur le wiki distant.';
$lang['js']['pushdel']               = 'Supprimer la révision sur le wiki distant.';
$lang['js']['pull']                  = 'Charger la révision sur le wiki local.';
$lang['js']['pulldel']               = 'Supprimer la révision sur le wiki local.';
$lang['js']['keep']                  = 'Ignorer ce fichier et garder les deux révisions.';
$lang['js']['syncdone']              = 'Synchronisation terminée.';
$lang['timeout']               = 'Expiration';

$lang['js']['list'] = 'Une liste des fichiers qui diffèrent entre vos fichiers locaux et le wiki distant est indiqué ci-dessous. Vous devez décider ceux que vous garder.';
$lang['js']['insync'] = 'Aucune différence n\'a été constatée entre votre wiki local et le wiki distant. Pas besoin de synchroniser.';
