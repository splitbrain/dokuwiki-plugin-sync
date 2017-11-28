<?php

namespace dokuwiki\plugin\sync;

/**
 * Class SyncException
 *
 * @package dokuwiki\plugin\sync
 */
class SyncException extends \Exception {

    /**
     * SyncException constructor.
     * @param string $message
     * @param int $code
     * @param object|null $previous
     */
    public function __construct($message = '', $code = 0, $previous = null) {
        // translate error messages
        $plugin = plugin_load('admin', 'sync');
        $msg = $plugin->getLang($message);
        if($msg) $message = $msg;

        parent::__construct($message, $code, $previous);
    }
}
