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

    protected $profiles = array();
    protected $profno = '';
    protected $client = null;
    protected $apiversion = 0;
    protected $defaultTimeout = 15;

    /**
     * Constructor.
     */
    function admin_plugin_sync(){
        $this->_profileLoad();
        $this->profno = preg_replace('/[^0-9]+/','',$_REQUEST['no']);
    }

    function _connect(){
        if(!is_null($this->client)) return true;
        $this->client = new IXR_Client($this->profiles[$this->profno]['server']);
        if ( isset($this->profiles[$this->profno]['timeout']) ){
          $timeout = (int) $this->profiles[$this->profno]['timeout'];
        } else {
          $timeout = $this->defaultTimeout;
        }
        $this->client->timeout = $timeout;

        // do the login
        if($this->profiles[$this->profno]['user']){
            $ok = $this->client->query('dokuwiki.login',
                    $this->profiles[$this->profno]['user'],
                    $this->profiles[$this->profno]['pass']
                  );
            if(!$ok){
                msg($this->getLang('xmlerr').' '.hsc($this->client->getErrorMessage()),-1);
                $this->client = null;
                return false;
            }
            if(!$this->client->getResponse()){
                msg($this->getLang('loginerr'),-1);
                $this->client = null;
                return false;
            }
        }

        $ok = $this->client->query('dokuwiki.getXMLRPCAPIVersion');
        if(!$ok){
            msg($this->getLang('xmlerr').' '.hsc($this->client->getErrorMessage()),-1);
            $this->client = null;
            return false;
        }
        $this->apiversion = (int) $this->client->getResponse();
        if($this->apiversion < 1){
            msg($this->getLang('versionerr'),-1);
            $this->client = null;
            return false;
        }

        return true;
    }

    /**
     * return sort order for position in admin menu
     */
    function getMenuSort() {
        return 1020;
    }

    /**
     * handle profile saving/deleting
     */
    function handle() {
        if(isset($_REQUEST['prf']) && is_array($_REQUEST['prf'])){
            if(isset($_REQUEST['sync__delete']) && $this->profno !== ''){
                // delete profile
                unset($this->profiles[$this->profno]);
                $this->profiles = array_values($this->profiles); //reindex
                $this->profno = '';
            }else{
                // add/edit profile
                if($this->profno === '') $this->profno = count($this->profiles);
                if ( !isset($_REQUEST['prf']['timeout']) || !is_numeric($_REQUEST['prf']['timeout']) ){
                  $_REQUEST['prf']['timeout'] = $this->defaultTimeout;
                }
                $this->profiles[$this->profno] = $_REQUEST['prf'];
            }
            $this->_profileSave();

            // reset the client
            $this->client = null;
        }
    }

    /**
     * output appropriate html
     */
    function html() {
        if(($_POST['sync_pages'] || $_POST['sync_media']) && $this->profno!==''){
            // do the sync
            echo $this->locale_xhtml('sync');

            //show progressbar
            echo '<div class="centeralign" id="dw__loading">'.NL;
            echo '<script type="text/javascript" charset="utf-8"><!--//--><![CDATA[//><!--'.NL;
            echo 'showLoadBar();'.NL;
            echo '//--><!]]></script>'.NL;
            echo '<br /></div>'.NL;
            flush();
            ob_flush();

            echo '<ul class="sync">';

            if($_POST['sync_pages']){
                $this->_sync($_POST['sync_pages'],'pages');
            }
            if($_POST['sync_media']){
                $this->_sync($_POST['sync_media'],'media');
            }
            $this->_saveSyncTimes((int) $_POST['lnow'],
                                 (int) $_POST['rnow']);

            echo '</ul>';

            //hide progressbar
            echo '<script type="text/javascript" charset="utf-8"><!--//--><![CDATA[//><!--'.NL;
            echo 'hideLoadBar("dw__loading");'.NL;
            echo '//--><!]]></script>'.NL;
            flush();
            ob_flush();


            echo '<p>'.$this->getLang('syncdone').'</p>';
        }elseif($_REQUEST['startsync'] && $this->profno!==''){
            // get sync list
            list($lnow,$rnow) = $this->_getTimes();
            $pages = array();
            $media = array();
            if($rnow){
                if($this->profiles[$this->profno]['type'] == 0 ||
                   $this->profiles[$this->profno]['type'] == 1){
                    $pages = $this->_getSyncList('pages');
                }
                if(($this->profiles[$this->profno]['type'] == 0 ||
                   $this->profiles[$this->profno]['type'] == 2)
                    && $pages !== false ){
                    $media = $this->_getSyncList('media');
                }
            }

            if ( $pages === false || $media === false ){
              return;
            }

            if(count($pages) || count($media)){
                $this->_directionFormStart($lnow,$rnow);
                if(count($pages))
                    $this->_directionForm('pages',$pages);
                if(count($media))
                    $this->_directionForm('media',$media);

                $this->_directionFormEnd();
            }else{
                echo $this->locale_xhtml('nochange');
            }
        }else{
            echo $this->locale_xhtml('intro');

            echo '<div class="sync_left">';
            $this->_profilelist($this->profno);
            if($this->profno !=='' ){
                echo '<br />';
                $this->_profileView($this->profno);
            }
            echo '</div>';
            echo '<div class="sync_right">';
            $this->_profileform($this->profno);
            echo '</div>';
        }
    }

    /**
     * Load profiles from serialized storage
     */
    function _profileLoad(){
        global $conf;
        $profiles = $conf['metadir'].'/sync.profiles';
        if(file_exists($profiles)){
            $this->profiles = unserialize(io_readFile($profiles,false));
        }
    }

    /**
     * Save profiles to serialized storage
     */
    function _profileSave(){
        global $conf;
        $profiles = $conf['metadir'].'/sync.profiles';
        io_saveFile($profiles,serialize($this->profiles));
    }

    /**
     * Check connection for choosen profile and display last sync date.
     */
    function _profileView(){
        if(!$this->_connect()) return false;

        global $conf;
        $no = $this->profno;

        $ok = $this->client->query('dokuwiki.getVersion');
        $version = '';
        if($ok) $version = $this->client->getResponse();

        echo '<form action="" method="post">';
        echo '<input type="hidden" name="no" value="'.hsc($no).'" />';
        echo '<fieldset><legend>'.$this->getLang('syncstart').'</legend>';
        if($version){
            echo '<p>'.$this->getLang('remotever').' '.hsc($version).'</p>';
            if($this->profiles[$no]['ltime']){
                echo '<p>'.$this->getLang('lastsync').' '.strftime($conf['dformat'],$this->profiles[$no]['ltime']).'</p>';
            }else{
                echo '<p>'.$this->getLang('neversync').'</p>';
            }
            echo '<input name="startsync" type="submit" value="'.$this->getLang('syncstart').'" class="button" />';
        }else{
            echo '<p class="error">'.$this->getLang('noconnect').'<br />'.hsc($this->client->getErrorMessage()).'</p>';
        }
        echo '</fieldset>';
        echo '</form>';
    }

    /**
     * Dropdown list of available sync profiles
     */
    function _profilelist($no=''){
        echo '<form action="" method="post">';
        echo '<fieldset><legend>'.$this->getLang('profile').'</legend>';
        echo '<select name="no" class="edit">';
        echo '  <option value="">'.$this->getLang('newprofile').'</option>';
        foreach($this->profiles as $pno => $opts){
            $srv = parse_url($opts['server']);

            echo '<option value="'.hsc($pno).'" '.(($no!=='' && $pno == $no)?'selected="selected"':'').'>';
            echo ($pno+1).'. ';
            if($opts['user']) echo hsc($opts['user']).'@';
            echo hsc($srv['host']);
            if($opts['ns']) echo ':'.hsc($opts['ns']);
            echo '</option>';
        }
        echo '</select>';
        echo '<input type="submit" value="'.$this->getLang('select').'" class="button" />';
        echo '</fieldset>';
        echo '</form>';
    }

    /**
     * Form to edit or create a sync profile
     */
    function _profileform($no=''){
        echo '<form action="" method="post" class="sync_profile">';
        echo '<fieldset><legend>';
        if($no !== ''){
            echo $this->getLang('edit');
        }else{
            echo $this->getLang('create');
        }
        echo '</legend>';

        echo '<input type="hidden" name="no" value="'.hsc($no).'" />';

        echo '<label for="sync__server">'.$this->getLang('server').'</label> ';
        echo '<input type="text" name="prf[server]" id="sync__server" class="edit" value="'.hsc($this->profiles[$no]['server']).'" />';
        echo '<samp>http://example.com/dokuwiki/lib/exe/xmlrpc.php</samp>';

        echo '<label for="sync__ns">'.$this->getLang('ns').'</label> ';
        echo '<input type="text" name="prf[ns]" id="sync__ns" class="edit" value="'.hsc($this->profiles[$no]['ns']).'" />';

        echo '<label for="sync__depth">'.$this->getLang('depth').'</label> ';
        echo '<select name="prf[depth]" id="sync__depth" class="edit">';
        echo '<option value="0" '.(($this->profiles[$no]['depth']==0)?'selected="selected"':'').'>'.$this->getLang('level0').'</option>';
        echo '<option value="1" '.(($this->profiles[$no]['depth']==1)?'selected="selected"':'').'>'.$this->getLang('level1').'</option>';
        echo '<option value="2" '.(($this->profiles[$no]['depth']==2)?'selected="selected"':'').'>'.$this->getLang('level2').'</option>';
        echo '<option value="3" '.(($this->profiles[$no]['depth']==3)?'selected="selected"':'').'>'.$this->getLang('level3').'</option>';
        echo '</select>';


        echo '<label for="sync__user">'.$this->getLang('user').'</label> ';
        echo '<input type="text" name="prf[user]" id="sync__user" class="edit" value="'.hsc($this->profiles[$no]['user']).'" />';

        echo '<label for="sync__pass">'.$this->getLang('pass').'</label> ';
        echo '<input type="password" name="prf[pass]" id="sync__pass" class="edit" value="'.hsc($this->profiles[$no]['pass']).'" />';

        echo '<label for="sync__timeout">'.$this->getLang('timeout').'</label>';
        echo '<input type="number" name="prf[timeout]" id="sync__timeout" class="edit" value="'.hsc($this->profiles[$no]['timeout']).'" />';

        echo '<span>'.$this->getLang('type').'</span>';

        echo '<div class="type">';
        echo '<input type="radio" name="prf[type]" id="sync__type0" value="0" '.(($this->profiles[$no]['type'] == 0)?'checked="checked"':'').'/>';
        echo '<label for="sync__type0">'.$this->getLang('type0').'</label> ';

        echo '<input type="radio" name="prf[type]" id="sync__type1" value="1" '.(($this->profiles[$no]['type'] == 1)?'checked="checked"':'').'/>';
        echo '<label for="sync__type1">'.$this->getLang('type1').'</label> ';

        echo '<input type="radio" name="prf[type]" id="sync__type2" value="2" '.(($this->profiles[$no]['type'] == 2)?'checked="checked"':'').'/>';
        echo '<label for="sync__type2">'.$this->getLang('type2').'</label> ';
        echo '</div>';


        echo '<div class="submit">';
        echo '<input type="submit" value="'.$this->getLang('save').'" class="button" />';
        if($no !== '' && $this->profiles[$no]['ltime']){
            echo '<small>'.$this->getLang('changewarn').'</small>';
        }
        echo '</div>';

        echo '<div class="submit">';
        echo '<input name="sync__delete" type="submit" value="'.$this->getLang('delete').'" class="button" />';
        echo '</div>';

        echo '</fieldset>';
        echo '</form>';
    }

    /**
     * Lock files that will be modified on either side.
     *
     * Lock fails are printed and removed from synclist
     *
     * @returns list of locked files
     */
    function _lockFiles(&$synclist){
        if(!$this->_connect()) return array();
        // lock the files
        $lock = array();
        foreach((array) $synclist as $id => $dir){
            if($dir == 0) continue;
            if(checklock($id)){
                $this->_listOut($this->getLang('lockfail').' '.hsc($id),'error');
                unset($synclist[$id]);
            }else{
                lock($id); // lock local
                $lock[] = $id;
            }
        }
        // lock remote files
        $ok = $this->client->query('dokuwiki.setLocks',array('lock'=>$lock,'unlock'=>array()));
        if(!$ok){
            $this->_listOut('failed RPC communication');
            $synclist = array();
            return array();
        }
        $data = $this->client->getResponse();
        foreach((array) $data['lockfail'] as $id){
            $this->_listOut($this->getLang('lockfail').' '.hsc($id),'error');
            unset($synclist[$id]);
        }

        return $lock;
    }

    /**
     * Print a message as list item using the given class
     */
    function _listOut($msg,$class='ok'){
        echo '<li class="'.hsc($class).'"><div class="li">';
        echo hsc($msg);
        echo "</div></li>\n";
        flush();
        ob_flush();
    }

    /**
     * Execute the sync action and print the results
     */
    function _sync(&$synclist,$type){
        if(!$this->_connect()) return false;
        $no = $this->profno;
        $sum = $_REQUEST['sum'];

        if($type == 'pages')
            $lock = $this->_lockfiles($synclist);

        // do the sync
        foreach((array) $synclist as $id => $dir){
            @set_time_limit(30);
            if($dir == 0){
                $this->_listOut($this->getLang('skipped').' '.$id,'skipped');
                continue;
            }
            if($dir == -2){
                //delete local
                if($type == 'pages'){
                    saveWikiText($id,'',$sum,false);
                    $this->_listOut($this->getLang('localdelok').' '.$id,'del_okay');
                }else{
                    if(unlink(mediaFN($id))){
                        $this->_listOut($this->getLang('localdelok').' '.$id,'del_okay');
                    }else{
                        $this->_listOut($this->getLang('localdelfail').' '.$id,'del_fail');
                    }
                }
                continue;
            }
            if($dir == -1){
                //pull
                if($type == 'pages'){
                    $ok = $this->client->query('wiki.getPage',$id);
                }else{
                    $ok = $this->client->query('wiki.getAttachment',$id);
                }
                if(!$ok){
                    $this->_listOut($this->getLang('pullfail').' '.$id.' '.
                                    $this->client->getErrorMessage(),'pull_fail');
                    continue;
                }
                $data = $this->client->getResponse();
                if($type == 'pages'){
                    saveWikiText($id,$data,$sum,false);
                    idx_addPage($id);
                }else{
                    if($this->apiversion < 7){
                        $data = base64_decode($data);
                    }
                    io_saveFile(mediaFN($id),$data);
                }
                $this->_listOut($this->getLang('pullok').' '.$id,'pull_okay');
                continue;
            }
            if($dir == 1){
                // push
                if($type == 'pages'){
                    $data = rawWiki($id);
                    $ok = $this->client->query('wiki.putPage',$id,$data,array('sum'=>$sum));
                }else{
                    $data = io_readFile(mediaFN($id),false);
                    if($this->apiversion < 6){
                        $data = base64_encode($data);
                    }else{
                        $data = new IXR_Base64($data);
                    }
                    $ok = $this->client->query('wiki.putAttachment',$id,$data,array('ow'=>true));
                }
                if(!$ok){
                    $this->_listOut($this->getLang('pushfail').' '.$id.' '.
                                    $this->client->getErrorMessage(),'push_fail');
                    continue;
                }
                $this->_listOut($this->getLang('pushok').' '.$id,'push_okay');
                continue;
            }
            if($dir == 2){
                // remote delete
                if($type == 'pages'){
                    $ok = $this->client->query('wiki.putPage',$id,'',array('sum'=>$sum));
                }else{
                    $ok = $this->client->query('wiki.deleteAttachment',$id);
                }
                if(!$ok){
                    $this->_listOut($this->getLang('remotedelfail').' '.$id.' '.
                                    $this->client->getErrorMessage(),'del_fail');
                    continue;
                }
                $this->_listOut($this->getLang('remotedelok').' '.$id,'del_okay');
                continue;
            }
        }

        // unlock
        if($type == 'pages'){
            foreach((array) $synclist as $id => $dir){
                unlock($id);
            }
            $this->client->query('dokuwiki.setLocks',array('lock'=>array(),'unlock'=>$lock));
        }


    }

    /**
     * Save synctimes
     */
    function _saveSyncTimes($ltime,$rtime){
        $no = $this->profno;
        list($letime,$retime) = $this->_getTimes();
        $this->profiles[$no]['ltime'] = $ltime;
        $this->profiles[$no]['rtime'] = $rtime;
        $this->profiles[$no]['letime'] = $letime;
        $this->profiles[$no]['retime'] = $retime;
        $this->_profileSave();
    }

    /**
     * Open the sync direction form and initialize the table
     */
    function _directionFormStart($lnow,$rnow){
        $no = $this->profno;
        echo $this->locale_xhtml('list');
        echo '<form action="" method="post">';
        echo '<table class="inline" id="sync__direction__table">';
        echo '<input type="hidden" name="lnow" value="'.$lnow.'" />';
        echo '<input type="hidden" name="rnow" value="'.$rnow.'" />';
        echo '<input type="hidden" name="no" value="'.$no.'" />';
        echo '<tr>
                <th class="sync__file">'.$this->getLang('file').'</th>
                <th class="sync__local">'.$this->getLang('local').'</th>
                <th class="sync__push" id="sync__push">&gt;</th>
                <th class="sync__skip" id="sync__skip">=</th>
                <th class="sync__pull" id="sync__pull">&lt;</th>
                <th class="sync__remote">'.$this->getLang('remote').'</th>
                <th class="sync__diff">'.$this->getLang('diff').'</th>
              </tr>';
    }

    /**
     * Close the direction form and table
     */
    function _directionFormEnd(){
        global $lang;
        echo '</table>';
        echo '<label for="the__summary">'.$lang['summary'].'</label> ';
        echo '<input type="text" name="sum" id="the__summary" value="" class="edit" />';
        echo '<input type="submit" value="'.$this->getLang('syncstart').'" class="button" />';
        echo '</form>';
    }

    /**
     * Print a list of changed files and ask for the sync direction
     *
     * Tries to be clever about suggesting the direction
     */
    function _directionForm($type,&$synclist){
        global $conf;
        global $lang;
        $no = $this->profno;

        $ltime = (int) $this->profiles[$no]['ltime'];
        $rtime = (int) $this->profiles[$no]['rtime'];
        $letime = (int) $this->profiles[$no]['letime'];
        $retime = (int) $this->profiles[$no]['retime'];

        foreach($synclist as $id => $item){
            // check direction
            $dir = 0;
            if($ltime && $rtime){ // synced before
                if($item['remote']['mtime'] > $rtime &&
                   $item['local']['mtime'] <= $letime){
                    $dir = -1;
                }
                if($item['remote']['mtime'] <= $retime &&
                   $item['local']['mtime'] > $ltime){
                    $dir = 1;
                }
            }else{ // never synced
                if(!$item['local']['mtime'] && $item['remote']['mtime']){
                    $dir = -1;
                }
                if($item['local']['mtime'] && !$item['remote']['mtime']){
                    $dir = 1;
                }
            }

            echo '<tr>';

            echo '<td class="sync__file">'.hsc($id).'</td>';
            echo '<td class="sync__local">';
            if(!isset($item['local'])){
                echo '&mdash;';
            }else{
                echo '<div>'.strftime($conf['dformat'],$item['local']['mtime']).'</div>';
                echo ' <div>('.$item['local']['size'].' bytes)</div>';
            }
            echo '</td>';

            echo '<td class="sync__push">';
            if(!isset($item['local'])){
                echo '<input type="radio" name="sync_'.$type.'['.hsc($id).']" value="2" class="syncpush" title="'.$this->getLang('pushdel').'" '.(($dir == 2)?'checked="checked"':'').' />';
            }else{
                echo '<input type="radio" name="sync_'.$type.'['.hsc($id).']" value="1" class="syncpush" title="'.$this->getLang('push').'" '.(($dir == 1)?'checked="checked"':'').' />';
            }
            echo '</td>';
            echo '<td class="sync__skip">';
            echo '<input type="radio" name="sync_'.$type.'['.hsc($id).']" value="0" class="syncskip" title="'.$this->getLang('keep').'" '.(($dir == 0)?'checked="checked"':'').' />';
            echo '</td>';
            echo '<td class="sync__pull">';
            if(!isset($item['remote'])){
                echo '<input type="radio" name="sync_'.$type.'['.hsc($id).']" value="-2" class="syncpull" title="'.$this->getLang('pulldel').'" '.(($dir == -2)?'checked="checked"':'').' />';
            }else{
                echo '<input type="radio" name="sync_'.$type.'['.hsc($id).']" value="-1" class="syncpull" title="'.$this->getLang('pull').'" '.(($dir == -1)?'checked="checked"':'').' />';
            }
            echo '</td>';

            echo '<td class="sync__remote">';
            if(!isset($item['remote'])){
                echo '&mdash;';
            }else{
                echo '<div>'.strftime($conf['dformat'],$item['remote']['mtime']).'</div>';
                echo ' <div>('.$item['remote']['size'].' bytes)</div>';
            }
            echo '</td>';

            echo '<td class="sync__diff">';
            if($type == 'pages'){
                echo '<a href="'.DOKU_BASE.'lib/plugins/sync/diff.php?id='.$id.'&amp;no='.$no.'" target="_blank" class="sync_popup">'.$this->getLang('diff').'</a>';
            }
            echo '</td>';

            echo '</tr>';
        }
    }

    /**
     * Get the local and remote time
     */
    function _getTimes(){
        if(!$this->_connect()) return false;
        // get remote time
        $ok = $this->client->query('dokuwiki.getTime');
        if(!$ok){
            msg('Failed to fetch remote time. '.
                $this->client->getErrorMessage(),-1);
            return false;
        }
        $rtime = $this->client->getResponse();
        $ltime = time();
        return array($ltime,$rtime);
    }

    /**
     * Get a list of changed files
     */
    function _getSyncList($type='pages'){
        if(!$this->_connect()) return array();
        global $conf;
        $no = $this->profno;
        $list = array();
        $ns = $this->profiles[$no]['ns'];

        // get remote file list
        if($type == 'pages'){
            $ok = $this->client->query('dokuwiki.getPagelist',$ns,
                    array('depth' => (int) $this->profiles[$no]['depth'],
                          'hash' => true));
        }else{
            $ok = $this->client->query('wiki.getAttachments',$ns,
                    array('depth' => (int) $this->profiles[$no]['depth'],
                          'hash' => true));
        }
        if(!$ok){
            msg('Failed to fetch remote file list. '.
                $this->client->getErrorMessage(),-1);
            return false;
        }
        $remote = $this->client->getResponse();
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
        if($type == 'pages'){
            search($local, $conf['datadir'], 'search_allpages',
                    array('depth' => (int) $this->profiles[$no]['depth'],
                          'hash' => true), $dir);
        }else{
            search($local, $conf['mediadir'], 'search_media',
                    array('depth' => (int) $this->profiles[$no]['depth'],
                          'hash' => true), $dir);
        }

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

    /**
     * show diff between the local and remote versions of the page
     */
    function _diff($id){
        if(!$this->_connect()) return false;
        $no = $this->profno;

        $ok = $this->client->query('wiki.getPage',$id);
        if(!$ok){
            echo $this->getLang('pullfail').' '.hsc($id).' ';
            echo hsc($this->client->getErrorMessage());
            die();
        }
        $remote = $this->client->getResponse();
        $local  = rawWiki($id);

        $df = new Diff(explode("\n",htmlspecialchars($local)),
                       explode("\n",htmlspecialchars($remote)));

        $tdf = new TableDiffFormatter();
        echo '<table class="diff">';
        echo '<tr>';
        echo '<th colspan="2">'.$this->getLang('local').'</th>';
        echo '<th colspan="2">'.$this->getLang('remote').'</th>';
        echo '</tr>';
        echo $tdf->format($df);
        echo '</table>';
    }
}
//Setup VIM: ex: et ts=4 enc=utf-8 :
