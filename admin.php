<?php
use dokuwiki\Form\Form;
use dokuwiki\plugin\sync\ProfileManager;
use dokuwiki\plugin\sync\SyncException;

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_sync extends DokuWiki_Admin_Plugin {

    /** @var IXR_Client */
    protected $client         = null;
    protected $apiversion     = 0;
    protected $defaultTimeout = 15;

    protected $profileManager;
    protected $profno;

    /**
     * Constructor.
     */
    function __construct() {
        global $INPUT;

        $this->profileManager = new ProfileManager();
        if($INPUT->str('no') == 'none') {
            $this->profno = -1;
        } else {
            $this->profno = $INPUT->int('no', -1);
        }
    }

    /**
     * return sort order for position in admin menu
     */
    function getMenuSort() {
        return 1020;
    }

    function _connect() {
        if(!is_null($this->client)) return true;
        $this->client = new IXR_Client($this->profiles[$this->profno]['server']);
        if(isset($this->profiles[$this->profno]['timeout'])) {
            $timeout = (int) $this->profiles[$this->profno]['timeout'];
        } else {
            $timeout = $this->defaultTimeout;
        }
        $this->client->timeout = $timeout;

        // do the login
        if($this->profiles[$this->profno]['user']) {
            $ok = $this->client->query(
                'dokuwiki.login',
                $this->profiles[$this->profno]['user'],
                $this->profiles[$this->profno]['pass']
            );
            if(!$ok) {
                msg($this->getLang('xmlerr') . ' ' . hsc($this->client->getErrorMessage()), -1);
                $this->client = null;
                return false;
            }
            if(!$this->client->getResponse()) {
                msg($this->getLang('loginerr'), -1);
                $this->client = null;
                return false;
            }
        }

        $ok = $this->client->query('dokuwiki.getXMLRPCAPIVersion');
        if(!$ok) {
            msg($this->getLang('xmlerr') . ' ' . hsc($this->client->getErrorMessage()), -1);
            $this->client = null;
            return false;
        }
        $this->apiversion = (int) $this->client->getResponse();
        if($this->apiversion < 1) {
            msg($this->getLang('versionerr'), -1);
            $this->client = null;
            return false;
        }

        return true;
    }

    /**
     * handle profile saving/deleting
     */
    function handle() {
        global $INPUT;

        $profile = $INPUT->arr('prf');
        if(!$profile) return;

        if(!checkSecurityToken()) return;

        try {
            if($INPUT->has('sync__delete') && $this->profno !== false) {
                // profile deletion
                $this->profileManager->deleteProfileConfig($this->profno);
                $this->profno = false;
                $INPUT->remove('prf');
                msg('profile deleted', 1);
            } else {
                // profile add/edit
                $this->profno = $this->profileManager->setProfileConfig($this->profno, $profile);
                msg('profile saved', 1);
            }
        } catch(SyncException $e) {
            msg(hsc($e->getMessage()), -1);
        }
    }

    /**
     * output appropriate html
     */
    function html() {
        global $INPUT;
        if($INPUT->bool('startsync') && $this->profno != -1 && checkSecurityToken()) {
            // fixme display an intro

            $data = [
                'profile' => $this->profno
            ];
            echo '<script type="application/javascript">';
            echo 'SYNC_DATA = ' . json_encode($data) . ';';
            echo '</script>';

            echo '<script src="' . DOKU_BASE . 'lib/plugins/sync/sync.js" type="text/javascript"></script>';

            echo '<div id="sync__progress"><div class="label"></div></div>';
            echo '<div id="sync__plugin"></div>';

        } else {
            echo $this->locale_xhtml('intro');

            echo '<div id="sync__plugin__form">';
            echo '<div class="sync_left">';
            $this->profileDropdown();
            if($this->profno !== -1) {
                echo '<br />';
                $this->profileInfo();
            }
            echo '</div>';
            echo '<div class="sync_right">';
            $this->profileForm();
            echo '</div>';
            echo '</div>';
        }
    }

    /**
     * Check connection for choosen profile and display last sync date.
     */
    protected function profileInfo() {
        try {
            $profile = $this->profileManager->getProfile($this->profno);
            $version = $profile->getRemotVersion();
            $ltime = $profile->getConfig('ltime');
        } catch(SyncException $e) {
            echo '<div class="error">' . $this->getLang('noconnect') . '<br />' . hsc($e->getMessage()) . '</div>';
            return;
        }

        $form = new Form(
            [
                'action' => wl('', false, '&'),
                'method' => 'GET',
            ]
        );
        $form->setHiddenField('no', $this->profno);
        $form->setHiddenField('do', 'admin');
        $form->setHiddenField('page', 'sync');
        $form->addFieldsetOpen($this->getLang('syncstart'));
        $form->addHTML('<p>' . $this->getLang('remotever') . ' ' . hsc($version) . '</p>');
        if($ltime) {
            $form->addHTML('<p>' . $this->getLang('lastsync') . ' ' . dformat($ltime) . '</p>');
        } else {
            $form->addHTML('<p>' . $this->getLang('neversync') . '</p>');
        }
        $form->addButton('startsync', $this->getLang('syncstart'))->attr('type', 'submit');
        $form->addFieldsetClose();
        echo $form->toHTML();
    }

    /**
     * Dropdown list of available sync profiles
     */
    protected function profileDropdown() {
        $form = new Form(
            [
                'action' => wl('', ['do' => 'admin', 'page' => 'sync'], false, '&'),
                'method' => 'POST',
            ]
        );

        $profiles = ['none' => $this->getLang('newprofile')];
        $profiles = array_merge($profiles, $this->profileManager->getProfileLabels());

        $form->addFieldsetOpen($this->getLang('profile'));
        $form->addDropdown('no', $profiles)->val($this->profno);
        $form->addButton('', $this->getLang('select'));
        $form->addFieldsetClose();

        echo $form->toHTML();
    }

    /**
     * Form to edit or create a sync profile
     */
    protected function profileForm() {
        $form = new Form(
            [
                'action' => wl('', ['do' => 'admin', 'page' => 'sync'], false, '&'),
                'method' => 'POST',
                'class' => 'sync_profile',
            ]
        );

        if($this->profno === -1) {
            $legend = $this->getLang('create');
            $profile = $this->profileManager->getEmptyConfig();
        } else {
            $legend = $this->getLang('edit');
            $profile = $this->profileManager->getProfileConfig($this->profno);
        }

        $depths = [
            ['label' => $this->getLang('level0')],
            ['label' => $this->getLang('level1')],
            ['label' => $this->getLang('level2')],
            ['label' => $this->getLang('level3')],
        ];
        $types = [
            ['label' => $this->getLang('type0')],
            ['label' => $this->getLang('type1')],
            ['label' => $this->getLang('type2')],
        ];

        $form->addFieldsetOpen($legend);
        $form->setHiddenField('no', $this->profno);
        $form->addTextInput('prf[server]', $this->getLang('server'))->val($profile['server']);
        $form->addHTML('<samp>http://example.com/dokuwiki/lib/exe/xmlrpc.php</samp>');
        $form->addTextInput('prf[ns]', $this->getLang('ns'))->val($profile['ns']);
        $form->addDropdown('prf[depth]', $depths, $this->getLang('depth'))->val($profile['depth']);
        $form->addTextInput('prf[user]', $this->getLang('user'))->val($profile['user']);
        $form->addPasswordInput('prf[pass]', $this->getLang('pass'))->val($profile['pass']);
        $form->addTextInput('prf[timeout]', $this->getLang('timeout'))->val($profile['timeout']);
        $form->addDropdown('prf[type]', $types, $this->getLang('type'))->val($profile['type']);
        $form->addButton('', $this->getLang('save'))->attr('type', 'submit');

        if($this->profno !== -1 && !empty($profile['ltime'])) {
            echo '<small>' . $this->getLang('changewarn') . '</small>';
        }

        $form->addFieldsetClose();

        if($this->profno !== -1) {
            $form->addFieldsetOpen($this->getLang('delete'));
            $form->addButton('sync__delete', $this->getLang('delete'));
            $form->addFieldsetClose();
        }

        echo $form->toHTML();
    }

    /**
     * Load profiles from serialized storage
     */
    function _profileLoad() {
        global $conf;
        $profiles = $conf['metadir'] . '/sync.profiles';
        if(file_exists($profiles)) {
            $this->profiles = unserialize(io_readFile($profiles, false));
        }
    }

    /**
     * Save profiles to serialized storage
     */
    function _profileSave() {
        global $conf;
        $profiles = $conf['metadir'] . '/sync.profiles';
        io_saveFile($profiles, serialize($this->profiles));
    }

    /**
     * Lock files that will be modified on either side.
     *
     * Lock fails are printed and removed from synclist
     *
     * @return array list of locked files
     */
    function _lockFiles(&$synclist) {
        if(!$this->_connect()) return array();
        // lock the files
        $lock = array();
        foreach((array) $synclist as $id => $dir) {
            if($dir == 0) continue;
            if(checklock($id)) {
                $this->_listOut($this->getLang('lockfail') . ' ' . hsc($id), 'error');
                unset($synclist[$id]);
            } else {
                lock($id); // lock local
                $lock[] = $id;
            }
        }
        // lock remote files
        $ok = $this->client->query('dokuwiki.setLocks', array('lock' => $lock, 'unlock' => array()));
        if(!$ok) {
            $this->_listOut('failed RPC communication');
            $synclist = array();
            return array();
        }
        $data = $this->client->getResponse();
        foreach((array) $data['lockfail'] as $id) {
            $this->_listOut($this->getLang('lockfail') . ' ' . hsc($id), 'error');
            unset($synclist[$id]);
        }

        return $lock;
    }

    /**
     * Print a message as list item using the given class
     */
    function _listOut($msg, $class = 'ok') {
        echo '<li class="' . hsc($class) . '"><div class="li">';
        echo hsc($msg);
        echo "</div></li>\n";
        flush();
        ob_flush();
    }

    /**
     * Execute the sync action and print the results
     * @param array $synclist
     * @param string $type
     */
    function _sync(&$synclist, $type) {
        if(!$this->_connect()) return;
        $sum = $_REQUEST['sum'];

        if($type == 'pages')
            $lock = $this->_lockfiles($synclist);

        // do the sync
        foreach((array) $synclist as $id => $dir) {
            @set_time_limit(30);
            if($dir == 0) {
                $this->_listOut($this->getLang('skipped') . ' ' . $id, 'skipped');
                continue;
            }
            if($dir == -2) {
                //delete local
                if($type == 'pages') {
                    saveWikiText($id, '', $sum, false);
                    $this->_listOut($this->getLang('localdelok') . ' ' . $id, 'del_okay');
                } else {
                    if(unlink(mediaFN($id))) {
                        $this->_listOut($this->getLang('localdelok') . ' ' . $id, 'del_okay');
                    } else {
                        $this->_listOut($this->getLang('localdelfail') . ' ' . $id, 'del_fail');
                    }
                }
                continue;
            }
            if($dir == -1) {
                //pull
                if($type == 'pages') {
                    $ok = $this->client->query('wiki.getPage', $id);
                } else {
                    $ok = $this->client->query('wiki.getAttachment', $id);
                }
                if(!$ok) {
                    $this->_listOut(
                        $this->getLang('pullfail') . ' ' . $id . ' ' .
                        $this->client->getErrorMessage(), 'pull_fail'
                    );
                    continue;
                }
                $data = $this->client->getResponse();
                if($type == 'pages') {
                    saveWikiText($id, $data, $sum, false);
                    idx_addPage($id);
                } else {
                    if($this->apiversion < 7) {
                        $data = base64_decode($data);
                    }
                    io_saveFile(mediaFN($id), $data);
                }
                $this->_listOut($this->getLang('pullok') . ' ' . $id, 'pull_okay');
                continue;
            }
            if($dir == 1) {
                // push
                if($type == 'pages') {
                    $data = rawWiki($id);
                    $ok = $this->client->query('wiki.putPage', $id, $data, array('sum' => $sum));
                } else {
                    $data = io_readFile(mediaFN($id), false);
                    if($this->apiversion < 6) {
                        $data = base64_encode($data);
                    } else {
                        $data = new IXR_Base64($data);
                    }
                    $ok = $this->client->query('wiki.putAttachment', $id, $data, array('ow' => true));
                }
                if(!$ok) {
                    $this->_listOut(
                        $this->getLang('pushfail') . ' ' . $id . ' ' .
                        $this->client->getErrorMessage(), 'push_fail'
                    );
                    continue;
                }
                $this->_listOut($this->getLang('pushok') . ' ' . $id, 'push_okay');
                continue;
            }
            if($dir == 2) {
                // remote delete
                if($type == 'pages') {
                    $ok = $this->client->query('wiki.putPage', $id, '', array('sum' => $sum));
                } else {
                    $ok = $this->client->query('wiki.deleteAttachment', $id);
                }
                if(!$ok) {
                    $this->_listOut(
                        $this->getLang('remotedelfail') . ' ' . $id . ' ' .
                        $this->client->getErrorMessage(), 'del_fail'
                    );
                    continue;
                }
                $this->_listOut($this->getLang('remotedelok') . ' ' . $id, 'del_okay');
                continue;
            }
        }

        // unlock
        if($type == 'pages') {
            foreach((array) $synclist as $id => $dir) {
                unlock($id);
            }
            $this->client->query('dokuwiki.setLocks', array('lock' => array(), 'unlock' => $lock));
        }

    }

    /**
     * Save synctimes
     *
     * @param int $ltime local start time
     * @param int $rtime remote start time
     */
    function _saveSyncTimes($ltime, $rtime) {
        $no = $this->profno;
        list($letime, $retime) = $this->_getTimes();
        $this->profiles[$no]['ltime'] = $ltime;
        $this->profiles[$no]['rtime'] = $rtime;
        $this->profiles[$no]['letime'] = $letime;
        $this->profiles[$no]['retime'] = $retime;
        $this->_profileSave();
    }

    /**
     * Open the sync direction form and initialize the table
     */
    function _directionFormStart($lnow, $rnow) {
        $no = $this->profno;
        echo $this->locale_xhtml('list');
        echo '<form action="" method="post">';
        echo '<table class="inline" id="sync__direction__table">';
        echo '<input type="hidden" name="lnow" value="' . $lnow . '" />';
        echo '<input type="hidden" name="rnow" value="' . $rnow . '" />';
        echo '<input type="hidden" name="no" value="' . $no . '" />';
        echo '<tr>
                <th class="sync__file">' . $this->getLang('file') . '</th>
                <th class="sync__local">' . $this->getLang('local') . '</th>
                <th class="sync__push" id="sync__push">&gt;</th>
                <th class="sync__skip" id="sync__skip">=</th>
                <th class="sync__pull" id="sync__pull">&lt;</th>
                <th class="sync__remote">' . $this->getLang('remote') . '</th>
                <th class="sync__diff">' . $this->getLang('diff') . '</th>
              </tr>';
    }

    /**
     * Close the direction form and table
     */
    function _directionFormEnd() {
        global $lang;
        echo '</table>';
        echo '<label for="the__summary">' . $lang['summary'] . '</label> ';
        echo '<input type="text" name="sum" id="the__summary" value="" class="edit" />';
        echo '<input type="submit" value="' . $this->getLang('syncstart') . '" class="button" />';
        echo '</form>';
    }

    /**
     * Print a list of changed files and ask for the sync direction
     *
     * Tries to be clever about suggesting the direction
     * @param string $type media or page
     * @param array $synclist list of files to sync
     */
    function _directionForm($type, &$synclist) {
        global $conf;
        $no = $this->profno;

        $ltime = (int) $this->profiles[$no]['ltime'];
        $rtime = (int) $this->profiles[$no]['rtime'];
        $letime = (int) $this->profiles[$no]['letime'];
        $retime = (int) $this->profiles[$no]['retime'];

        foreach($synclist as $id => $item) {
            // check direction
            $dir = 0;
            if($ltime && $rtime) { // synced before
                if($item['remote']['mtime'] > $rtime &&
                    $item['local']['mtime'] <= $letime
                ) {
                    $dir = -1;
                }
                if($item['remote']['mtime'] <= $retime &&
                    $item['local']['mtime'] > $ltime
                ) {
                    $dir = 1;
                }
            } else { // never synced
                if(!$item['local']['mtime'] && $item['remote']['mtime']) {
                    $dir = -1;
                }
                if($item['local']['mtime'] && !$item['remote']['mtime']) {
                    $dir = 1;
                }
            }

            echo '<tr>';

            echo '<td class="sync__file">' . hsc($id) . '</td>';
            echo '<td class="sync__local">';
            if(!isset($item['local'])) {
                echo '&mdash;';
            } else {
                echo '<div>' . strftime($conf['dformat'], $item['local']['mtime']) . '</div>';
                echo ' <div>(' . $item['local']['size'] . ' bytes)</div>';
            }
            echo '</td>';

            echo '<td class="sync__push">';
            if(!isset($item['local'])) {
                echo '<input type="radio" name="sync_' . $type . '[' . hsc($id) . ']" value="2" class="syncpush" title="' . $this->getLang('pushdel') . '" ' . (($dir == 2) ? 'checked="checked"' : '') . ' />';
            } else {
                echo '<input type="radio" name="sync_' . $type . '[' . hsc($id) . ']" value="1" class="syncpush" title="' . $this->getLang('push') . '" ' . (($dir == 1) ? 'checked="checked"' : '') . ' />';
            }
            echo '</td>';
            echo '<td class="sync__skip">';
            echo '<input type="radio" name="sync_' . $type . '[' . hsc($id) . ']" value="0" class="syncskip" title="' . $this->getLang('keep') . '" ' . (($dir == 0) ? 'checked="checked"' : '') . ' />';
            echo '</td>';
            echo '<td class="sync__pull">';
            if(!isset($item['remote'])) {
                echo '<input type="radio" name="sync_' . $type . '[' . hsc($id) . ']" value="-2" class="syncpull" title="' . $this->getLang('pulldel') . '" ' . (($dir == -2) ? 'checked="checked"' : '') . ' />';
            } else {
                echo '<input type="radio" name="sync_' . $type . '[' . hsc($id) . ']" value="-1" class="syncpull" title="' . $this->getLang('pull') . '" ' . (($dir == -1) ? 'checked="checked"' : '') . ' />';
            }
            echo '</td>';

            echo '<td class="sync__remote">';
            if(!isset($item['remote'])) {
                echo '&mdash;';
            } else {
                echo '<div>' . strftime($conf['dformat'], $item['remote']['mtime']) . '</div>';
                echo ' <div>(' . $item['remote']['size'] . ' bytes)</div>';
            }
            echo '</td>';

            echo '<td class="sync__diff">';
            if($type == 'pages') {
                echo '<a href="' . DOKU_BASE . 'lib/plugins/sync/diff.php?id=' . $id . '&amp;no=' . $no . '" target="_blank" class="sync_popup">' . $this->getLang('diff') . '</a>';
            }
            echo '</td>';

            echo '</tr>';
        }
    }

    /**
     * Get the local and remote time
     */
    function _getTimes() {
        if(!$this->_connect()) return false;
        // get remote time
        $ok = $this->client->query('dokuwiki.getTime');
        if(!$ok) {
            msg(
                'Failed to fetch remote time. ' .
                $this->client->getErrorMessage(), -1
            );
            return false;
        }
        $rtime = $this->client->getResponse();
        $ltime = time();
        return array($ltime, $rtime);
    }

    /**
     * Get a list of changed files
     */
    function _getSyncList($type = 'pages') {
        if(!$this->_connect()) return array();
        global $conf;
        $no = $this->profno;
        $list = array();
        $ns = $this->profiles[$no]['ns'];

        // get remote file list
        if($type == 'pages') {
            $ok = $this->client->query(
                'dokuwiki.getPagelist', $ns,
                array(
                    'depth' => (int) $this->profiles[$no]['depth'],
                    'hash' => true
                )
            );
        } else {
            $ok = $this->client->query(
                'wiki.getAttachments', $ns,
                array(
                    'depth' => (int) $this->profiles[$no]['depth'],
                    'hash' => true
                )
            );
        }
        if(!$ok) {
            msg(
                'Failed to fetch remote file list. ' .
                $this->client->getErrorMessage(), -1
            );
            return false;
        }
        $remote = $this->client->getResponse();
        // put into synclist
        foreach($remote as $item) {
            $list[$item['id']]['remote'] = $item;
            unset($list[$item['id']]['remote']['id']);
        }
        unset($remote);

        // get local file list
        $local = array();
        $dir = utf8_encodeFN(str_replace(':', '/', $ns));
        require_once(DOKU_INC . 'inc/search.php');
        if($type == 'pages') {
            search(
                $local, $conf['datadir'], 'search_allpages',
                array(
                    'depth' => (int) $this->profiles[$no]['depth'],
                    'hash' => true
                ), $dir
            );
        } else {
            search(
                $local, $conf['mediadir'], 'search_media',
                array(
                    'depth' => (int) $this->profiles[$no]['depth'],
                    'hash' => true
                ), $dir
            );
        }

        // put into synclist
        foreach($local as $item) {
            // skip identical files
            if($list[$item['id']]['remote']['hash'] == $item['hash']) {
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
     *
     * @param $id
     */
    function _diff($id) {
        if(!$this->_connect()) return;

        $ok = $this->client->query('wiki.getPage', $id);
        if(!$ok) {
            echo $this->getLang('pullfail') . ' ' . hsc($id) . ' ';
            echo hsc($this->client->getErrorMessage());
            die();
        }
        $remote = $this->client->getResponse();
        $local = rawWiki($id);

        $df = new Diff(
            explode("\n", htmlspecialchars($local)),
            explode("\n", htmlspecialchars($remote))
        );

        $tdf = new TableDiffFormatter();
        echo '<table class="diff">';
        echo '<tr>';
        echo '<th colspan="2">' . $this->getLang('local') . '</th>';
        echo '<th colspan="2">' . $this->getLang('remote') . '</th>';
        echo '</tr>';
        echo $tdf->format($df);
        echo '</table>';
    }
}
//Setup VIM: ex: et ts=4 enc=utf-8 :
