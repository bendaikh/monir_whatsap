<?php

namespace App\Services;

use App\Models\GoogleSheetConnection;
use App\Models\ProductLead;
use App\Models\UpsellOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    protected const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    protected const SHEETS_SCOPE = 'https://www.googleapis.com/auth/spreadsheets';
    protected const SHEETS_API = 'https://sheets.googleapis.com/v4/spreadsheets';

    public function isConfigured(): bool
    {
        $credentials = $this->tryLoadCredentials();

        return $credentials !== null
            && ! empty($credentials['client_email'])
            && ! empty($credentials['private_key']);
    }

    public function getServiceAccountEmail(): ?string
    {
        $credentials = $this->tryLoadCredentials();

        return $credentials['client_email'] ?? null;
    }

    public function appendLead(ProductLead $lead, GoogleSheetConnection $connection): array
    {
        $row = app(LeadsExportService::class)->rowForLead(
            $lead->loadMissing(['product', 'selectedPromotion'])
        );

        return $this->appendRows([$row], $connection, [
            'lead_id' => $lead->id,
            'type' => 'lead',
        ]);
    }

    public function appendUpsellOrder(UpsellOrder $order, GoogleSheetConnection $connection): array
    {
        $row = app(LeadsExportService::class)->rowForUpsellOrder(
            $order->loadMissing(['lead', 'upsellProduct'])
        );

        return $this->appendRows([$row], $connection, [
            'lead_id' => $order->lead_id,
            'upsell_order_id' => $order->id,
            'type' => 'upsell',
        ]);
    }

    protected function appendRows(array $rows, GoogleSheetConnection $connection, array $logContext = []): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'message' => 'Google Sheets credentials are not configured.'];
        }

        if (! $connection->is_active) {
            return ['success' => false, 'message' => 'Google Sheet connection is disabled.'];
        }

        try {
            $token = $this->getAccessToken();
            $range = $this->escapeSheetName($connection->sheet_name) . '!A:J';

            $url = self::SHEETS_API . '/' . $connection->spreadsheet_id . '/values/' . rawurlencode($range) . ':append';
            $url .= '?valueInputOption=USER_ENTERED&insertDataOption=INSERT_ROWS';

            $response = Http::withToken($token)
                ->post($url, [
                    'values' => $rows,
                ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Exported to Google Sheet.'];
            }

            $error = $response->json('error.message') ?? $response->body();

            if ($response->status() === 403) {
                $email = $this->getServiceAccountEmail();
                $error = 'The caller does not have permission. Share the spreadsheet with '
                    . ($email ?? 'your service account email')
                    . ' as Editor (not Viewer), then try again.';
            }

            Log::error('Google Sheets append failed', array_merge($logContext, [
                'connection_id' => $connection->id,
                'status' => $response->status(),
                'error' => $error,
            ]));

            return ['success' => false, 'message' => 'Google Sheets API error: ' . $error];
        } catch (\Throwable $e) {
            Log::error('Google Sheets exception', array_merge($logContext, [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]));

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function testConnection(GoogleSheetConnection $connection): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'message' => 'Google Sheets credentials are not configured in .env'];
        }

        try {
            $token = $this->getAccessToken();
            $range = $this->escapeSheetName($connection->sheet_name) . '!A1:J1';

            $url = self::SHEETS_API . '/' . $connection->spreadsheet_id . '/values/' . rawurlencode($range);

            $response = Http::withToken($token)->get($url);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Connection successful. Sheet is accessible.'];
            }

            $error = $response->json('error.message') ?? $response->body();

            return ['success' => false, 'message' => 'Cannot access sheet: ' . $error];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function getAccessToken(): string
    {
        $credentials = $this->loadCredentials();
        $now = time();

        $payload = [
            'iss' => $credentials['client_email'],
            'scope' => self::SHEETS_SCOPE,
            'aud' => self::TOKEN_URL,
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $jwt = $this->encodeJwt($payload, $credentials['private_key']);

        $response = Http::asForm()->post(self::TOKEN_URL, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Failed to obtain Google access token: ' . $response->body());
        }

        return $response->json('access_token');
    }

    protected function tryLoadCredentials(): ?array
    {
        $json = config('services.google_sheets.credentials_json');

        if ($json) {
            $decoded = json_decode($json, true);
            if (is_array($decoded) && ! empty($decoded['client_email'])) {
                return $decoded;
            }
        }

        $path = config('services.google_sheets.credentials_path');

        if ($path) {
            $resolved = $this->resolveCredentialsPath($path);

            if ($resolved && is_readable($resolved)) {
                $decoded = json_decode(file_get_contents($resolved), true);
                if (is_array($decoded) && ! empty($decoded['client_email'])) {
                    return $decoded;
                }
            }
        }

        return null;
    }

    protected function resolveCredentialsPath(string $path): ?string
    {
        if (file_exists($path)) {
            return $path;
        }

        $fromBase = base_path($path);
        if (file_exists($fromBase)) {
            return $fromBase;
        }

        $fromStorage = storage_path($path);
        if (file_exists($fromStorage)) {
            return $fromStorage;
        }

        return null;
    }

    protected function loadCredentials(): array
    {
        $credentials = $this->tryLoadCredentials();

        if ($credentials === null) {
            throw new \RuntimeException('Google service account credentials not found.');
        }

        return $credentials;
    }

    protected function escapeSheetName(string $name): string
    {
        if (preg_match('/[\s\'!]/', $name)) {
            return "'" . str_replace("'", "''", $name) . "'";
        }

        return $name;
    }

    protected function encodeJwt(array $payload, string $privateKey): string
    {
        $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $body = $this->base64UrlEncode(json_encode($payload));
        $input = $header . '.' . $body;

        $key = openssl_pkey_get_private($privateKey);
        if ($key === false) {
            throw new \RuntimeException('Invalid Google service account private key.');
        }

        if (! openssl_sign($input, $signature, $key, OPENSSL_ALGO_SHA256)) {
            throw new \RuntimeException('Failed to sign JWT for Google API.');
        }

        return $input . '.' . $this->base64UrlEncode($signature);
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
