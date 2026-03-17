<?php

namespace App\Controllers\Admin;

use App\Services\Admin\DashboardService;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:admin'])]
class DashboardController
{
    protected DashboardService $service;

    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    #[Get('/admin/dashboard', name: 'admin.dashboard')]
    public function index()
    {
        $stats = $this->service->getStats();

        return view('admin/dashboard', [
            'title' => 'Painel de Controle',
            'stats' => $stats
        ]);
    }
}
