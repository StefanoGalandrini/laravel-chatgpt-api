<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $nameCharacter = $request->input('nameCharacter');
        $action = $request->input('action');
        $temperature = 0.7;

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Sei ${nameCharacter}, uno dei protagonisti del libro 'Il Signore degli Anelli', e ti chiedo di ${action} con un massimo di 200 caratteri e un minimo di 100 caratteri senza mai uscire dal tuo personaggio"
                ]
            ],
            'temperature' => $temperature
        ]);

        $data = $response->json();

        $message = $data['choices'][0]['message']['content'];

        return response()->json([
            'response' => $message,
        ]);
    }




    public function image(Request $request)
    {
        $prompt = $request->input('prompt', 'Una stanza con mobili vecchi e ragnatele, abbandonata da anni');
        $n = $request->input('n', 1); // numero di immagini da generare
        $size = $request->input('size', '512x512'); // dimensione delle immagini
        $response_format = $request->input('response_format', 'url'); // formato di risposta

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/images/generations', [
            'prompt' => $prompt,
            'n' => $n,
            'size' => $size,
            'response_format' => $response_format
        ]);

        $data = $response->json();
        // dd($data);
        $image_data = $response_format === 'url' ? $data['data'][0]['url'] : $data['data'][0]['b64_json'];

        return response()->json([
            'response' => $image_data,
        ]);
    }
}
