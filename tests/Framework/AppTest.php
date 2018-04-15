<?php
namespace Tests\Framework;
use App\Blog\BlogModule;
use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Tests\Framework\Modules\ModuleWithErrors;
use Tests\Framework\Modules\StringModule;

//classe qui permet de regrouper les tests pour vérifier différentes parties de l'application
//grâce à ces objets, il est vraiment beaucoup plus facile de créer les tests nécessaires
class AppTest extends TestCase {

    //Permet de faire des tests avec des objets plutôt qu'avec des echos qui renvoient le contenu; et on peut ainsi, grâce aux tests,
    //voir s'il y a un problème sans aller dans le browser.
    /**
     * @throws \Exception
     */
    public function testRedirectTrailingSlash(){
        $app = new App(); //l'objet App représente l'application dans le contexte des tests
        $request = new ServerRequest('GET', '/demoslash/');
        //$_SERVER['REQUEST_URI'] = '/asdfasdf/';
        $response = $app->run($request);
        $this->assertContains('/demoslash', $response->getHeader('Location')); //qu'est-ce que devrait contenir le header
        $this->assertEquals(301, $response->getStatusCode()); //à quel statut la redirection doit correspondre
    }
    //pour tester que la bonne page est affichée (ici la page du blogue)

    /**
     * @throws \Exception
     */
    public function testBlog() {
        $app = new App([
            BlogModule::class
        ]);
        $request = new ServerRequest('GET', '/blog');
        $response = $app->run($request);
        $this->assertContains('<h1>Bienvenue sur le blog</h1>', (string)$response->getBody()); //ce qui devrait être dans le body
        $this->assertEquals(200, $response->getStatusCode());

        $requestSingle = new ServerRequest('GET', "/blog/article-de-test");
        $responseSingle = $app->run($requestSingle);
        $this->assertContains("<h1>Bienvenue sur l'article article-de-test</h1>", (string)$responseSingle->getBody());
    }

    /**
     * @throws \Exception
     */
    public function testThrowExceptionIfNoResponseSent(){
        $app = new App([
            ModuleWithErrors::class
        ]);
        $request = new ServerRequest('GET', '/demo');
        $this->expectException(\Exception::class);
        $app->run($request);
    }

    /**
     * @throws \Exception
     */
    public function testConvertStringToResponse(){
        $app = new App([
            StringModule::class
        ]);
        $request = new ServerRequest('GET', '/demo');
        $response = $app->run($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('DEMO', (string)$response->getBody());
    }

    //pour le code de page introuvable

    /**
     * @throws \Exception
     */
    public function testError404() {
        $app = new App([]);
        $request = new ServerRequest('GET', '/asdf');
        $response = $app->run($request);
        $this->assertContains('<h1>Erreur 404</h1>', (string)$response->getBody()); //ce qui devrait être dans le body
        $this->assertEquals(404, $response->getStatusCode());
    }
}