<?php

namespace Mango;

/**
 * Class TemplateEngine
 * @package Mango
 */
class TemplateEngine
{
    /**
     * @var string
     */
    private $viewPath;

    /**
     * TemplateEngine constructor.
     * @param string $viewPath
     */
    public function __construct(string $viewPath) {
        $this->viewPath = $viewPath;
    }

    /**
     * @param string $view
     * @return string
     */
    public function getOutputForView(string $view) : string {
        ob_start();
        include $this->viewPath . '/' . $view . '.php';
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * @param string $variable
     * @return string
     */
    private function cleanVariableDeclaration(string $variable) : string {
        $cleanVariableName = str_replace('{{', '', $variable);
        $cleanVariableName = str_replace('}}', '', $cleanVariableName);
        $cleanVariableName = str_replace('$', '', $cleanVariableName);
        $cleanVariableName = str_replace(' ', '', $cleanVariableName);

        return $cleanVariableName;
    }

    /**
     * @param string $content
     * @param array $variables
     * @return string
     */
    private function replaceVariablesForOutput(string $content, array $variables) : string {
        $variablesWaitingForReplace = [];
        preg_match_all("~\{\{\s*(.*?)\s*\}\}~", $content, $variablesWaitingForReplace);

        foreach ($variablesWaitingForReplace[0] as $variable) {
            $cleanVariableName = $this->cleanVariableDeclaration($variable);

            if (array_key_exists($cleanVariableName, $variables)) {
                $content = str_replace($variable, $variables[$cleanVariableName], $content);
            }
        }

        return $content;
    }

    /**
     * @param string $view
     * @param array $variables
     */
    public function renderView(string $view, array $variables = []) {
        $output = $this->getOutputForView($view);

        if (count($variables) > 0) {
            $content = $this->replaceVariablesForOutput($output, $variables);
        }

        echo $content;
    }
}