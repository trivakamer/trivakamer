<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\ExampleController;
use App\Controllers\GeneralController;
use App\Controllers\UserController;
use App\Controllers\AuthWebhookController;
use App\Middleware\AdminMiddleware;
use CQ\Controllers\AuthCodeController;
use CQ\Controllers\AuthDeviceController;
use CQ\Middleware\AuthMiddleware;
use CQ\Middleware\FormMiddleware;
use CQ\Middleware\JsonMiddleware;
use CQ\Middleware\RatelimitMiddleware;

$route->get('/', [GeneralController::class, 'index']);

$middleware->create(['middleware' => [FormMiddleware::class]], function () use ($route, $middleware) {
    $route->post('/upload', [GeneralController::class, 'upload']);
});

$middleware->create(['prefix' => '/auth'], function () use ($route, $middleware) {
    $route->get('/request', [AuthCodeController::class, 'request']);
    $route->get('/callback', [AuthCodeController::class, 'callback']);
    $route->get('/logout', [AuthCodeController::class, 'logout']);

    $route->get('/request/device', [AuthDeviceController::class, 'request']);
    $route->post('/callback/device', [AuthDeviceController::class, 'callback']);

    $middleware->create(['middleware' => [JsonMiddleware::class]], function () use ($route) {
        $route->post('/delete', [AuthWebhookController::class, 'delete']);
    });
});

$middleware->create(['middleware' => [AuthMiddleware::class]], function () use ($route, $middleware) {
    $route->get('/dashboard', [UserController::class, 'dashboard']);

    $middleware->create(['prefix' => '/admin', 'middleware' => [AdminMiddleware::class]], function () use ($route) {
        $route->get('', [AdminController::class, 'index']);
    });
});
