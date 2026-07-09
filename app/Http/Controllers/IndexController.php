<?php

namespace App\Http\Controllers;

use App\Services\GameProviderFactory;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function cek(Request $request) 
    {
        // Validasi input dasar
        $request->validate([
            'game_code' => 'required|string',
            'user_id' => 'required|string',
            'zone_id' => 'nullable|string',
            'provider' => 'nullable|string'
        ]);

        $gameCode = $request->input('game_code');
        $userId = $request->input('user_id');
        $zoneId = $request->input('zone_id');
        $preferredProvider = $request->input('provider', 'apigames');

        // Menggunakan GameProviderFactory untuk mengecek ID game
        // Ini akan memindahkan logika API ke service layer
        $apiData = GameProviderFactory::checkGameIdWithFallback(
            $gameCode, 
            $userId, 
            $zoneId, 
            $preferredProvider
        );

        return view('index', compact('apiData'));
    }
}
