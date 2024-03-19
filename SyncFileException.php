<?php

namespace dokuwiki\plugin\sync;

/**
 * Class SyncFileException
 *
 * This exception is thrown in the context of syncing one particular file
 *
 * @package dokuwiki\plugin\sync
 */
class SyncFileException extends SyncException {

    /**
     * SyncFileException constructor.
     *
     * @param string $message
     * @param int $id
     * @param int $code
     */
    public function __construct($message, $id, $code = 0) {
        // translate error messages
        $plugin = plugin_load('admin', 'sync');
        $msg = $plugin->getLang($message);
        if($msg) $message = $msg;

        if(substr($message, -1) != ':') $message .= ':';

        // append file name
        $message = $message . ' ' . $id;
        parent::__construct(html_entity_decode($message), $code);
    }
}
