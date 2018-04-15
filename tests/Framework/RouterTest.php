<?php
namespace Tests\Framework;
//use Framework\App;
use Framework\Router;
//use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\ServerRequest as Request;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
//use Aura\Router\RouterContainer;

//classe qui permet de regrouper les tests pour vérifier différentes parties de l'application
//grâce à ces objets, il est vraiment beaucoup plus facile de créer les tests nécessaires
class RouterTest extends TestCase {
    /**
     * @var Router
     */
    private $router;
    public function setUp()
    {
        $this->router = new Router();
    }

    public function testGetMethod(){
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blog', function (){ return 'hello'; }, 'blog');
        //est-ce que ma fonction correspond à une des routes établies
        $route = $this->router->match($request);
        $this->assertEquals('blog', $route->getName());
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
    }
    public function testGetMethodIfURLDoesNotExists(){
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blogtd', function (){ return 'hello'; }, 'blog');
        //est-ce que ma fonction correspond à une des routes établies
        $route = $this->router->match($request);
        $this->assertEquals(null, $route);
    }

    /**
     *
     */
    public function testGetMethodWithParameters(){
        $request = new ServerRequest('GET', '/blog/mon-slug-8');
        $this->router->get('/blog', function (){ return 'asdfad'; }, 'posts');
        $this->router->get(
            "/blog/{slug}-{id}",
            function() { return 'hello'; },
            'post.show',
            ['slug' => '[a-z\-0-9]+', 'id' => '[0-9]+']);
        //$this->router->get("/blog/{slug:[a-z0-9\-]+}-{id:\d+}", function (){ return 'hello'; }, 'post.show');
        //est-ce que ma fonction correspond à une des routes établies
        $route = $this->router->match($request);
        $this->assertEquals('post.show', $route->getName());
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
        $this->assertEquals(['slug' => 'mon-slug', 'id' => '8'], $route->getParams());
        // Test invalid url
        $route = $this->router->match(new ServerRequest('GET', '/blog/mon_slug-18'));
        $this->assertEquals(null, $route);
    }

    /**
     * @throws \Aura\Router\Exception\RouteNotFound
     */
    public function testGenerateUri(){
        $this->router->get('/blog', function (){ return 'asdfad'; }, 'posts');
        $this->router->get(
            "/blog/{slug}-{id}",
            function() { return 'hello'; },
            'post.show',
            ['slug' => '[a-z\-0-9]+', 'id' => '[0-9]+']);
        $uri = $this->router->generateUri('post.show', ['slug' => 'mon-article', 'id' => '18']);
        $this->assertEquals('/blog/mon-article-18', $uri);
    }
}