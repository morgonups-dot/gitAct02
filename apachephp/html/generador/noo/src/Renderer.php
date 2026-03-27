<?php

class Renderer
{
    private $viewsPath;

    public function __construct()
    {
        $this->viewsPath = __DIR__ . '/../views/';
    }

    public function renderForm(array $data = []): string
    {
        extract(['data' => $data]);
        ob_start();
        include $this->viewsPath . 'form.php';
        return ob_get_clean();
    }

    public function renderResults(array $numbers, array $stats, array $previousInput = []): string
    {
        extract([
            'numbers' => $numbers,
            'stats' => $stats,
            'previousInput' => $previousInput
        ]);
        ob_start();
        include $this->viewsPath . 'results.php';
        return ob_get_clean();
    }

    public function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
