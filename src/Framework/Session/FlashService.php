<?php

namespace Framework\Session;

class FlashService
{
//pour que ce soit un message flash qui soit affiché, et non un message persistant, qui reste affiché
    /**
     * @var SessionInterface
     */
    private $session;

    private $sessionKey = 'flash';

    private $messages;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function success(string $message)
    {
        //plus portable que si on décide d'y aller avec $_SESSION['flash']['success'] et tout le système session
        //et ça permet de garder un seul niveau de profondeur avec le système de clés, donc c'est plus simple aussi
        $flash = $this->session->get($this->sessionKey, []);
        $flash['success'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    public function error(string $message)
    {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['error'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    public function get(string $type): ?string //pour récupérer la clé associée à la fonction utilisée
    {
        if (is_null($this->messages)) {
            $this->messages = $this->session->get($this->sessionKey, []);
            $this->session->delete($this->sessionKey); //pour que le message ne reste pas affiché
        }
        if (array_key_exists($type, $this->messages)) {
            return $this->messages[$type];
        }
        return null;
    }
}
