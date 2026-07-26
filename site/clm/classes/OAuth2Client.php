<?php
/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
/**
 * OAuth2 Client Credentials Flow mit Refresh-Token-Unterstützung
 * Spec: https://www.oauth.com/oauth2-servers/access-tokens/client-credentials/
 */

class clm_class_OAuth2Client
{
    // ─────────────────────────────────────────────
    //  Konfiguration (öffentliche Eigenschaften)
    // ─────────────────────────────────────────────
    public string $apiBaseUrl;
    public string $clientId;
    public string $clientSecret;
    public string $tokenEndpoint;
    public string $scope;
    public string $cacheFile;

    // ─────────────────────────────────────────────
    //  Konstruktor – initialisiert alle Konfigurationswerte
    // ─────────────────────────────────────────────
    public function __construct(
        string $apiBaseUrl    = 'https://schachde-apps.liga.nu/dsbwertungsportal/rs',
        string $clientId      = '',
        string $clientSecret  = '',
        string $tokenEndpoint = '',
        string $scope         = 'dwz_liste',
        string $cacheFile     = 'cachefile.json'
    ) {
        $this->apiBaseUrl    = $apiBaseUrl;
        $this->clientId      = $clientId;
        $this->clientSecret  = $clientSecret;
        $this->tokenEndpoint = $tokenEndpoint;
        $this->scope         = $scope;
	$this->cacheFile     = $cacheFile !== '' ? $cacheFile : sys_get_temp_dir() . '/oauth2_token_cache.json';


    }

    private static function debug(string $line) {
//        fwrite(STDERR, date("Y-m-d H:i:s") . " : ");
//        fwrite(STDERR, $line);
clm_core::$api->test_print(date("Y-m-d H:i:s") . " : ",$line);
    }
    // ─────────────────────────────────────────────
    //  Cache lesen / schreiben / löschen
    // ─────────────────────────────────────────────
    public function readCache(): array
    {
        if (!file_exists($this->cacheFile)) {
            return [];
        }
        return json_decode(file_get_contents($this->cacheFile), true) ?? [];
    }

    public function writeCache(array $data): void
    {
        file_put_contents($this->cacheFile, json_encode($data));
    }

