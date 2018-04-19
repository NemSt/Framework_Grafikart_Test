<?php

namespace Tests\Framework\Twig;

use Framework\Twig\FormExtension;
use PHPUnit\Framework\TestCase;

class FormExtensionTest extends TestCase
{

    /**
     * @var FormExtension
     */
    private $formExtension;

    public function setUp()
    {
        $this->formExtension = new FormExtension();
    }

    private function trim(string $string)
    {
        //$lines = explode(PHP_EOL, $string);
        $lines = explode("\n", $string);
        $lines = array_map('trim', $lines);
        return implode('', $lines);
    }

    public function assertSimilar(string $expected, string $actual)
    {
        $this->assertEquals($this->trim($expected), $this->trim($actual));
    }

    public function testField()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'Titre');
        $this->assertSimilar("
            <div class=\"form-group\">
              <label for=\"name\">Titre</label>
              <input type=\"text\" class=\"form-control is-valid\" name=\"name\" id=\"name\" value=\"demo\">
            </div>
        ", $html);
    }

    public function testFieldWithClass()
    {
        $html = $this->formExtension->field(
            [],
            'name',
            'demo',
            'Titre',
            ['class' => 'demo']
        );
        $this->assertSimilar("
            <div class=\"form-group\">
              <label for=\"name\">Titre</label>
              <input type=\"text\" class=\"form-control demo is-valid\" name=\"name\" id=\"name\" value=\"demo\">
            </div>
        ", $html);
    }

    public function testTextarea()
    {
        $html = $this->formExtension->field(
            [],
            'name',
            'demo',
            'Titre',
            ['type' => 'textarea']
        );
        $this->assertSimilar("
            <div class=\"form-group\">
              <label for=\"name\">Titre</label>
              <textarea class=\"form-control is-valid\" name=\"name\" id=\"name\">demo</textarea>
            </div>
        ", $html);
    }

    public function testFieldWithErrors()
    {
        $context = ['errors' => ['name' => 'erreur']];
        $html = $this->formExtension->field($context, 'name', 'demo', 'Titre');
        $this->assertSimilar("
            <div class=\"form-group\">
                <label for=\"name\">Titre</label>
                <input type=\"text\" class=\"form-control is-invalid\" name=\"name\" id=\"name\" value=\"demo\">
                <div class=\"invalid-feedback\">
                   erreur
                </div>
            </div>
        ", $html);
    }
}