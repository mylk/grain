<?php

namespace Grain;

use Grain\Exception\TemplateNotFoundException;

class Template
{
    /**
     * Returns a rendered template
     *
     * In case the template is a php file, its code is evaluated.
     * If its of any other file type, its handled as plain text.
     *
     * @param string $filePath
     * @param array $data
     * @throws TemplateNotFoundException
     * @return string
     */
    public function render(string $filePath, array $data): string
    {
        $pathInfo = \pathinfo($filePath);

        if (!file_exists($filePath)) {
            throw new TemplateNotFoundException(sprintf("Template named \"%s\" was not found.", $pathInfo["basename"]));
        }

        // render php template
        if ("php" === $pathInfo["extension"]) {
            \ob_start();
            \extract($data);
            require $filePath;
            $content = \ob_get_contents();
            \ob_end_clean();

            return $content;
        }

        // prepend keys with the "%" sign that defines a template variable
        $keys = \array_keys($data);
        $keys = array_map(function ($key) {
            return "%$key";
        }, $keys);

        // render text file template
        $content = \file_get_contents($filePath);
        $output = \str_replace($keys, \array_values($data), $content);
        // remove placeholders that had no match with array keys of $data
        $output = \preg_replace("/%(\w+)/", "", $output);

        return $output;
    }
}
