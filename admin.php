<?php
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'admin.php');

require_once(DOKU_INC.'inc/IXR_Library.php');

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_sync extends DokuWiki_Admin_Plugin {

    var $profiles = array();
    var $profno = '';

    function admin_plugin_sync(){
        $this->_profileLoad();

        $this->profno = preg_replace('/[^0-9]+/','',$_REQUEST['no']);
    }



    /**
     * return some info
     */
    function getInfo(){
        return confToHash(dirname(__FILE__).'/info.txt');
    }

    /**
     * return sort order for position in admin menu
     */
    function getMenuSort() {
        return 1020;
    }

    /**
     * handle user request
     */
    function handle() {
        if(isset($_REQUEST['p']) && is_array($_REQUEST['p'])){
            $this->profiles[$this->profno] = $_REQUEST['p'];
            $this->_profileSave();
        }
    }

    /**
     * output appropriate html
     */
    function html() {
        if($_POST['sync'] && $this->profno!==''){
            // do the sync
            $this->_sync($this->profno,
                         $_POST['sync'],
                         (int) $_POST['lnow'],
                         (int) $_POST['rnow']);
        }elseif($_REQUEST['startsync'] && $this->profno!==''){
            // get sync list
            list($lnow,$rnow) = $this->_getTimes($this->profno);
            if($lnow){
                $list = $this->_getSyncList($this->profno,$rnow);
            }else{
                $list = array();
            }
            if(count($list)){
                $this->_form($this->profno,$list,$lnow,$rnow);
            }else{
                echo '<p>No changes were found.</p>';
            }
        }else{
            // profile management
            $this->_profilelist($this->profno);
            $this->_profileform($this->profno);

            if($this->profno !=='' ){
                $this->_profileView($this->profno);
            }
        }
    }

    function _profileLoad(){
        global $conf;
        $profiles = $conf['metadir'].'/sync.profiles';
        if(file_exists($profiles)){
            $this->profiles = unserialize(io_readFile($profiles,false));
        }
    }

    function _profileSave(){
        global $conf;
        $profiles = $conf['metadir'].'/sync.profiles';
        io_saveFile($profiles,serialize($this->profiles));
    }

    function _profileView($no){
        global $conf;
        echo '<form action="" method="post">';
        echo '<input type="hidden" name="no" value="'.hsc($no).'" />';
        echo '<fieldset><legend>Synchronize Wikis</legend>';
        if($this->profiles[$no]['ltime']){
            echo '<p>Last sync: '.strftime($conf['dformat'],$this->profiles[$no]['ltime']).'</p>';
        }else{
            echo '<p>This profile was never synchronized before.</p>';
        }
        echo '<input name="startsync" type="submit" value="Start Synchronization" class="button" />';
        echo '</fieldset>';
        echo '</form>';
    }

    function _profilelist($no=''){
        echo '<form action="" method="post">';
        echo '<fieldset><legend>Sync Profile</legend>';
        echo '<select name="no" class="edit"';
        echo '  <option value="">New profile...</option>';
        foreach($this->profiles as $pno => $opts){
            $srv = parse_url($opts['server']);

            echo '<option value="'.hsc($pno).'" '.(($no!=='' && $pno == $no)?'selected="selected"':'').'>';
            if($opts['user']) echo hsc($opts['user']).'@';
            echo hsc($srv['host']);
            echo '</option>';
        }
        echo '</select>';
        echo '<input type="submit" value="Go" class="button" />';
        echo '</fieldset>';
        echo '</form>';
    }

    function _profileform($no=''){
        echo '<form action="" method="post">';
        echo '<fieldset><legend>';
        if($no !== ''){
            echo 'Edit Sync Profile';
        }else{
            echo 'Create New Sync Profile';
        }
        echo '</legend>';

        echo '<input type="hidden" name="no" value="'.hsc($no).'" />';

        echo '<label for="sync__server">XMLRPC URL</label> ';
        echo '<input type="text" name="p[server]" id="sync__server" class="edit" value="'.hsc($this->profiles[$no]['server']).'" /><br />';

        echo '<label for="sync__server">Namespace</label> ';
        echo '<input type="text" name="p[ns]" id="sync__ns" class="edit" value="'.hsc($this->profiles[$no]['ns']).'" /><br />';

        echo '<label for="sync__user">User</label> ';
        echo '<input type="text" name="p[user]" id="sync__user" class="edit" value="'.hsc($this->profiles[$no]['user']).'" /><br />';

        echo '<label for="sync__pass">Password</label> ';
        echo '<input type="password" name="p[pass]" id="sync__pass" class="edit" value="'.hsc($this->profiles[$no]['pass']).'" /><br />';

        echo '<input type="submit" value="save" class="button" />';
        if($no !== ''){
            echo '<br /><small>Changing a profile will reset the last sync time</small>';
        }
        echo '</fieldset>';
        echo '</form>';
    }

    function _sync($no,&$synclist,$ltime,$rtime){
        echo '<ul>';
        $client = new IXR_Client($this->profiles[$no]['server']);
        $client->user = $this->profiles[$no]['user'];
        $client->pass = $this->profiles[$no]['pass'];
        foreach((array) $synclist as $id => $dir){
            flush();
            if($dir == 0) continue;
            if($dir == -1){
                //pull
                $ok = $client->query('wiki.getPage',$id);
                if(!$ok){
                    echo '<li class="error"><div class="li">';
                    echo 'failed to pull '.hsc($id).' ';
                    echo hsc($client->getErrorMessage());
                    echo '</div></li>';
                    continue;
                }
                $data = $client->getResponse();
                saveWikiText($id,$data,'synced',false);
                echo '<li class="ok"><div class="li">';
                echo 'pulled '.hsc($id);
                echo '</div></li>';
                continue;
            }
            if($dir == 1){
                // push
                $data = rawWiki($id);
                $ok = $client->query('wiki.putPage',$id,$data,array('sum'=>'synced'));
                if(!$ok){
                    echo '<li class="error"><div class="li">';
                    echo 'failed to push '.hsc($id).' ';
                    echo hsc($client->getErrorMessage());
                    echo '</div></li>';
                    continue;
                }
                echo '<li class="ok"><div class="li">';
                echo 'pushed '.hsc($id);
                echo '</div></li>';

                continue;
            }
        }
        echo '</ul>';

        // save synctime
        list($letime,$retime) = $this->_getTimes($no);
        $this->profiles[$no]['ltime'] = $ltime;
        $this->profiles[$no]['rtime'] = $rtime;
        $this->profiles[$no]['letime'] = $letime;
        $this->profiles[$no]['retime'] = $retime;
        $this->_profileSave();
    }

    function _form($no,$synclist,$lnow,$rnow){
        global $conf;

        $ltime = (int) $this->profiles[$no]['ltime'];
        $rtime = (int) $this->profiles[$no]['rtime'];
        $letime = (int) $this->profiles[$no]['letime'];
        $retime = (int) $this->profiles[$no]['retime'];

        echo '<form action="" method="post">';
        echo '<table class="inline">';
        echo '<input type="hidden" name="lnow" value="'.$lnow.'" />';
        echo '<input type="hidden" name="rnow" value="'.$rnow.'" />';
        echo '<input type="hidden" name="no" value="'.$no.'" />';
        echo '<tr>
                <th>page</th>
                <th>local</th>
                <th>&gt;</th>
                <th>=</th>
                <th>&lt;</th>
                <th>remote</th>
              </tr>';
        foreach($synclist as $id => $item){
            // check direction
            $dir = 0;
            if($ltime && $rtime){
                if($item['remote']['rev'] > $rtime &&
                   $item['local']['rev'] <= $letime){
                    $dir = -1;
                }
                if($item['remote']['rev'] <= $retime &&
                   $item['local']['rev'] > $ltime){
                    $dir = 1;
                }
            }

            echo '<tr>';

            echo '<td>'.hsc($id).'</td>';
            echo '<td>';
            if(!isset($item['local'])){
                echo '&mdash;';
            }else{
                echo strftime($conf['dformat'],$item['local']['rev']);
                echo ' ('.$item['local']['size'].' bytes)';
            }
            echo '</td>';

            echo '<td>';
            echo '<input type="radio" name="sync['.hsc($id).']" value="1" title="push local version to remote wiki" '.(($dir == 1)?'checked="checked"':'').' />';
            echo '</td>';
            echo '<td>';
            echo '<input type="radio" name="sync['.hsc($id).']" value="0" title="keep both versions" '.(($dir == 0)?'checked="checked"':'').' />';
            echo '</td>';
            echo '<td>';
            echo '<input type="radio" name="sync['.hsc($id).']" value="-1" title="pull remote version to local wiki" '.(($dir == -1)?'checked="checked"':'').' />';
            echo '</td>';

            echo '<td>';
            if(!isset($item['remote'])){
                echo '&mdash;';
            }else{
                echo strftime($conf['dformat'],$item['remote']['rev']);
                echo ' ('.$item['remote']['size'].' bytes)';
            }
            echo '</td>';

            echo '</tr>';
        }
        echo '</table>';
        echo '<input type="submit" value="Synchronize!" class="button" />';
        echo '</form>';
    }

    function _getTimes($no){
        // get remote time
        $client = new IXR_Client($this->profiles[$no]['server']);
        $client->user = $this->profiles[$no]['user'];
        $client->pass = $this->profiles[$no]['pass'];
        $ok = $client->query('dokuwiki.getTime');
        if(!$ok){
            msg('Failed to fetch remote time. '.
                $client->getErrorMessage(),-1);
            return false;
        }
        $rtime = $client->getResponse();
        $ltime = time();
        return array($ltime,$rtime);
    }

    function _getSyncList($no){
        global $conf;
        $list = array();
        $ns = $this->profiles[$no]['ns'];

        // get remote file list
        $client = new IXR_Client($this->profiles[$no]['server']);
        $client->user = $this->profiles[$no]['user'];
        $client->pass = $this->profiles[$no]['pass'];
        $ok = $client->query('dokuwiki.getPagelist',$ns,array('depth' => 0, 'hash' => true));
        if(!$ok){
            msg('Failed to fetch remote file list. '.
                $client->getErrorMessage(),-1);
            return false;
        }
        $remote = $client->getResponse();
        // put into synclist
        foreach($remote as $item){
            $list[$item['id']]['remote'] = $item;
            unset($list[$item['id']]['remote']['id']);
        }
        unset($remote);

        // get local file list
        $local = array();
        $dir = utf8_encodeFN(str_replace(':', '/', $ns));
        require_once(DOKU_INC.'inc/search.php');
        search($local, $conf['datadir'], 'search_allpages', array('depth' => 0, 'hash' => true), $dir);
        // put into synclist
        foreach($local as $item){
            // skip identical files
            if($list[$item['id']]['remote']['hash'] == $item['hash']){
                unset($list[$item['id']]);
                continue;
            }

            $list[$item['id']]['local'] = $item;
            unset($list[$item['id']]['local']['id']);
        }
        unset($local);

        ksort($list);
        return $list;
    }

}
//Setup VIM: ex: et ts=4 enc=utf-8 :
