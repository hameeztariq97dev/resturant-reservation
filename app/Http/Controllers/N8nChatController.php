<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class N8nChatController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_message' => ['required', 'string', 'max:8000'],
        ]);

        $url = config('services.n8n.webhook_url');

        if (blank($url)) {
            return response()->json([
                'success' => false,
                'message' => 'N8N webhook URL is not configured. Set N8N_WEBHOOK_URL in your .env file.',
            ], 503);
        }

        $response = Http::timeout(120)
            ->acceptJson()
            ->asJson()
            ->post($url, [
                'user_message' => $validated['user_message'],
            ]);

        if (! $response->successful()) {
            return response()->json([
                'success' => false,
                'message' => $this->n8nErrorMessage($response),
                'detail' => $response->body(),
                'n8n_http_status' => $response->status(),
                'hint' => 'In n8n: Executions → open the failed run to see which node failed. Typical fixes: attach Google Gemini (PaLM) API credentials to the Gemini node; activate the workflow; confirm the Gemini model exists for your key; if n8n runs in Docker, use host.docker.internal instead of 127.0.0.1 for Laravel API URLs in HTTP tools.',
            ], 502);
        }

        $data = $response->json();

        return response()->json([
            'success' => true,
            'reply' => $this->extractReply($data),
        ]);
    }

    private function extractReply(mixed $data): string
    {
        if (is_string($data)) {
            return $data;
        }

        if (! is_array($data)) {
            return json_encode($data, JSON_UNESCAPED_UNICODE) ?: '';
        }

        if (isset($data['output']) && (is_string($data['output']) || is_numeric($data['output']))) {
            return (string) $data['output'];
        }

        if (isset($data['text'])) {
            return (string) $data['text'];
        }

        if (isset($data['response'])) {
            return is_string($data['response']) ? $data['response'] : json_encode($data['response'], JSON_UNESCAPED_UNICODE);
        }

        $first = $data[0] ?? null;
        if (is_array($first)) {
            if (isset($first['json']['output'])) {
                return (string) $first['json']['output'];
            }
            if (isset($first['output'])) {
                return is_string($first['output']) ? $first['output'] : json_encode($first['output'], JSON_UNESCAPED_UNICODE);
            }
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Surface n8n's JSON error message when the workflow fails (HTTP 5xx).
     */
    private function n8nErrorMessage(Response $response): string
    {
        $raw = $response->body();
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            if (! empty($decoded['message']) && is_string($decoded['message'])) {
                return 'n8n: '.$decoded['message'];
            }
            if (! empty($decoded['error']['message']) && is_string($decoded['error']['message'])) {
                return 'n8n: '.$decoded['error']['message'];
            }
        }

        return 'The n8n webhook returned an error (HTTP '.$response->status().').';
    }
}
