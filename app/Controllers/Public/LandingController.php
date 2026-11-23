<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Restaurante;

class LandingController extends Controller
{
    /**
     * Muestra la Landing Page oficial del SaaS Mesaly.
     */
    public function index(Request $request): Response
    {
        $restauranteModel = new Restaurante();
        $demoRestaurant = $restauranteModel->findBySlug('fuego-urbano');

        return $this->render('public/landing', [
            'title' => 'Mesaly — Software Gastronómico, Menú Digital y Pedidos en Mesa con QR',
            'demoSlug' => $demoRestaurant ? $demoRestaurant['slug'] : 'fuego-urbano',
            'demoNombre' => $demoRestaurant ? $demoRestaurant['nombre'] : 'Fuego Urbano — Burger & Grill',
            'isLoggedIn' => Auth::check(),
            'dashboardUrl' => Auth::check() ? (Auth::isSuperAdmin() ? app_url('/superadmin') : app_url('/admin')) : app_url('/login'),
        ], 'landing');
    }
}
