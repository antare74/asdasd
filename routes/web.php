<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebNotificationController;
use Workerman\Worker;
use PHPSocketIO\SocketIO;
use PhpMqtt\Client\Facades\MQTT;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Auth::routes();

// Route::get('/', [WebNotificationController::class, 'index'])->name('push-notificaiton');
Route::get('/', [WebNotificationController::class, 'index'])->name('push-notificaiton');
Route::post('/store-token', [WebNotificationController::class, 'storeToken'])->name('store.token');
Route::post('/send-web-notification', [WebNotificationController::class, 'sendWebNotification'])->name('send.web-notification');

Route::get('/index','WelcomeController@index');

Route::get('form','WelcomeController@form');

Route::post('hit','WelcomeController@hit');

Route::get('/sock', function () {
    return view('home-socket');
});
Route::get('/sock2', function () {
    return view('home2');
});

Route::get('/publish', function () {
    $server = 'localhost';     // change if necessary
    $port = 1883;                     // change if necessary
    $username = 'guest';                   // set your username
    $password = 'guest';                   // set your password
    $client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()
    $mqtt = new \Bluerhinos\phpMQTT($server, $port, $client_id);

    if ($mqtt->connect(true, NULL, $username, $password)) {
        $mqtt->publish('onegaisho', json_encode([
            'title' => 'Hello',
            'body' => 'World',
        ]) , 0, false);
        $mqtt->close();
    } else {
        echo "Time out!\n";
    }
    // MQTT::publish('onegaisho', 'Hello World!');
});
