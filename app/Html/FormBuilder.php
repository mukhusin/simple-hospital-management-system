<?php

namespace App\Html;

class FormBuilder
{
    /**
     * Build an HTML attribute string from an array.
     */
    protected static function attributes(array $attrs): string
    {
        $html = [];
        foreach ($attrs as $key => $value) {
            if (is_numeric($key)) {
                // bare attribute e.g. 'required', 'multiple'
                $html[] = e($value);
            } elseif ($value !== null && $value !== false) {
                $html[] = e($key) . '="' . e($value) . '"';
            }
        }
        return $html ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Open a form tag.
     * Usage: Form::open(['method' => 'POST', 'action' => '...', 'files' => true])
     */
    public static function open(array $attrs = []): string
    {
        $method = strtoupper($attrs['method'] ?? 'POST');
        $action = $attrs['action'] ?? request()->url();

        $formMethod = in_array($method, ['GET', 'POST']) ? $method : 'POST';

        $extra = [];
        if (!empty($attrs['files'])) {
            $extra['enctype'] = 'multipart/form-data';
        }
        if (!empty($attrs['class'])) {
            $extra['class'] = $attrs['class'];
        }
        if (!empty($attrs['id'])) {
            $extra['id'] = $attrs['id'];
        }

        $attrStr = static::attributes(array_merge(['method' => $formMethod, 'action' => $action], $extra));
        $csrf    = ($formMethod !== 'GET') ? csrf_field() : '';
        $spoofed = !in_array($method, ['GET', 'POST'])
            ? '<input type="hidden" name="_method" value="' . e($method) . '">'
            : '';

        return "<form{$attrStr}>{$csrf}{$spoofed}";
    }

    /**
     * Close a form tag.
     */
    public static function close(): string
    {
        return '</form>';
    }

    /**
     * Generate a <select> element.
     * Usage: Form::select($name, $options, $selected, $attrs)
     */
    public static function select(string $name, array $options, $selected = null, array $attrs = []): string
    {
        $attrs['name'] = $name;
        $attrStr = static::attributes($attrs);

        $optionsHtml = '';
        foreach ($options as $value => $label) {
            $isSelected = ((string) $value === (string) $selected) ? ' selected' : '';
            $optionsHtml .= '<option value="' . e($value) . '"' . $isSelected . '>' . e($label) . '</option>';
        }

        return "<select{$attrStr}>{$optionsHtml}</select>";
    }

    /**
     * Generate an <input> element.
     * Usage: Form::input($type, $name, $value, $attrs)
     */
    public static function input(string $type, string $name, $value = null, array $attrs = []): string
    {
        $attrs = array_merge(['type' => $type, 'name' => $name, 'value' => $value], $attrs);
        return '<input' . static::attributes($attrs) . '>';
    }

    /**
     * Generate a text input.
     */
    public static function text(string $name, $value = null, array $attrs = []): string
    {
        return static::input('text', $name, $value, $attrs);
    }

    /**
     * Generate a textarea.
     */
    public static function textarea(string $name, $value = null, array $attrs = []): string
    {
        $attrs['name'] = $name;
        $attrStr = static::attributes($attrs);
        return '<textarea' . $attrStr . '>' . e($value) . '</textarea>';
    }

    /**
     * Generate a submit button.
     */
    public static function submit(string $value = 'Submit', array $attrs = []): string
    {
        return static::input('submit', '', $value, $attrs);
    }
}
