<?php
namespace App;

use Phpfastcache\Helper\Psr16Adapter;

class Cache
{
    public $files;

    public function __construct()
    {
        $this->files = new Psr16Adapter('Files');
    }

    /**
     * Checks if there is a value stored by this key.
     *
     * @param string $key - They key for the value
     * @return boolean - Indicates if the value exists
     */
    public function has(string $key)
    {
        return $this->files->has($key);
    }

    /**
     * Sets the value by the key
     *
     * @param string $key - They key for the value
     * @param $value - The value to be set by the key
     * @param int $time - How long the value will exist ( in seconds )
     * @return mixed
     */
    public function set(string $key, $value, int $time)
    {
        return $this->files->set($key, $value, $time);
    }

    /**
     * Gets the value by the key
     *
     * @param string $key - They key for the value
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->files->get($key);
    }
}
