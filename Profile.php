<?php

namespace dokuwiki\plugin\sync;

class Profile {

    /** @var array hold the profile configuration */
    protected $config;
    /** @var Client the API client */
    protected $client;
    /** @var  array the options we use to query the files to sync */
    protected $syncoptions;
    /** @var  array the list of files to sync */
    protected $synclist;

    /**
     * Profile constructor.
     * @param array $config
     */
    public function __construct($config) {
        $this->config = $config;

        $this->syncoptions = [
            'depth' => (int) $config['depth'],
            'hash' => true,
        ];

        $this->client = new Client(
            $config['server'],
            $config['user'],
            $config['pass'],
            $config['timeout'] ?: 15
        );
    }

    /**
     * Get a list of changed files
     */
    public function getSyncList($type) {
        $this->getRemoteFileList($type);
        $this->getLocalFileList($type);
        $this->consolidateSyncList($type);

        ksort($this->synclist); #FIXME implement our own sort with dir=0 at the top

        return $this->synclist;
    }

    /**
     * put all remote files for this profile into the sync list
     *
     * @param string $type pages|media
     */
    protected function getRemoteFileList($type) {
        if($type == 'pages') {
            $cmd = 'dokuwiki.getPagelist';
        } else {
            $cmd = 'wiki.getAttachments';
        }

        $this->client->query($cmd, $this->config['ns'], $this->syncoptions);
        $remote = $this->client->getResponse();

        // put into synclist
        foreach($remote as $item) {
            $this->synclist[$type][$item['id']]['remote'] = $item;
        }
    }

    /**
     * put all local files for this profile into the sync list
     *
     * @param string $type pages|media
     */
    protected function getLocalFileList($type) {
        global $conf;

        if($type == 'pages') {
            $basedir = $conf['datadir'];
            $cmd = 'search_allpages';
        } else {
            $basedir = $conf['mediadir'];
            $cmd = 'search_media';
        }

        $local = array();
        $dir = utf8_encodeFN(str_replace(':', '/', $this->config['ns']));
        search($local, $basedir, $cmd, $this->syncoptions, $dir);

        // put into synclist
        foreach($local as $item) {
            $this->synclist[$type][$item['id']]['local'] = $item;
        }
    }

    /**
     * removes all files that do not need syncing and calulates direction for the rest
     *
     * @param $type
     */
    protected function consolidateSyncList($type) {
        // synctimes
        $ltime = (int) $this->config['ltime'];
        $rtime = (int) $this->config['rtime'];
        $letime = (int) $this->config['letime'];
        $retime = (int) $this->config['retime'];

        foreach($this->synclist[$type] as $id => $item) {

            // no sync if hashes match
            if($item['remote']['hash'] == $item['local']['hash']) {
                unset($this->synclist[$type][$item['id']]);
                continue;
            }

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
            $this->synclist[$type][$item['id']]['dir'] = $dir;
        }
    }

}
