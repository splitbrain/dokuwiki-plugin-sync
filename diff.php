<?php
/**
 * CAPTCHA antispam plugin - Image generator
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <gohr@cosmocode.de>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
require_once(DOKU_INC.'inc/init.php');
require_once(DOKU_INC.'inc/auth.php');
session_write_close();
require_once(DOKU_INC.'inc/template.php');
require_once(DOKU_INC.'inc/DifferenceEngine.php');
require_once(dirname(__FILE__).'/admin.php');

if(!auth_isadmin()) die('not you my friend!');

$id = cleanID($_REQUEST['id']);
$plugin = new admin_plugin_sync();
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $conf['lang']?>"
 lang="<?php echo $conf['lang']?>" dir="<?php echo $lang['direction']?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo $plugin->getLang('diff')?></title>
  <?php tpl_metaheaders()?>
</head>
<body>
    <div class="dokuwiki">
    <?php $plugin->_diff($id);?>
    </div>
</body>

