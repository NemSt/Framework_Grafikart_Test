<?php
namespace Framework;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
//un code sniffer est ajouté dans le but de détecter d'autres anomalies : "composer require squizlabs/php_codesniffer"
//aussi, après avoir nettoyé tout le code grâce au code sniffer, il est important de s'attarder aux version, donc git init
class App
{
    //la méthode implémente des interfaces de guzzle qui permettent d'utiliser les objets request et response
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        // Pour éliminer les / en fin d'url et rediriger; utilisation des variables globales du serveur
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && $uri[-1] === "/") {
            return (new Response())//on crée l'objet et on le retourne en appellant tout de suite certaines méthodes
            ->withStatus(301)//c'est la façon que guzzle a de faire une sorte de "set"
            ->withHeader('Location', substr($uri, 0, -1));
        }
        if ($uri === '/blog') {
            return new Response(200, [], '<h1>Bienvenue sur le blog</h1>');
        }
        return new Response(404, [], '<h1>Erreur 404</h1>');
    }
}