<?php

namespace Brunoocto\Sample\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Sample model
 *
 */
class Sample extends Model
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
    protected $table = 'samples';

    /**
     * Guarded attributes
     * Array of attributes that we do not want to be mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
