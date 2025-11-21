<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    // Trait para manejar autorizaciones
    use AuthorizesRequests;
}
