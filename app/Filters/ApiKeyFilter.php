<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiKeyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $sentKey     = $request->getHeaderLine('X-API-Key');
        $expectedKey = (string) env('API_KEY', '');

        if ($expectedKey === '' || ! hash_equals($expectedKey, $sentKey)) {
            return service('response')->setStatusCode(401)->setJSON([
                'status' => 401,
                'error'  => [
                    'code'    => 'unauthorized',
                    'message' => 'Missing or wrong API key.',
                ],
            ]);
        }
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // Nothing to do after the request.
    }
}
