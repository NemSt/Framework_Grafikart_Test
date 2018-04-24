<?php
namespace Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;

class TrailingSlashMiddleware {
// Pour éliminer les / en fin d'url et rediriger; utilisation des variables globales du serveur
    public function __invoke(ServerRequestInterface $request, callable $next) //$next = le prochain middleware appelé
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && $uri[-1] === "/") {
            return (new \GuzzleHttp\Psr7\Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }
        return $next($request);
    }

}