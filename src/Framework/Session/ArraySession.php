<?php

namespace Framework\Session;

class ArraySession implements SessionInterface //semblable à PHPSession, mais servirait à faire les tests éventuellement
                                                //(difficile de tester PHPSession avec $_SESSION :
                                                //compliqué voire impossible de simuler avec une fausse valeur
{

    private $session = [];

    /**
     * Récupère une information en Session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->session)) {
            return $this->session[$key];
        }
        return $default;
    }

    /**
     * Ajoute une information en Session
     *
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function set(string $key, $value): void
    {
        $this->session[$key] = $value;
    }

    /**
     * Supprime une clef en session
     * @param string $key
     */
    public function delete(string $key): void
    {
        unset($this->session[$key]);
    }
}
