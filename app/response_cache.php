<?php
namespace App;

require_once 'app/cache.php';

class ResponseCache extends Cache
{

    /**
     * Returns a key for the error response
     *
     * @param $key - The key for the value
     * @return string
     */
    public function getErrorsKey(string $key)
    {
        return "errors.$key";
    }

    /**
     * Checks if there is an error response whith such a key
     *
     * @param string $key - The key for the value
     * @return bool
     */
    public function hasError(string $key)
    {
        $errorKey = $this->getErrorsKey($key);
        return $this->has($errorKey);
    }

    /**
     * Gets a stored error response by the key
     *
     * @param string $key - The key for the value
     * @return mixed
     */
    public function getError(string $key)
    {
        $errorKey = $this->getErrorsKey($key);
        return $this->get($errorKey);
    }

    /**
     * Stores an error response by the key
     *
     * @param string $key - The key for the value
     * @param $value - The value to be set by the key
     * @param int $time - How long the value will exist
     * @return mixed
     */
    public function setError(string $key, $value, int $time)
    {
        $errorKey = $this->getErrorsKey($key);
        return $this->set($errorKey, $value, $time);
    }
}
