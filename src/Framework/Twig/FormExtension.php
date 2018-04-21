<?php
//extension qui sert à générer le code html des inputs du formulaire
namespace Framework\Twig;

class FormExtension extends \Twig_Extension
{

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('field', [$this, 'field'], [
                'is_safe'       => ['html'],
                'needs_context' => true
            ])
        ];
    }

    /**
     * Generate html for a field
     * @param array $context Context of the Twig view
     * @param string $key Field Key
     * @param mixed $value Field Value
     * @param string|null $label Label that will be used
     * @param array $options
     * @return string
     */
    public function field(array $context, string $key, $value, ?string $label = null, array $options = []): string
    {
        $type = $options['type'] ?? 'text';
        $error = $this->getErrorHtml($context, $key);
        $class = 'form-group';
        $value = $this->convertValue($value);
        $attributes = [
            'class' => trim('form-control ' . ($options['class'] ?? '')),
            'name' => $key,
            'id' => $key
        ];
        ($error) ? $attributes['class'] .=
            ' is-invalid' : $attributes['class'] .= ' is-valid';

        /*$value = $this->convertValue($value);
        $attributes = [
            'class' => trim('form-control ' . ($options['class'] ?? '')),
            'name'  => $key,
            'id'    => $key
        ];*/

        /*if ($error) {
            $class .= ' is-invalid';
            $attributes['class'] .= ' is-invalid';
        }*/
        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } elseif (array_key_exists('options', $options)) {
            $input = $this->select($value, $options['options'], $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }
        return "<div class=\"" . $class . "\">
              <label for=\"name\">{$label}</label>
              {$input}
              {$error}
            </div>";
    }

    private function convertValue($value): string
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d');
        }
        return (string)$value;
    }

    /**
     * Generate html from errors in context
     * @param $context
     * @param $key
     * @return string
     */
    private function getErrorHtml($context, $key)
    {
        $error = $context['errors'][$key] ?? false;
        /*if ($error) {
            return "<small class=\"form-text text-muted\">{$error}</small>";
        }*/
        /* /* */if ($error) {
            return "<div class=\"invalid-feedback\">{$error}</div>";
        }
        // */
        return "";
    }

    /**
     * Generate <input>
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function input(?string $value, array $attributes): string
    {
        return "<input type=\"text\" " . $this->getHtmlFromArray($attributes) . " value=\"{$value}\">";
    }

    /**
     * Generate <textarea>
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function textarea(?string $value, array $attributes): string
    {
        return "<textarea " . $this->getHtmlFromArray($attributes) . ">{$value}</textarea>";
    }

    /**
     * Génère un <select>
     * @param null|string $value
     * @param array $options
     * @param array $attributes
     * @return string
     */
    private function select(?string $value, array $options, array $attributes)
    {
        $htmlOptions = array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' => $key === $value];
            return $html . '<option value="' .
                substr($this->getHtmlFromArray($params), 7) . '>' . $options[$key] . '</option>';
        }, '');
        return "<select " . $this->getHtmlFromArray($attributes) . ">$htmlOptions</select>";

        /*<select class="form-control is-valid" name="name" id="name">
              <option value="1">Demo</option>
              <option value="2" selected>Demo2</option>
              </select>*/
    }

    /**
     * Change a $key => $value array in html attribute
     * @param array $attributes
     * @return string
     */
    private function getHtmlFromArray(array $attributes)
    {
        $htmlParts = [];
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $htmlParts[] = (string) $key;
            } elseif ($value !== false) {
                $htmlParts[] = "$key=\"$value\"";
            }
        }
        return implode(' ', $htmlParts);
        /*return implode(' ', array_map(function ($key, $value) {
            return "$key=\"$value\"";
        }, array_keys($attributes), $attributes));*/
    }
}
