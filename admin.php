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

    /** @var ProfileManager */
    protected $profileManager;
    /** @var int */
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

            echo '<h1>' . $this->getLang('menu') . '</h1>';
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

}
