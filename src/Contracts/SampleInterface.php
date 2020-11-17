<?php

namespace Brunoocto\Sample\Contracts;

interface SampleInterface
{
    /**
     * Record if the binding worked
     *
     * @return void
     */
    public function bind();

    /**
     * Send a successul response
     *
     * @param object $data The data send, usually a Database model instance
     * @param int $status HTTP Code
     * @param string $message A human-readable message to inform the client (it will attached to 'meta')
     * @param mixed $meta Any additionnal information we want to send to the client
     * @return Response
     */
    public function json($data = null, $status = 200, $message = '', $meta = null);
}
