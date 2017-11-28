<?php

namespace dokuwiki\plugin\sync;

class Profile {

    const DIR_PULL = -1;
    const DIR_PULL_DEL = -2;
    const DIR_PUSH = 1;
    const DIR_PUSH_DEL = 2;
    const DIR_NONE = 0;

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
        $this->fetchRemoteFileList($type);
        $this->fetchLocalFileList($type);
        $this->consolidateSyncList($type);

        ksort($this->synclist); #FIXME implement our own sort with dir=0 at the top

        return $this->synclist;
    }

    /**
     * Synchronise the given file
     *
     * @param string $type pages|media
     * @param string $id the ID of the page or media
     * @param int $dir the sync direction
     * @param string $summary the editing summary
     */
    public function syncFile($type, $id, $dir, $summary) {
        switch($dir) {
            case self::DIR_PULL:
                $this->syncPull($type, $id, $summary);
                break;
            case self::DIR_PULL_DEL:
                $this->syncPullDelete($type, $id, $summary);
                break;
            case self::DIR_PUSH:
                $this->syncPush($type, $id, $summary);
                break;
            case self::DIR_PUSH_DEL:
                $this->syncPushDelete($type, $id, $summary);
                break;
            default:
                // skip
        }
    }

    /**
     * Sync from remote to local
     *
     * @param string $type pages|media
     * @param string $id the ID of the page or media
     * @param string $summary the editing summary
     * @throws FatalException
     */
    protected function syncPull($type, $id, $summary) {
        if($type == 'pages') {
            if(checklock($id)) throw new FatalException('Local file is locked');
            $this->client->query('wiki.getPage', $id);
        } else {
            $this->client->query('wiki.getAttachment', $id);
        }

        $data = $this->client->getResponse();
        if($type == 'pages') {
            saveWikiText($id, $data, $summary, false);
            idx_addPage($id);
        } else {
            io_saveFile(mediaFN($id), $data); #FIXME what about summary etc.
        }
    }

    /**
     * Delete a local file
     *
     * @param string $type pages|media
     * @param string $id the ID of the page or media
     * @param string $summary the editing summary
     * @throws FatalException
     */
    protected function syncPullDelete($type, $id, $summary) {
        if($type == 'pages') {
            if(checklock($id)) throw new FatalException('Local file is locked');
            saveWikiText($id, '', $summary, false);
        } else {
            if(!unlink(mediaFN($id))) throw new FatalException('File deletion failed');
        }
    }

    /**
     * Sync from local to remote
     *
     * @param string $type pages|media
     * @param string $id the ID of the page or media
     * @param string $summary the editing summary
     * @throws FatalException
     */
    protected function syncPush($type, $id, $summary) {
        if($type == 'pages') {
            $this->setRemoteLock($id, true);
            $data = rawWiki($id);
            $this->client->query('wiki.putPage', $id, $data, array('sum' => $summary));
            $this->setRemoteLock($id, false);
        } else {
            $data = io_readFile(mediaFN($id), false);
            $data = new \IXR_Base64($data);
            $this->client->query('wiki.putAttachment', $id, $data, array('ow' => true)); #FIXME what about the summary
        }
    }

    /**
     * Delete a remote file
     *
     * @param string $type pages|media
     * @param string $id the ID of the page or media
     * @param string $summary the editing summary
     * @throws FatalException
     */
    protected function syncPushDelete($type, $id, $summary) {
        if($type == 'pages') {
            $this->setRemoteLock($id, true);
            $this->client->query('wiki.putPage', $id, '', array('sum' => $summary));
            $this->setRemoteLock($id, false);
        } else {
            $this->client->query('wiki.deleteAttachment', $id); #FIXME what about the summary
        }
    }

    /**
     * Lock or unlock a remote page
     *
     * @param string $id
     * @param bool $state is this a lock (true) or unlock (false)
     * @throws FatalException
     */
    protected function setRemoteLock($id, $state) {
        if($state) {
            $lock = [$id];
            $unlock = [];
        } else {
            $lock = [];
            $unlock = [$id];
        }

        $this->client->query('dokuwiki.setLocks', array('lock' => $lock, 'unlock' => $unlock));
        $data = $this->client->getResponse();
        if(count((array) $data['lockfail'])) {
            throw new FatalException('Locking at the remote wiki failed');
        }
    }

    /**
     * put all remote files for this profile into the sync list
     *
     * @param string $type pages|media
     */
    protected function fetchRemoteFileList($type) {
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
    protected function fetchLocalFileList($type) {
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
            $dir = self::DIR_NONE;
            if($ltime && $rtime) { // synced before
                if($item['remote']['mtime'] > $rtime &&
                    $item['local']['mtime'] <= $letime
                ) {
                    $dir = self::DIR_PULL;
                }
                if($item['remote']['mtime'] <= $retime &&
                    $item['local']['mtime'] > $ltime
                ) {
                    $dir = self::DIR_PUSH;
                }
            } else { // never synced
                if(!$item['local']['mtime'] && $item['remote']['mtime']) {
                    $dir = self::DIR_PULL;
                }
                if($item['local']['mtime'] && !$item['remote']['mtime']) {
                    $dir = self::DIR_PUSH;
                }
            }
            $this->synclist[$type][$item['id']]['dir'] = $dir;
        }
    }

}
