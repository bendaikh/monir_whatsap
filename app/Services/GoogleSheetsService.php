<?php

namespace App\Services;

use App\Models\GoogleSheetConnection;
use App\Models\ProductLead;
use Firebase\JWT\JWT;
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
        if (! $this->isConfigured()) {
            return ['success' => false, 'message' => 'Google Sheets credentials are not configured.'];
        }

        if (! $connection->is_active) {
            return ['success' => false, 'message' => 'Google Sheet connection is disabled.'];
        }

        try {
            $token = $this->getAccessToken();
            $row = app(LeadsExportService::class)->rowForLead($lead->loadMissing(['product', 'selectedPromotion']));
            $range = $this->escapeSheetName($connection->sheet_name) . '!A:I';

            $url = self::SHEETS_API . '/' . $connection->spreadsheet_id . '/values/' . rawurlencode($range) . ':append';
            $url .= '?valueInputOption=USER_ENTERED&insertDataOption=INSERT_ROWS';

            $response = Http::withToken($token)
                ->post($url, [
                    'values' => [$row],
                ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Lead exported to Google Sheet.'];
            }

            $error = $response->json('error.message') ?? $response->body();

            Log::error('Google Sheets append failed', [
                'connection_id' => $connection->id,
                'lead_id' => $lead->id,
                'status' => $response->status(),
                'error' => $error,
            ]);

            return ['success' => false, 'message' => 'Google Sheets API error: ' . $error];
        } catch (\Throwable $e) {
            Log::error('Google Sheets exception', [
                'connection_id' => $connection->id,
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
            ]);

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
            $range = $this->escapeSheetName($connection->sheet_name) . '!A1:I1';

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

        $jwt = JWT::encode($payload, $credentials['private_key'], 'RS256');

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
}
