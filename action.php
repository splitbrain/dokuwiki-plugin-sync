<?php

use dokuwiki\plugin\sync\ProfileManager;
use dokuwiki\plugin\sync\SyncException;

class action_plugin_sync extends DokuWiki_Action_Plugin {

    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this, 'handle_ajax');
    }

    /**
     * Dispatch the AJAX calls
     *
     * @param Doku_Event $e
     */
    public function handle_ajax(Doku_Event $e) {
        $call = $e->data;
        if(substr($call, 0, 5) !== 'sync_') return;
        $e->preventDefault();
        $e->stopPropagation();

        try {
            $data = $this->$call();
            header('Content-Type: application/json');
            echo json_encode($data);
        } catch(SyncException $e) {
            http_status(500);
            header('Content-Type: text/plain');
            echo $e->getMessage();
        }
    }

    /**
     * Syncs a single file
     *
     * @return bool
     */
    protected function sync_file() {
        global $INPUT;

        $prmanager = new ProfileManager();
        $profno = $INPUT->int('no');

        $profile = $prmanager->getProfile($profno);
        $profile->syncFile(
            $INPUT->int('type'),
            $INPUT->filter('cleanID')->str('id'),
            $INPUT->int('dir'),
            'FIXME'
        );

        return true;
    }

    /**
     * Initializes the sync by fetching the synclist and the inital times
     *
     * @return array
     */
    protected function sync_init() {
        global $INPUT;
        $prmanager = new ProfileManager();
        $profno = $INPUT->int('no');

        $profile = $prmanager->getProfile($profno);
        $list = $profile->getSyncList();
        $times = $profile->getTimes();
        $count = 0;

        foreach($list as $items) {
            $count += count($items);
        }

        $data = [
            'times' => [
                'ltime' => $times[0],
                'rtime' => $times[1],
            ],
            'list' => $list,
            'count' => $count
        ];

        return $data;
    }

    /**
     * Store the times of the last sync
     *
     * @return bool
     */
    protected function sync_finish() {
        global $INPUT;
        $prmanager = new ProfileManager();
        $profno = $INPUT->int('no');

        $profile = $prmanager->getProfile($profno);
        $times = $profile->getTimes();

        $config = $profile->getConfig();

        $config['ltime'] = $INPUT->int('ltime');
        $config['rtime'] = $INPUT->int('rtime');
        $config['letime'] = $times[0];
        $config['retime'] = $times[1];

        $prmanager->setProfileConfig($profno, $config);
        return true;
    }
}
