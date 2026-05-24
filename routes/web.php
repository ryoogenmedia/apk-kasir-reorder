<?php

use Illuminate\Support\Facades\Route;
use App\Models\Order;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/admin/orders/receipt/{order}', function (Order $order) {
    return view('reports.receipt', compact('order'));
})->middleware(['auth']);
