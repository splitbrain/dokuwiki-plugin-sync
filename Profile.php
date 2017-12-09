<?php

namespace dokuwiki\plugin\sync;

class Profile {

    const DIR_PULL = -1;
    const DIR_PULL_DEL = -2;
    const DIR_PUSH = 1;
    const DIR_PUSH_DEL = 2;
    const DIR_NONE = 0;

    const TYPE_BOTH = 0;
    const TYPE_PAGES = 1;
    const TYPE_MEDIA = 2;

    /** @var array hold the profile configuration */
    protected $config;
    /** @var Client the API client */
    protected $client;
    /** @var  array the options we use to query the files to sync */
    protected $syncoptions;
    /** @var  array the list of files to sync */
    protected $synclist = [];

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
     * Access the config that initialized this profile
     *
     * @param string|null $key when a key is given only that key's config value is returned
     * @return mixed
     */
    public function getConfig($key = null) {
        if($key !== null) {
            if(isset($this->config[$key])) {
                return $this->config[$key];
            } else {
                return null;
            }
        }
        return $this->config;
    }

    /**
     * Return the remote wiki's version
     *
     * This is used for testing the connection
     *
     * @return string
     */
    public function getRemotVersion() {
        $this->client->query('dokuwiki.getVersion');
        return $this->client->getResponse();
    }

    /**
     * Get the current local and remote time
     *
     * @return int[] the local and remote timestamps
     */
    public function getTimes() {
        $this->client->query('dokuwiki.getTime');
        $rtime = $this->client->getResponse();
        $ltime = time();
        return array($ltime, $rtime);
    }

    /**
     * Get a list of changed files
     *
     * @return array
     */
    public function getSyncList() {
        if($this->config['type'] == self::TYPE_PAGES || $this->config['type'] == self::TYPE_BOTH) {
            $this->fetchRemoteFileList(self::TYPE_PAGES);
            $this->fetchLocalFileList(self::TYPE_PAGES);
        }
        if($this->config['type'] == self::TYPE_MEDIA || $this->config['type'] == self::TYPE_BOTH) {
            $this->fetchRemoteFileList(self::TYPE_MEDIA);
            $this->fetchLocalFileList(self::TYPE_MEDIA);
        }

        $this->consolidateSyncList();
        ksort($this->synclist); #FIXME implement our own sort with dir=0 at the top
        return $this->synclist;
    }

    /**
     * Synchronise the given file
     *
     * @param int $type pages|media
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
     * @param int $type pages|media
     * @param string $id the ID of the page or media
     * @param string $summary the editing summary
     * @throws SyncException
     */
    protected function syncPull($type, $id, $summary) {
        if($type === self::TYPE_PAGES) {
            if(checklock($id)) throw new SyncException('Local file is locked');
            $this->client->query('wiki.getPage', $id);
        } else {
            $this->client->query('wiki.getAttachment', $id);
        }

        $data = $this->client->getResponse();
        if($type === self::TYPE_PAGES) {
            saveWikiText($id, $data, $summary, false);
            idx_addPage($id);
        } else {
            io_saveFile(mediaFN($id), $data); #FIXME what about summary etc.
        }
    }

    /**
     * Delete a local file
     *
     * @param int $type pages|media
     * @param string $id the ID of the page or media
     * @param string $summary the editing summary
     * @throws SyncException
     */
    protected function syncPullDelete($type, $id, $summary) {
        if($type === self::TYPE_PAGES) {
            if(checklock($id)) throw new SyncException('Local file is locked');
            saveWikiText($id, '', $summary, false);
        } else {
            if(!unlink(mediaFN($id))) throw new SyncException('File deletion failed');
        }
    }

    /**
     * Sync from local to remote
     *
     * @param int $type pages|media
     * @param string $id the ID of the page or media
     * @param string $summary the editing summary
     * @throws SyncException
     */
    protected function syncPush($type, $id, $summary) {
        if($type === self::TYPE_PAGES) {
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
     * @param int $type pages|media
     * @param string $id the ID of the page or media
     * @param string $summary the editing summary
     * @throws SyncException
     */
    protected function syncPushDelete($type, $id, $summary) {
        if($type === self::TYPE_PAGES) {
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
     * @throws SyncException
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
            throw new SyncException('Locking at the remote wiki failed');
        }
    }

    /**
     * put all remote files for this profile into the sync list
     *
     * @param int $type pages|media
     */
    protected function fetchRemoteFileList($type) {
        if($type === self::TYPE_PAGES) {
            $cmd = 'dokuwiki.getPagelist';
        } else {
            $cmd = 'wiki.getAttachments';
        }

        $this->client->query($cmd, $this->config['ns'], $this->syncoptions);
        $remote = $this->client->getResponse();

        // put into synclist
        foreach($remote as $item) {
            $this->synclist[$type][$item['id']]['remote'] = $this->itemEnhance($item);
        }
    }

    /**
     * put all local files for this profile into the sync list
     *
     * @param int $type pages|media
     */
    protected function fetchLocalFileList($type) {
        global $conf;

        if($type === self::TYPE_PAGES) {
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
            $this->synclist[$type][$item['id']]['local'] = $this->itemEnhance($item);
        }
    }

    /**
     * Enance item with formatted info
     *
     * @param array $item
     * @return mixed
     */
    protected function itemEnhance($item) {
        $item['info'] = dformat($item['mtime']) . '<br />' . filesize_h($item['size']);
        return $item;
    }

    /**
     * removes all files that do not need syncing and calulates direction for the rest
     */
    protected function consolidateSyncList() {
        // synctimes
        $ltime = (int) $this->config['ltime'];
        $rtime = (int) $this->config['rtime'];
        $letime = (int) $this->config['letime'];
        $retime = (int) $this->config['retime'];

        foreach([self::TYPE_PAGES, self::TYPE_MEDIA] as $type) {
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

}
