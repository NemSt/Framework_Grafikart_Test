<?= $renderer->render('header') ?>

<h1>Bienvenue sur le blog</h1>

<ul>
    <li><a href="<?= $router->generateUri('blog.show', ['slug' => 'asdfasdf0-7asdf']); ?>">Article 1</a></li>
    <li>Article 2</li>
</ul>
<?= $renderer->render('footer') ?>