    public function clearCache(): void
    {
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
	    clm_class_OAuth2Client::debug("🗑️  Token-Cache gelöscht.\n");
        }
    }

    // ─────────────────────────────────────────────
    //  Token-Request (flexibel für beide Grant-Typen)
    // ─────────────────────────────────────────────
    public function requestToken(array $postFields): array
    {
        $ch = curl_init($this->tokenEndpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($postFields),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_POSTREDIR      => 3,
        ]);

        $response     = curl_exec($ch);
        $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $curlError    = curl_error($ch);
        curl_close($ch);

        if ($effectiveUrl !== $this->tokenEndpoint) {
		clm_class_OAuth2Client::debug("ℹ️  Weitergeleitet zu: $effectiveUrl\n");
        }

        if ($curlError) {
            throw new RuntimeException("cURL-Fehler: $curlError");
        }

        $data = json_decode($response, true);

        if ($httpCode !== 200 || empty($data['access_token'])) {
            $errorMsg = $data['error_description'] ?? $data['error'] ?? 'Unbekannter Fehler';
            throw new RuntimeException("Token-Anfrage fehlgeschlagen (HTTP $httpCode): $errorMsg");
        }

        return $data;
    }

    // ─────────────────────────────────────────────
    //  Neuen Token via Client Credentials holen
    // ─────────────────────────────────────────────
    public function fetchNewToken(): array
    {
	    clm_class_OAuth2Client::debug("🔑 Hole neuen Access Token (client_credentials) ...\n");

        $tokenData = $this->requestToken([
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope'         => $this->scope,
        ]);

        $this->saveTokenToCache($tokenData);
	clm_class_OAuth2Client::debug("✅ Neuer Token erhalten (gültig für {$tokenData['expires_in']} Sekunden).\n");
        return $tokenData;
    }

    // ─────────────────────────────────────────────
    //  Bestehenden Token via Refresh-Token erneuern
    // ─────────────────────────────────────────────
    public function refreshToken(string $refreshToken): array
    {
	    clm_class_OAuth2Client::debug("🔄 Erneuere Access Token via Refresh-Token ...\n");

        $tokenData = $this->requestToken([
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        $this->saveTokenToCache($tokenData);
	clm_class_OAuth2Client::debug("✅ Token erneuert (gültig für {$tokenData['expires_in']} Sekunden).\n");
        return $tokenData;
    }

    // ─────────────────────────────────────────────
    //  Token-Daten im Cache speichern
    // ─────────────────────────────────────────────
    public function saveTokenToCache(array $tokenData): void
    {
        $expiresIn = $tokenData['expires_in'] ?? 3600;
        $this->writeCache([
            'access_token'  => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'] ?? null,
            'expires_at'    => time() + $expiresIn,
        ]);
    }

    // ─────────────────────────────────────────────
    //  Gültigen Access Token liefern
    //  Reihenfolge:
    //    1. Gecachter Token noch gültig   → direkt verwenden
    //    2. Refresh-Token vorhanden       → Token erneuern
    //    3. Kein Cache                    → neuen Token holen
    // ─────────────────────────────────────────────
    public function getValidToken(): string
    {
        $cache = $this->readCache();

        // 1. Access Token noch gültig? (30 Sekunden Puffer)
        if (!empty($cache['access_token']) && isset($cache['expires_at'])
            && time() < ($cache['expires_at'] - 30)) {
	    clm_class_OAuth2Client::debug("ℹ️  Verwende gecachten Access Token.\n");
            return $cache['access_token'];
        }

        // 2. Refresh-Token vorhanden → erneuern statt neu anfordern
        if (!empty($cache['refresh_token'])) {
            try {
                $tokenData = $this->refreshToken($cache['refresh_token']);
                return $tokenData['access_token'];
            } catch (RuntimeException $e) {
                // Refresh-Token ungültig → Cache leeren und neu starten
		    clm_class_OAuth2Client::debug("⚠️  Refresh fehlgeschlagen ({$e->getMessage()}), hole neuen Token ...\n");
                $this->clearCache();
            }
        }

        // 3. Komplett neuen Token holen
        $tokenData = $this->fetchNewToken();
        return $tokenData['access_token'];
    }

    // ─────────────────────────────────────────────
    //  API-Aufruf mit Bearer Token
    // ─────────────────────────────────────────────
    public function callApi(string $accessToken, string $apiUrl, string $method = 'GET', ?array $body = null): array
    {
        $ch = curl_init($apiUrl);

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json',
        ];

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            // CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ];

        if (strtoupper($method) === 'POST') {
            $options[CURLOPT_POST]       = true;
            $options[CURLOPT_POSTFIELDS] = json_encode($body ?? []);
            $headers[]                   = 'Content-Type: application/json';
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        curl_setopt_array($ch, $options);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new RuntimeException("cURL-Fehler beim API-Aufruf: $curlError");
        }

        return [
            'http_code' => $httpCode,
            'body'      => json_decode($response, true) ?? $response,
        ];
    }

    // ─────────────────────────────────────────────
    //  API-Aufruf mit automatischem Token-Refresh
    //  bei HTTP 401 (Token abgelaufen / ungültig)
    // ─────────────────────────────────────────────
    public function callApiWithRefresh(string $apiUrl, string $method = 'GET', ?array $body = null): array
    {
	//	$token  = $this->getValidToken();
	$token = "";
        $result = $this->callApi($token, $apiUrl, $method, $body);

        // Bei 401: Token per Refresh-Token erneuern und einmal wiederholen
        if ($result['http_code'] === 401) {
            echo "⚠️  HTTP 401 – Token wird erneuert ...\n";
            $cache = $this->readCache();
            $this->clearCache();

            if (!empty($cache['refresh_token'])) {
                $tokenData = $this->refreshToken($cache['refresh_token']);
                $token     = $tokenData['access_token'];
            } else {
                $tokenData = $this->fetchNewToken();
                $token     = $tokenData['access_token'];
            }

            $result = $this->callApi($token, $apiUrl, $method, $body);
        }

        return $result;
    }
}
?>
