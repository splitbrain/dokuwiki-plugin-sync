<?php

$lang['menu'] = 'Synchronize Wikis';

$lang['syncstart'] = 'Start Synchronization';
$lang['lastsync']  = 'Last Synchronization:';
$lang['remotever'] = 'Remote Wiki Version:';

$lang['autherr']  = 'The provided user is not allowed to access the XMLRPC API at the remote wiki.';
$lang['loginerr'] = 'Failed to login at remote wiki. Make sure supplied credentials are valid at the remote wiki.';
$lang['versionerr'] = 'The remote wiki XMLRPC API version is too old. You need to upgrade the remote wiki to use the sync plugin.';

$lang['neversync'] = 'This profile was never synced before.';
$lang['profile'] = 'Sync Profile';
$lang['newprofile'] = 'New Profile...';
$lang['select'] = 'Select';
$lang['create'] = 'Create new Sync Profile';
$lang['edit'] = 'Edit Sync Profile';
$lang['delete'] = 'Delete Sync Profile';

$lang['server'] = 'XMLRPC URL';
$lang['user']   = 'Username';
$lang['pass']   = 'Password';
$lang['ns']     = 'Namespace';
$lang['depth']  = 'Sync Depth';
$lang['level0'] = 'All sub namespaces';
$lang['level1'] = 'No sub namespaces';
$lang['level2'] = 'Namespace + 1 sub namespace';
$lang['level3'] = 'Namespace + 2 sub namespaces';
$lang['type']   = 'What to sync';
$lang['type0']  = 'Everything';
$lang['type1']  = 'Pages only';
$lang['type2']  = 'Media files';


$lang['save'] = 'Save';
$lang['changewarn'] = 'Resaving this profile will reset the sync times. You will need to manually choose the sync directions for all files on the next sync.';

$lang['noconnect']     = 'Could not connect to remote wiki:';
$lang['lockfail']      = 'couldn\'t lock and will skip:';
$lang['localdelfail']  = 'local delete failed:';

$lang['js']['list']   = 'A list of files that differ between your local and the remote wiki is shown below. You need to decide which revisions you want to keep.'; #from list.txt

$lang['js']['file']   = 'Page or Media File';
$lang['js']['local']  = 'Local Wiki';
$lang['js']['remote'] = 'Remote Wiki';
$lang['js']['diff']   = 'Diff';
$lang['js']['dir']    = 'Sync Direction';

$lang['js']['push']    = 'Push local revision to remote wiki.';
$lang['js']['pushdel'] = 'Delete revision at the remote wiki.';
$lang['js']['pull']    = 'Pull remote revision to local wiki.';
$lang['js']['pulldel'] = 'Delete local revision.';
$lang['js']['keep']    = 'Skip this file and keep both revisions as is.';

$lang['js']['insync']    = 'There were no differences found between your local wiki and the remote wiki. No need to synchronize.'; #from nochange.txt
$lang['js']['tosync']    = 'Syncing %d files…';
$lang['js']['btn_done']  = 'Done';
$lang['js']['btn_start'] = 'Start';
$lang['js']['syncdone']  = 'Synchronization finished.';
$lang['js']['loading']   = 'Retrieving File List…';
$lang['js']['summary']   = 'Summary';

$lang['timeout'] = 'Timeout';


