<?php
namespace Framework\Session;

interface SessionInterface
{

    /**
     * Get info in session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Add a key to session
     *
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function set(string $key, $value): void;

    /**
     * Delete session key
     * @param string $key
     */
    public function delete(string $key): void;
}
