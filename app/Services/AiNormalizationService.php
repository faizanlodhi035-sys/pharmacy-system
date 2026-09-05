<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AiNormalizationService
{
    /**
     * Normalizes a medicine search query using Gemini API.
     * Returns a structured array or null if failed.
     */
    public function normalizeMedicineSearch(string $query): ?array
    {
        $query = trim($query);
        if (strlen($query) < 2) {
            return null;
        }

        $cacheKey = 'ai_med_norm_' . md5($query);

        return Cache::remember($cacheKey, 86400, function () use ($query) {
            return $this->callGemini($query);
        });
    }

    private function callGemini(string $query): ?array
    {
        $apiKey = config('services.gemini.api_key');
        if (empty($apiKey)) {
            Log::warning('Gemini API key is not configured.');
            return null;
        }

        // We use gemini-3.6-flash as it is fast and suitable for this task
        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key=' . $apiKey;

        $prompt = "You are an expert pharmacy data normalization assistant.
The user is searching for a medicine: \"{$query}\"
Your job is to normalize this search into structured JSON format.
If you do not know a value, leave it as null or empty string. DO NOT invent facts.

Return strictly valid JSON with this exact schema:
{
  \"normalized_name\": \"string (the correct medical name without form/strength, e.g. Panadol)\",
  \"strength\": \"string (e.g. 500mg, 250 mg)\",
  \"dosage_form\": \"string (e.g. Tablet, Syrup, Injection)\",
  \"brand\": \"string\",
  \"manufacturer\": \"string\",
  \"barcode\": \"string (if present in the query)\",
  \"confidence\": integer (0 to 100 based on how sure you are about the medicine)
}";

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'response_mime_type' => 'application/json',
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $jsonText = $data['candidates'][0]['content']['parts'][0]['text'];
                    $decoded = json_decode($jsonText, true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $decoded;
                    } else {
                        Log::error('Gemini returned invalid JSON', ['response' => $jsonText]);
                    }
                }
            } else {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Gemini API connection error', ['error' => $e->getMessage()]);
        }

        return null;
    }
}
