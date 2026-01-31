<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI;
use GuzzleHttp\Client;

class TranslatorController extends Controller
{
    protected $hizkuntzak = [
        'euskera'   => 'Euskera',
        'gaztelera' => 'Gaztelera',
        'ingelesa'  => 'Ingelesa',
    ];

    public function index()
    {
        return view('translator', ['hizkuntzak' => $this->hizkuntzak]);
    }

    public function translate(Request $request)
    {
        $validated = $request->validate([
            'testua' => 'required|string|max:500',
            'hizkuntza' => 'required|string|in:' . implode(',', array_keys($this->hizkuntzak)),
        ], [
            'testua.required' => 'Itzuli beharreko testua falta da.',
            'testua.max' => 'Testuak gehienez 500 karaktere izan ditzake.',
            'hizkuntza.required' => 'Aukeratu hizkuntza bat.',
            'hizkuntza.in' => 'Aukeratutako hizkuntza ez da baliozkoa.',
        ]);

        $text = $request->input('testua');
        $targetLanguageKey = $request->input('hizkuntza');

        $target_lang = match ($targetLanguageKey) {
            'gaztelera' => 'Spanish',
            'euskera' => 'Basque',
            'ingelesa' => 'English',
            default => 'English',
        };

        try {
            $apiKey = config('services.openai.key');
            if (empty($apiKey)) {
                throw new \Exception('OPENAI_API_KEY falta da ingurune aldagaian.');
            }
            
            $client = OpenAI::factory()
                ->withApiKey($apiKey)
                ->withHttpClient(new Client(['verify' => false])) 
                ->make();

            //$client = OpenAI::client($apiKey);

            $result = $client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => 'Translate the following text into ' . $target_lang . ' only. Respond with just the translated text.'],
                    ['role' => 'user', 'content' => $text],
                ],
            ]);

            if (!isset($result->choices) || empty($result->choices)) {
                
                $errorDetails = json_encode($result);
                if (isset($result['error']['message'])) {
                    $errorDetails = $result['error']['message'];
                }
                throw new \Exception('La API no devolvió una traducción válida. Detalles: ' . $errorDetails);
            }

            $translationContent = $result->choices[0]->message->content;
            $translation = nl2br(htmlspecialchars($translationContent));

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['api_error' => 'Errorea: ' . $e->getMessage()]);
        }

        return view('translator', [
            'hizkuntzak' => $this->hizkuntzak,
            'testua' => $text,
            'hizkuntza' => $targetLanguageKey,
            'translation' => $translation,
        ]);
    }
}
