<?php

declare(strict_types=1);

namespace App\Src;

use function header;
use function session_status;
use const PHP_SESSION_NONE;

class App
{
    private Request $request;
    private Renderer $renderer;

    public function __construct(Request $request, Renderer $renderer)
    {
        $this->request = $request;
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
        $n = $this->request->getInt('n');
        $min = $this->request->getInt('min', 1);
        $max = $this->request->getInt('max', 10000);

        if ($min === null) {
            $min = 1;
        }
        if ($max === null) {
            $max = 10000;
        }

        $errors = $this->request->validateN(1, 1000);

        if (empty($errors)) {
            if ($min === null || $max === null) {
                $min = 1;
                $max = 10000;
            }

            $generator = new RandomGenerator($n, $min, $max);
            $numbers = $generator->generate();

            $_SESSION['results'] = [
                'numbers' => $numbers,
                'stats' => [
                    'sum' => $generator->getSum(),
                    'average' => $generator->getAverage(),
                    'min' => $generator->getMin(),
                    'max' => $generator->getMax(),
                ],
                'previousInput' => [
                    'n' => $n,
                    'min' => $min,
                    'max' => $max,
                ],
            ];
        } else {
            $_SESSION['errors'] = $errors;
            $_SESSION['previousInput'] = [
                'n' => $n,
                'min' => $min,
                'max' => $max,
            ];
        }

        header('Location: ' . $_SERVER['REQUEST_URI'], true, 303);
        exit;
    }

    private function handleGet(): void
    {
        $results = $_SESSION['results'] ?? null;
        $errors = $_SESSION['errors'] ?? [];
        $previousInput = $_SESSION['previousInput'] ?? [];

        unset($_SESSION['results'], $_SESSION['errors'], $_SESSION['previousInput']);

        if ($results !== null) {
            echo $this->renderer->renderResults(
                $results['numbers'],
                $results['stats'],
                $previousInput
            );
        } else {
            echo $this->renderer->renderForm([
                'errors' => $errors,
                'n' => $previousInput['n'] ?? '',
                'min' => $previousInput['min'] ?? '',
                'max' => $previousInput['max'] ?? '',
            ]);
        }
    }
}
