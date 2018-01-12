<?php

use dokuwiki\plugin\sync\ProfileManager;

if(!defined('DOKU_INC')) define('DOKU_INC', realpath(dirname(__FILE__) . '/../../../') . '/');
require_once(DOKU_INC . 'inc/init.php');
session_write_close();
if(!auth_isadmin()) die('not you my friend!');

global $INPUT;
global $conf;
global $lang;

$id = $INPUT->filter('cleanid')->str('id');
$profno = $INPUT->int('no');
$prmanager = new ProfileManager();
$profile = $prmanager->getProfile($profno);
$plugin = plugin_load('admin', 'sync');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $conf['lang'] ?>"
      lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo $plugin->getLang('diff') ?></title>
    <?php tpl_metaheaders() ?>
</head>
<body>
<div class="dokuwiki">
    <?php
    $df = $profile->diffPage($id);
    $tdf = new TableDiffFormatter();
    echo '<table class="diff" id="plugin__sync_diff">';
    echo '<tr>';
    echo '<th colspan="2" width="50%">' . $plugin->getLang('local') . '</th>';
    echo '<th colspan="2" width="50%">' . $plugin->getLang('remote') . '</th>';
    echo '</tr>';
    echo $tdf->format($df);
    echo '</table>';
    ?>
</div>
</body>

