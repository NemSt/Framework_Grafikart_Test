<?php
namespace Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;
//Middleware qui doit être placé en bout de ligne: va bloquer la requête si aucun middleware n'a intercepté
class NotFoundMiddleware {

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        var_dump($request); die();
        return new Response(404, [], 'Erreur 404');
    }

}