<?php

namespace dokuwiki\plugin\sync;

class Client extends \IXR_Client {

    const MIN_API = 7;

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
    function query() {
        $ok = call_user_func_array('parent::query', func_get_args());
        if(!$ok) throw new FatalException($this->getErrorMessage(), $this->getErrorCode());
        return $ok;
    }

    /**
     * Authenticate the client
     *
     * @param string $user
     * @param string $pass
     * @throws FatalException
     */
    protected function login($user, $pass) {
        $this->query('dokuwiki.login', $user, $pass);
        if(!$this->getResponse()) {
            throw new FatalException('loginerr', $this->getErrorCode());
        }
    }

    /**
     * Ensures the API version matches our expectations
     *
     * @throws FatalException
     */
    protected function ensureAPIversionOk() {
        $this->query('dokuwiki.getXMLRPCAPIVersion');

        $apiversion = (int) $this->getResponse();
        if($apiversion < self::MIN_API) {
            throw new FatalException('versionerr');
        }
    }
}
