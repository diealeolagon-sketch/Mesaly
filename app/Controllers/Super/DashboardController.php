<?php

declare(strict_types=1);

namespace App\Controllers\Super;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->render('super/dashboard', [
            'title' => 'Super Admin — Vencimientos',
            'user' => Auth::user(),
        ], 'super');
    }
}
