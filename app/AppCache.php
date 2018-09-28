<?php

require_once __DIR__.'/AppKernel.php';

use FOS\HttpCache\SymfonyCache\CacheInvalidation;
use FOS\HttpCache\SymfonyCache\EventDispatchingHttpCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

class AppCache extends HttpCache
{
	protected function invalidate(Request $request, $catch = false)
    {
        if ('PURGE' !== $request->getMethod()) {
            return parent::invalidate($request, $catch);
        }

        if ('127.0.0.1' !== $request->getClientIp()) {
            return new Response(
                'Invalid HTTP method',
                Response::HTTP_BAD_REQUEST
            );
        }

        $response = new Response();
        if ($this->getStore()->purge($request->getUri())) {
            $response->setStatusCode(Response::HTTP_OK, 'Purged');
        } else {
            $response->setStatusCode(Response::HTTP_NOT_FOUND, 'Not found');
        }

        return $response;
    }

	protected function getOptions()
    {
        return array(
            'default_ttl' => 0,
            // ...
        );
    }

    /**
     * Made public to allow event listeners to do refresh operations.
     *
     * {@inheritDoc}
     */
    public function fetch(Request $request, $catch = false)
    {
        return parent::fetch($request, $catch);
    }
    
}
