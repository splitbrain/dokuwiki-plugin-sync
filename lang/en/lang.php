<?php

$lang['menu'] = 'Synchronize Wikis';

$lang['syncstart'] = 'Start Synchronization';
$lang['lastsync']  = 'Last Synchronization:';
$lang['remotever'] = 'Remote Wiki Version:';

$lang['xmlerr']   = 'Failed to talk to remote wiki. Make sure the remote wiki allows XMLRPC requests and that you set up the endpoint URL correctly.';
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

$lang['lockfail']      = 'couldn\'t lock and will skip:';
$lang['pullfail']      = 'pull failed:';
$lang['pullok']        = 'pull succeded:';
$lang['localdelok']    = 'local delete succeeded:';
$lang['localdelfail']  = 'local delete failed:';
$lang['pushfail']      = 'push failed:';
$lang['pushok']        = 'push succeded:';
$lang['remotedelok']   = 'remote delete succeeded:';
$lang['remotedelfail'] = 'remote delete failed:';
$lang['skipped']       = 'skipped:';

$lang['file']   = 'Page or Media File';
$lang['local']  = 'Local Wiki';
$lang['remote'] = 'Remote Wiki';
$lang['diff']   = 'Diff';

$lang['push']    = 'Push local revision to remote wiki.';
$lang['pushdel'] = 'Delete revision at the remote wiki.';
$lang['pull']    = 'Pull remote revision to local wiki.';
$lang['pulldel'] = 'Delete local revision.';
$lang['keep']    = 'Skip this file and keep both revisions as is.';

$lang['syncdone'] = 'Synchronization finished.';
$lang['timeout'] = 'Timeout';
