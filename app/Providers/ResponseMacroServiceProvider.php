<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($data) {
            return Response::json([
                    'errors'  => false,
                    'data' => $data,
            ]);
        });
        Response::macro('error', function ($message, $status = 400) {
            $response = [
                'success' => false,
                'status_code' => $status
            ];

            if (is_array($message)) {
                return Response::json($response + ['errors' => $message], $status);
            }
            $response['errors'] = $message;
            return Response::json($response, $status);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
