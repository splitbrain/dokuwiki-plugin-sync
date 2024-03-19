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

        if($code === -403) $message = $plugin->getLang('autherr');

        parent::__construct(html_entity_decode($message), $code, $previous);
    }
}
