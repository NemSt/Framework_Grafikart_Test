<?php
namespace tests\Framework;
use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

//classe qui permet de regrouper les tests pour vérifier différentes parties de l'application
//grâce à ces objets, il est vraiment beaucoup plus facile de créer les tests nécessaires
class AppTest extends TestCase {

    //Permet de faire des tests avec des objets plutôt qu'avec des echos qui renvoient le contenu; et on peut ainsi, grâce aux tests,
    //voir s'il y a un problème sans aller dans le browser.
    public function testRedirectTrailingSlash(){
        $app = new App(); //l'objet App représente l'application dans le contexte des tests
        $request = new ServerRequest('GET', '/demoslash/');
        //$_SERVER['REQUEST_URI'] = '/asdfasdf/';
        $response = $app->run($request);
        $this->assertContains('/demoslash', $response->getHeader('Location')); //qu'est-ce que devrait contenir le header
        $this->assertEquals(301, $response->getStatusCode()); //à quel statut la redirection doit correspondre
    }
    //pour tester que la bonne page est affichée (ici la page du blogue)
    public function testBlog() {
        $app = new App();
        $request = new ServerRequest('GET', '/blog');
        $response = $app->run($request);
        $this->assertContains('<h1>Bienvenue sur le blog</h1>', (string)$response->getBody()); //ce qui devrait être dans le body
        $this->assertEquals(200, $response->getStatusCode());
    }

    //pour le code de page introuvable
    public function testError404() {
        $app = new App();
        $request = new ServerRequest('GET', '/asdf');
        $response = $app->run($request);
        $this->assertContains('<h1>Erreur 404</h1>', (string)$response->getBody()); //ce qui devrait être dans le body
        $this->assertEquals(404, $response->getStatusCode());
    }
}