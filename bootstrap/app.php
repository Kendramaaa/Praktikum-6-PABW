<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Pengguna yang sudah login dan membuka /login atau /register dialihkan ke Daftar Laporan
        $middleware->redirectUsersTo(fn () => route('laporan.index'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
