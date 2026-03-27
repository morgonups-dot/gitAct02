<?php

class App
{
    private $request;
    private $renderer;

    public function __construct(Request $req, Renderer $renderer)
    {
        $this->request = $req;
        $this->renderer = $renderer;
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($method === 'POST') {
            $this->handlePost();
        } else {
            $this->handleGet();
        }
    }

    private function handlePost(): void
    {
        $validation = $this->request->validate();
        $errors = $validation['errors'];
        $data = $validation['data'];

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['previous_input'] = $this->request->all();
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        $generator = new RandomGenerator($data['n'], $data['min'], $data['max']);
        $numbers = $generator->generate();

        $_SESSION['results'] = [
            'numbers' => $numbers,
            'stats' => [
                'sum' => $generator->getSum(),
                'average' => $generator->getAverage(),
                'min' => $generator->getMin(),
                'max' => $generator->getMax()
            ],
            'input' => $data
        ];

        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    private function handleGet(): void
    {
        $errors = $_SESSION['errors'] ?? [];
        $previousInput = $_SESSION['previous_input'] ?? [];
        $results = $_SESSION['results'] ?? null;

        unset($_SESSION['errors'], $_SESSION['previous_input']);

        if ($results !== null) {
            unset($_SESSION['results']);
        }

        $numbers = $results['numbers'] ?? [];
        $stats = $results['stats'] ?? [
            'sum' => 0,
            'average' => 0.0,
            'min' => 0,
            'max' => 0
        ];
        $input = $results['input'] ?? $previousInput;

        $this->render($errors, $numbers, $stats, $input);
    }

    private function render(array $errors, array $numbers, array $stats, array $input): void
    {
        ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Números Aleatorios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Generador de Números Aleatorios</h1>
        <?php echo $this->renderer->renderForm($input); ?>
        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
            }
        }
        if (!empty($numbers)) {
            echo $this->renderer->renderResults($numbers, $stats, $input);
        }
        ?>
    </div>
</body>
</html>
        <?php
    }
}
