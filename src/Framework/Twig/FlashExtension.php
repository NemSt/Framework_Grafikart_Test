<?php
namespace Framework\Twig;

use Framework\Session\FlashService;

class FlashExtension extends \Twig_Extension //il ne faut pas oublier d'aller l'indiquer dans config/config
{

    /**
     * @var FlashService
     */
    private $flashService;

    public function __construct(FlashService $flashService)
    {
        $this->flashService = $flashService;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('flash', [$this, 'getFlash'])
        ];
    }

    public function getFlash($type): ?string
    {
        return $this->flashService->get($type); //on va donc aller chercher la clé dans FlashService, ce qui va
                                                //définir le type de message à afficher
    }
}
