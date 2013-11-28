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
$lang['xmlerr']                = 'Impossible de communiquer avec le wiki distant. Assurez-vous que celui si permet des demandes XMLRPC à distance et que vous avez correctement configuré le paramètre URL.';
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
$lang['pullfail']              = 'Réception échouée:';
$lang['pullok']                = 'Réception réussie:';
$lang['localdelok']            = 'Suppression locale réussie:';
$lang['localdelfail']          = 'Suppression locale échouée:';
$lang['pushfail']              = 'Envoi raté:';
$lang['pushok']                = 'Envoi réussi:';
$lang['remotedelok']           = 'Suppression à distance réussie:';
$lang['remotedelfail']         = 'Echec de la suppression à distance:';
$lang['skipped']               = 'Ignoré:';
$lang['file']                  = 'Page ou fichier multimédia';
$lang['local']                 = 'Wiki Local';
$lang['remote']                = 'Wiki Distant';
$lang['diff']                  = 'Diff';
$lang['push']                  = 'Envoi de la révision locale sur le wiki distant.';
$lang['pushdel']               = 'Supprimer la révision sur le wiki distant.';
$lang['pull']                  = 'Charger la révision sur le wiki local.';
$lang['pulldel']               = 'Supprimer la révision sur le wiki local.';
$lang['keep']                  = 'Ignorer ce fichier et garder les deux révisions.';
$lang['syncdone']              = 'Synchronisation terminée.';
$lang['timeout']               = 'Expiration';
