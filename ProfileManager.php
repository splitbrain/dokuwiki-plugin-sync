<?php

namespace dokuwiki\plugin\sync;

class ProfileManager {

    protected $profiles = [];

    public function __construct() {
        $this->load();
    }

    /**
     * Return a list of all available profiles
     *
     * @return array
     */
    public function getAllProfiles() {
        return $this->profiles;
    }

    /**
     * Get a Profile instance by it's index
     *
     * @param int $num
     * @return Profile
     */
    public function getProfile($num) {
        $config = $this->getProfileConfig($num);
        return new Profile($config);
    }

    /**
     * Load a profile config by it's index
     *
     * @param int $num
     * @return array
     * @throws SyncException
     */
    public function getProfileConfig($num) {
        if(isset($this->profiles[$num])) return $this->profiles[$num];
        throw new SyncException('No such profile');
    }

    /**
     * Return an empty profile config
     *
     * @return array
     */
    public function getEmptyConfig() {
        return [
            'server' => '',
            'ns' => '',
            'depth' => 0,
            'user' => '',
            'pass' => '',
            'timeout' => 15,
            'type' => 0,
        ];
    }

    /**
     * Set the given config for the given profile
     *
     * When $num is false, the data is added to a new profile
     *
     * @param int|false $num
     * @param $data
     * @return int the index of the saved profile
     */
    public function setProfileConfig($num, $data) {
        if($num === false || !isset($this->profiles[$num])) {
            $num = count($this->profiles);
        }
        if(isset($this->profiles[$num])) {
            $this->profiles[$num] = array_merge($this->profiles[$num], $data);
        } else {
            $this->profiles[$num] = $data;
        }
        $this->save();
        return $num;
    }

    /**
     * List all profiles with a nice label
     *
     * @return array
     */
    public function getProfileLabels() {
        $labels = [];
        foreach($this->profiles as $idx => $profile) {
            $label = parse_url($profile['server'], PHP_URL_HOST);
            if($label === null) $label = $profile['server'];
            $label = ($idx + 1) . '. ' . $label;
            if($profile['user'] !== '') $label = $profile['user'] . '@' . $label;
            if($profile['ns'] !== '') $label .= ':' . $profile['ns'];
            $labels[$idx] = $label;
        }

        return $labels;
    }

    /**
     * Delete a profile
     *
     * @param int $num
     * @throws SyncException
     */
    public function deleteProfileConfig($num) {
        if(!isset($this->profiles[$num])) throw new SyncException('No such profile');
        unset($this->profiles[$num]);
        $this->profiles = array_values($this->profiles); //reindex
        $this->save();
    }

    /**
     * load profile configuration
     */
    protected function load() {
        global $conf;
        $profiles = $conf['metadir'] . '/sync.profiles';
        if(file_exists($profiles)) {
            $this->profiles = unserialize(io_readFile($profiles, false));
        }
    }

    /**
     * Save profiles to serialized storage
     */
    protected function save() {
        global $conf;
        $profiles = $conf['metadir'] . '/sync.profiles';
        io_saveFile($profiles, serialize($this->profiles));
    }
}
