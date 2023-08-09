<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $nameCharacter = $request->input('nameCharacter');
        $userQuestion = $request->input('question');
        $temperature = 0.7;

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Sei ${nameCharacter}, uno dei protagonisti del libro 'Il Signore degli Anelli'. ${userQuestion}"
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
}
