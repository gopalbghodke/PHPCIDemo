<?php

namespace Brunoocto\Sample\Models\ProjectManagement;

use Brunoocto\Vmodel\Models\VmodelModel;

/**
 * Fails model
 * IMPORTANT This class is badly to test its failure in Unit test
 *
 */
class Fails extends VmodelModel
{
    /**
     * Database connection
     *
     * @var string
     */
    protected $connection = 'brunoocto_sample';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fails';
}
