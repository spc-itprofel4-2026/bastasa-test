<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Models\WeatherLogModel;

class Weather extends BaseController
{
    // POST /api/v1/weather/refresh
    public function refresh()
    {
        $url = 'https://api.open-meteo.com/v1/forecast'
            . '?latitude=8.2280&longitude=124.2452&current_weather=true';

        try {
            $client   = \Config\Services::curlrequest();
            $response = $client->get($url, ['timeout' => 10]);
            $result   = json_decode($response->getBody(), true);
        } catch (\Throwable $e) {
            return $this->errorResponse(502, 'upstream_error', 'Weather source unreachable.');
        }

        $temperature = $result['current_weather']['temperature'] ?? null;

        if ($temperature === null) {
            return $this->errorResponse(502, 'upstream_error', 'No temperature received.');
        }

        $model = new WeatherLogModel();
        $id    = $model->insert([
            'city'        => 'Iligan City',
            'temperature' => $temperature,
            'fetched_at'  => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setStatusCode(201)->setJSON([
            'status' => 201,
            'data'   => $this->formatRow($model->find($id)),
        ]);
    }

    // GET /api/v1/weather/logs?limit=10
    public function logs()
    {
        $limit = $this->request->getGet('limit') ?? '10';

        $limitIsValid = is_string($limit) && ctype_digit($limit)
            && (int) $limit >= 1 && (int) $limit <= 50;

        if (! $limitIsValid) {
            return $this->errorResponse(422, 'invalid_limit', 'limit must be 1 to 50.');
        }

        $rows  = (new WeatherLogModel())->orderBy('id', 'DESC')->findAll((int) $limit);
        $items = array_map(fn ($row) => $this->formatRow($row), $rows);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 200,
            'data'   => $items,
        ]);
    }

    // GET /api/v1/weather/logs/5
    public function show($id = null)
    {
        $row = (new WeatherLogModel())->find($id);

        if ($row === null) {
            return $this->errorResponse(404, 'not_found', 'No weather log with that id.');
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 200,
            'data'   => $this->formatRow($row),
        ]);
    }

    // Same error shape for every error.
    private function errorResponse(int $status, string $code, string $message)
    {
        return $this->response->setStatusCode($status)->setJSON([
            'status' => $status,
            'error'  => [
                'code'    => $code,
                'message' => $message,
            ],
        ]);
    }

    // One shared shape for a weather log, used by every endpoint.
    private function formatRow(array $row): array
    {
        return [
            'id'           => (int) $row['id'],
            'city'         => $row['city'],
            'temperatureC' => (float) $row['temperature'],
            'fetchedAt'    => date('Y-m-d\TH:i:s', strtotime($row['fetched_at'])),
        ];
    }
}
