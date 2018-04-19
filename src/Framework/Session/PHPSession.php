<?php

namespace Framework\Session;

class PHPSession implements SessionInterface
{
    //Comme je ne veux pas toujours avoir une session de démarrée, je ne veux pas que ce soit dans l'index, donc
    //ici c'est l'endroit le plus logique puisque ce n'est que lors du Set, Get, Delete que ce sera nécessaire
    /**
     * Ensure session starts
     */
    private function ensureStarted() //je ne veux démarrer une session que si une session n'est pas déjà démarrée
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Get info in session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $this->ensureStarted();
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return $default;
    }

    /**
     * Add element to session
     *
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function set(string $key, $value): void
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }

    /**
     * Delete session key
     * @param string $key
     */
    public function delete(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }
}
