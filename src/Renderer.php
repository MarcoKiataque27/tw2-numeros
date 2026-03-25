<?php

declare(strict_types=1);

namespace App\Src;

use function htmlspecialchars;
use function sprintf;

class Renderer
{
    private string $viewsPath;
    // URL del CDN de Bootstrap para no usar archivos locales
    private string $bootstrapCss = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css';

    public function __construct(string $viewsPath)
    {
        $this->viewsPath = $viewsPath;
    }

    private function getHeader(string $title): string
    {
        return sprintf(
            '<!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>%s</title>
                <link href="%s" rel="stylesheet">
                <style>
                    body { background-color: #f8f9fa; padding-top: 50px; }
                    .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                </style>
            </head>
            <body>
            <div class="container">',
            $title,
            $this->bootstrapCss
        );
    }

    public function renderForm(array $data = []): string
    {
        $n = htmlspecialchars((string)($data['n'] ?? ''), ENT_QUOTES, 'UTF-8');
        $min = htmlspecialchars((string)($data['min'] ?? ''), ENT_QUOTES, 'UTF-8');
        $max = htmlspecialchars((string)($data['max'] ?? ''), ENT_QUOTES, 'UTF-8');
        $errors = $data['errors'] ?? [];

        $errorHtml = '';
        foreach ($errors as $message) {
            $errorHtml .= sprintf('<div class="alert alert-danger">%s</div>', htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
        }

        return $this->getHeader('Generador de Números') . sprintf(
            '<div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card mt-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Configuración del Generador</h4>
                        </div>
                        <div class="card-body">
                            %s
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">Cantidad (n): *</label>
                                    <input type="number" name="n" class="form-control" value="%s" min="1" max="1000" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mínimo:</label>
                                        <input type="number" name="min" class="form-control" value="%s">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Máximo:</label>
                                        <input type="number" name="max" class="form-control" value="%s">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Generar Números</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            </div></body></html>',
            $errorHtml, $n, $min, $max
        );
    }

    public function renderResults(array $numbers, array $stats, array $previousInput = []): string
    {
        $tableRows = '';
        foreach ($numbers as $index => $number) {
            $tableRows .= sprintf('<tr><td>%d</td><td>%d</td></tr>', $index + 1, $number);
        }

        return $this->getHeader('Resultados') . sprintf(
            '<div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-success text-white"><h5>Números Generados</h5></div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark"><tr><th>#</th><th>Valor</th></tr></thead>
                                <tbody>%s</tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4 text-center">
                        <div class="card-header bg-info text-white">Estadísticas</div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Suma: <strong>%s</strong></li>
                            <li class="list-group-item">Promedio: <strong>%s</strong></li>
                            <li class="list-group-item">Mínimo: <strong>%s</strong></li>
                            <li class="list-group-item">Máximo: <strong>%s</strong></li>
                        </ul>
                    </div>
                    <a href="/" class="btn btn-secondary w-100">Volver al Formulario</a>
                </div>
            </div>
            </div></body></html>',
            $tableRows,
            number_format($stats['sum'], 0, ',', '.'),
            number_format($stats['average'], 2, ',', '.'),
            $stats['min'],
            $stats['max']
        );
    }
}