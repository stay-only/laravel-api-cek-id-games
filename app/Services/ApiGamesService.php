<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class ApiGamesService
{
    private $client;
    private $baseUrl;
    private $merchantId;
    private $signature;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = config('services.apigames.base_url', 'https://v1.apigames.id');
        $this->merchantId = config('services.apigames.merchant_id');
        $this->signature = config('services.apigames.signature');
    }

    /**
     * Cek username/ID game
     */
    public function checkGameId($gameCode, $userId, $zoneId = null)
    {
        try {
            $url = "{$this->baseUrl}/merchant/{$this->merchantId}/cek-username/{$gameCode}";
            
            $params = [
                'user_id' => $userId,
                'signature' => $this->signature
            ];

            // Tambahkan zone_id jika diperlukan (untuk game seperti Mobile Legends)
            if ($zoneId) {
                $params['zone_id'] = $zoneId;
            }

            $response = $this->client->get($url, [
                'query' => $params,
                'timeout' => 30
            ]);

            $data = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'data' => $data,
                'provider' => 'apigames'
            ];

        } catch (RequestException $e) {
            Log::error('ApiGames Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal mengecek ID game',
                'error' => $e->getMessage(),
                'provider' => 'apigames'
            ];
        }
    }

    /**
     * Get daftar game yang didukung
     */
    public function getSupportedGames()
    {
        return [
            'mobilelegend' => [
                'name' => 'Mobile Legends',
                'fields' => ['user_id', 'zone_id'],
                'icon' => 'https://cdn.apigames.id/games/mobilelegend.png'
            ],
            'freefire' => [
                'name' => 'Free Fire',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.apigames.id/games/freefire.png'
            ],
            'pubgmobile' => [
                'name' => 'PUBG Mobile',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.apigames.id/games/pubgmobile.png'
            ],
            'valorant' => [
                'name' => 'Valorant',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.apigames.id/games/valorant.png'
            ],
            'genshinimpact' => [
                'name' => 'Genshin Impact',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.apigames.id/games/genshinimpact.png'
            ]
        ];
    }

    /**
     * Topup game (struktur dasar)
     */
    public function topup($gameCode, $userId, $productCode, $amount, $zoneId = null)
    {
        try {
            $url = "{$this->baseUrl}/merchant/{$this->merchantId}/topup";
            
            $data = [
                'game_code' => $gameCode,
                'user_id' => $userId,
                'product_code' => $productCode,
                'amount' => $amount,
                'signature' => $this->signature
            ];

            if ($zoneId) {
                $data['zone_id'] = $zoneId;
            }

            $response = $this->client->post($url, [
                'json' => $data,
                'timeout' => 60
            ]);

            $result = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'data' => $result,
                'provider' => 'apigames'
            ];

        } catch (RequestException $e) {
            Log::error('ApiGames Topup Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal melakukan topup',
                'error' => $e->getMessage(),
                'provider' => 'apigames'
            ];
        }
    }
}
