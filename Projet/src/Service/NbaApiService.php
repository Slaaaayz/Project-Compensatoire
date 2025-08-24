<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class NbaApiService
{
    private const API_BASE_URL = 'https://api.balldontlie.io/v1';
    private const API_KEY = 'YOUR_API_KEY';
    private const RATE_LIMIT_DELAY = 15; // 15 secondes entre les requêtes (4 par minute)
    private const CACHE_DURATION = 1800; // 30 minutes de cache au lieu de 10

    private $lastRequestTime = 0;
    private $cache = [];

    public function __construct(
        private HttpClientInterface $httpClient
    ) {}

    private function respectRateLimit(): void
    {
        $currentTime = time();
        $timeSinceLastRequest = $currentTime - $this->lastRequestTime;
        
        if ($timeSinceLastRequest < self::RATE_LIMIT_DELAY) {
            $sleepTime = self::RATE_LIMIT_DELAY - $timeSinceLastRequest;
            sleep($sleepTime);
        }
        
        $this->lastRequestTime = time();
    }

    private function getCacheKey(string $endpoint, array $params = []): string
    {
        return $endpoint . '_' . md5(serialize($params));
    }

    private function getFromCache(string $cacheKey): ?array
    {
        if (isset($this->cache[$cacheKey])) {
            $cached = $this->cache[$cacheKey];
            if (time() - $cached['timestamp'] < self::CACHE_DURATION) {
                return $cached['data'];
            }
            unset($this->cache[$cacheKey]);
        }
        return null;
    }

    private function setCache(string $cacheKey, array $data): void
    {
        $this->cache[$cacheKey] = [
            'data' => $data,
            'timestamp' => time()
        ];
    }

    private function handleApiResponse($response): array
    {
        $statusCode = $response->getStatusCode();
        
        if ($statusCode === 429) {
            // Rate limit atteint, attendre plus longtemps
            sleep(45); // Attendre 45 secondes supplémentaires
            throw new \Exception('Rate limit atteint, veuillez réessayer dans quelques instants');
        }
        
        if ($statusCode >= 400) {
            throw new \Exception("Erreur API: $statusCode");
        }
        
        return $response->toArray();
    }

    public function getPlayers(int $page = 1, int $perPage = 25): array
    {
        $cacheKey = $this->getCacheKey('players', ['page' => $page, 'per_page' => $perPage]);
        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $this->respectRateLimit();
        
        try {
            $query = ['per_page' => $perPage];
            
            // Utiliser le cursor pour la pagination
            if ($page > 1) {
                $cursor = ($page - 1) * $perPage;
                $query['cursor'] = $cursor;
            }

            $response = $this->httpClient->request('GET', self::API_BASE_URL . '/players', [
                'headers' => [
                    'Authorization' => self::API_KEY,
                    'Accept' => 'application/json'
                ],
                'query' => $query,
                'timeout' => 30
            ]);

            $data = $this->handleApiResponse($response);
            
            // Ajouter les métadonnées de pagination pour compatibilité
            if (!isset($data['meta'])) {
                $data['meta'] = [];
            }
            
            // Calculer le total_count approximatif basé sur le cursor
            if (isset($data['meta']['next_cursor'])) {
                $data['meta']['total_count'] = $data['meta']['next_cursor'] + count($data['data']);
            } else {
                $data['meta']['total_count'] = count($data['data']);
            }
            
            $data['meta']['per_page'] = $perPage;
            $data['meta']['current_page'] = $page;

            $this->setCache($cacheKey, $data);
            return $data;
        } catch (\Exception $e) {
            return [
                'data' => [],
                'meta' => [
                    'total_count' => 0,
                    'per_page' => $perPage,
                    'current_page' => $page,
                ],
                'error' => $e->getMessage()
            ];
        }
    }

    public function getPlayer(int $id): array
    {
        $cacheKey = $this->getCacheKey('player', ['id' => $id]);
        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $this->respectRateLimit();
        
        try {
            $response = $this->httpClient->request('GET', self::API_BASE_URL . '/players/' . $id, [
                'headers' => [
                    'Authorization' => self::API_KEY,
                    'Accept' => 'application/json'
                ],
                'timeout' => 30
            ]);
            
            $statusCode = $response->getStatusCode();
            
            if ($statusCode === 429) {
                // Rate limit atteint, attendre plus longtemps
                sleep(45);
                throw new \Exception('Rate limit de l\'API atteint. Veuillez attendre quelques instants avant de réessayer.');
            }
            
            if ($statusCode >= 400) {
                throw new \Exception("Erreur API: $statusCode");
            }
            
            $data = $response->toArray();
            $this->setCache($cacheKey, $data);
            return $data;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getTeams(int $page = 1, int $perPage = 30): array
    {
        $cacheKey = $this->getCacheKey('teams', ['page' => $page, 'per_page' => $perPage]);
        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $this->respectRateLimit();
        
        try {
            $query = ['per_page' => $perPage];
            
            // Utiliser le cursor pour la pagination
            if ($page > 1) {
                $cursor = ($page - 1) * $perPage;
                $query['cursor'] = $cursor;
            }

            $response = $this->httpClient->request('GET', self::API_BASE_URL . '/teams', [
                'headers' => [
                    'Authorization' => self::API_KEY,
                    'Accept' => 'application/json'
                ],
                'query' => $query,
                'timeout' => 30
            ]);

            $data = $this->handleApiResponse($response);
            
            // Ajouter les métadonnées de pagination pour compatibilité
            if (!isset($data['meta'])) {
                $data['meta'] = [];
            }
            
            // Calculer le total_count approximatif basé sur le cursor
            if (isset($data['meta']['next_cursor'])) {
                $data['meta']['total_count'] = $data['meta']['next_cursor'] + count($data['data']);
            } else {
                $data['meta']['total_count'] = count($data['data']);
            }
            
            $data['meta']['per_page'] = $perPage;
            $data['meta']['current_page'] = $page;

            $this->setCache($cacheKey, $data);
            return $data;
        } catch (\Exception $e) {
            return [
                'data' => [],
                'meta' => [
                    'total_count' => 0,
                    'per_page' => $perPage,
                    'current_page' => $page,
                ],
                'error' => $e->getMessage()
            ];
        }
    }

    public function getTeam(int $id): array
    {
        $cacheKey = $this->getCacheKey('team', ['id' => $id]);
        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $this->respectRateLimit();
        
        try {
            $response = $this->httpClient->request('GET', self::API_BASE_URL . '/teams/' . $id, [
                'headers' => [
                    'Authorization' => self::API_KEY,
                    'Accept' => 'application/json'
                ],
                'timeout' => 30
            ]);
            $data = $this->handleApiResponse($response);
            $this->setCache($cacheKey, $data);
            return $data;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getGames(int $page = 1, int $perPage = 25): array
    {
        $cacheKey = $this->getCacheKey('games', ['page' => $page, 'per_page' => $perPage]);
        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $this->respectRateLimit();
        
        try {
            $query = ['per_page' => $perPage];
            
            // Utiliser le cursor pour la pagination
            if ($page > 1) {
                $cursor = ($page - 1) * $perPage;
                $query['cursor'] = $cursor;
            }

            $response = $this->httpClient->request('GET', self::API_BASE_URL . '/games', [
                'headers' => [
                    'Authorization' => self::API_KEY,
                    'Accept' => 'application/json'
                ],
                'query' => $query,
                'timeout' => 30
            ]);

            $data = $this->handleApiResponse($response);
            
            // Ajouter les métadonnées de pagination pour compatibilité
            if (!isset($data['meta'])) {
                $data['meta'] = [];
            }
            
            // Calculer le total_count approximatif basé sur le cursor
            if (isset($data['meta']['next_cursor'])) {
                $data['meta']['total_count'] = $data['meta']['next_cursor'] + count($data['data']);
            } else {
                $data['meta']['total_count'] = count($data['data']);
            }
            
            $data['meta']['per_page'] = $perPage;
            $data['meta']['current_page'] = $page;

            $this->setCache($cacheKey, $data);
            return $data;
        } catch (\Exception $e) {
            return [
                'data' => [],
                'meta' => [
                    'total_count' => 0,
                    'per_page' => $perPage,
                    'current_page' => $page,
                ],
                'error' => $e->getMessage()
            ];
        }
    }

    public function getGame(int $id): array
    {
        $cacheKey = $this->getCacheKey('game', ['id' => $id]);
        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $this->respectRateLimit();
        
        try {
            $response = $this->httpClient->request('GET', self::API_BASE_URL . '/games/' . $id, [
                'headers' => [
                    'Authorization' => self::API_KEY,
                    'Accept' => 'application/json'
                ],
                'timeout' => 30
            ]);
            $data = $this->handleApiResponse($response);
            $this->setCache($cacheKey, $data);
            return $data;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getPlayersByTeam(int $teamId): array
    {
        $cacheKey = $this->getCacheKey('players_by_team', ['team_id' => $teamId]);
        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $this->respectRateLimit();
        
        try {
            $response = $this->httpClient->request('GET', self::API_BASE_URL . '/players', [
                'headers' => [
                    'Authorization' => self::API_KEY,
                    'Accept' => 'application/json'
                ],
                'query' => [
                    'team_ids[]' => $teamId,
                    'per_page' => 100
                ],
                'timeout' => 30
            ]);

            $data = $this->handleApiResponse($response);
            $this->setCache($cacheKey, $data);
            return $data;
        } catch (\Exception $e) {
            return ['data' => [], 'error' => $e->getMessage()];
        }
    }
}
