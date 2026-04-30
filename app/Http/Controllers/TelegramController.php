<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\TelegramService;

class TelegramController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function connect(TelegramService $telegram)
    {
        $user = Auth::user();

        if (!$user->telegram_link_token) {
            $user->telegram_link_token = Str::random(32);
            $user->save();
        }

        $startLink = $telegram->buildStartLink($user->telegram_link_token);

        return view('telegram.connect', compact('startLink'));
    }
}
