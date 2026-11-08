<?php

declare(strict_types=1);

namespace App\Controllers\Super;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Restaurante;

class DashboardController extends Controller
{
    private Restaurante $restauranteModel;

    public function __construct()
    {
        $this->restauranteModel = new Restaurante();
    }

    public function index(Request $request): Response
    {
        $negocios = $this->restauranteModel->allWithPlan();
        $totalNegocios = count($negocios);
        $enPrueba = count(array_filter($negocios, fn($n) => !empty($n['en_prueba'])));

        return $this->render('super/dashboard', [
            'title' => 'Super Admin — Vencimientos',
            'user' => Auth::user(),
            'negocios' => $negocios,
            'totalNegocios' => $totalNegocios,
            'enPrueba' => $enPrueba,
        ], 'super');
    }
}
