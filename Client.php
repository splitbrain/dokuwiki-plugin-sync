<?php

namespace dokuwiki\plugin\sync;

class Client extends \IXR_Client {

    const MIN_API = 7;

    protected $filecontext = '';

    /**
     * Client constructor.
     *
     * @param string $server
     * @param string $user
     * @param int $pass
     * @param int $timeout
     */
    public function __construct($server, $user, $pass, $timeout = 15) {
        parent::__construct($server);
        $this->timeout = $timeout;
        $this->login($user, $pass);
    }

    /** @inheritdoc */
    public function query(...$args) {
        $ok = call_user_func_array('parent::query', func_get_args());
        $code = @$this->getErrorCode();
        $http = $this->getHTTPClient();
        if($code === -32300) $code = -1 * $http->status; // use http status on transport errors
        if(!$ok) {
            // when a file context is given include it in the exception
            if($this->filecontext) {
                throw new SyncFileException($this->getErrorMessage(), $this->filecontext, $code);
            } else {
                throw new SyncException($this->getErrorMessage(), $code);
            }
        }
        return $ok;
    }

    /**
     * Authenticate the client
     *
     * @param string $user
     * @param string $pass
     * @throws SyncException
     */
    protected function login($user, $pass) {
        $this->query('dokuwiki.login', $user, $pass);
        if(!$this->getResponse()) {
            throw new SyncException('loginerr', $this->getErrorCode());
        }
    }

    /**
     * Ensures the API version matches our expectations
     *
     * @throws SyncException
     */
    protected function ensureAPIversionOk() {
        $this->query('dokuwiki.getXMLRPCAPIVersion');

        $apiversion = (int) $this->getResponse();
        if($apiversion < self::MIN_API) {
            throw new SyncException('versionerr');
        }
    }

    /**
     * Set the file ID this query is running under
     *
     * @param string $file
     */
    public function setFileContext($file) {
        $this->filecontext = $file;
    }
}
