<?php

namespace Brunoocto\Sample\Services;

use Brunoocto\Sample\Contracts\SampleInterface;

/**
 * Sample service to explain the difference between:
 *   - Dependency injection
 *   - Interface dependency injection
 *   - Facade
 *   - Maker
 *
 */
class SampleService implements SampleInterface
{

    /**
     * At true it helps to know if the instance binding has properly been done
     *
     * @var boolean
     */
    protected $binding = false;

    /**
     * Record if the binding worked
     *
     * @return void
     */
    public function bind()
    {
        $this->binding = true;
    }


    /**
     * Send a successul response
     *
     * @param object $data The data send, usually a Database model instance
     * @param int $status HTTP Code
     * @param string $message A human-readable message to inform the client (it will attached to 'meta')
     * @param mixed $meta Any additionnal information we want to send to the client
     * @return Response
     */
    public function json($data = null, $status = 200, $message = '', $meta = null)
    {
        // If the $data is not an object or an array, we convert it as an Array first, the first key will be 0 then.
        if (!is_object($data) && !is_array($data)) {
            $data = (array)$data;
        }
        
        // We make sure to return an object
        $json_meta = (object)$meta;
        $json_meta->binding = false;
        if ($this->binding) {
            $json_meta->binding = true;
        }
        $json_meta->message = (string)$message;
        return response()
        ->json(
            [
                // The document's "primary data". We make sure it returns an object
                'data' => (object)$data,
                'errors' => (object)[],
                // A meta object that contains non-standard meta-information.
                'meta' => $json_meta,
            ],
            // HTTP Code
            (int)$status
        )
        // Use vnd.api+json as standard response
        ->header('Content-Type', 'application/vnd.api+json');
    }
}
