<?php

use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Message;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('chat', function () {
    $messages = Message::all();
    return view('chat',compact('messages'));
});

Route::post('message', function (Request $request) {
    $messageContent = $request->input('message');

    // Tạo một bản ghi mới trong bảng tin nhắn
    $message = new Message;
    $message->user_id = auth()->user()->id;
    $message->content = $messageContent;
    $message->save();

    broadcast(new MessageSent(auth()->user(), $messageContent));

    return $messageContent;
});

Route::get('login/{id}', function ($id) {
    Auth::loginUsingId($id);

    return back();
});

Route::get('logout', function () {
    Auth::logout();

    return back();
});
