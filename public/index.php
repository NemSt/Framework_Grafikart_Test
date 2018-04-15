<?php
require '../vendor/autoload.php';
//Initialisation du renderer pour que les views puissent être adaptées
$renderer = new \Framework\Renderer();
$renderer->addPath(dirname(__DIR__) . '/views');
$app = new \Framework\App([
    \App\Blog\BlogModule::class
], [
    'renderer' => $renderer
]);
//guzzle est un ensemble de méthodes conçues pour répondre au PSR7
$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
//par contre, je dois convertir en output http l'objet response en psr7, et pour ça, j'utilise le package interop
\Http\Response\send($response);
