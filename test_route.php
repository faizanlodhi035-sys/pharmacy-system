
Route::get('/test-gemini', function () {
    $q = request('q', 'panadol');
    $service = app(\App\Services\AiNormalizationService::class);
    $res = $service->normalizeMedicineSearch($q);
    return response()->json([
        'query' => $q,
        'api_key_configured' => !empty(config('services.gemini.api_key')),
        'api_key_prefix' => substr(config('services.gemini.api_key'), 0, 5),
        'result' => $res,
    ]);
});
