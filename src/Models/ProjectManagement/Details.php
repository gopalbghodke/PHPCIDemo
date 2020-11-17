<?php

namespace Brunoocto\Sample\Models\ProjectManagement;

use Illuminate\Database\Eloquent\SoftDeletes;
use Brunoocto\Vmodel\Models\VmodelModel;

use Brunoocto\Sample\Models\ProjectManagement\NonLinears;

/**
 * Details model
 * This model does not have any meaning in a Project management system,
 * but it exists to test the "_" underscore in its relationship with the model NonLinears which contains "_" underscore in its table name.
 *
 * Tables needed:
 *  - 'details' => The model storage
 *
 */
class Details extends VmodelModel
{
    // Add "deleted_at"
    use SoftDeletes;

    /**
    * All relationships
    *
    * @var array
    */
    protected static $model_relationships = [
        // Editable (usually UP relation)
        'non_linears' => [ // Display name (always plural)
            true, // Authorize input
            true, // Visible
            'nonLinears', // Method name
            'belongsTo', // Relation type
            NonLinears::class, // Class name
        ],
    ];

    /**
     * Storage validation rules
     *
     * @var array
     */
    protected static $rules = [
        'title' => 'required|max:1000',
        'relationships' => 'required', // Required relation (We use Polymorphic version to simplify the reading, but we can also use 'project' => 'required' instead)
    ];

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
    protected $table = 'details';

    /**
     * List visible keys for json response.
     * Anything starting with a single underscore are reserved for relationships.
     * VmodelModel does always add by default the following fields if they exist in the table:
     *  'id',
     *  'created_at',
     *  'created_by',
     *  'updated_at',
     *  'updated_by',
     *  'deleted_at',
     *  'deleted_by',
     *
     * @var array
     */
    protected $visible = [
        'title',
    ];

    /**
     * Force the output format of some keys
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string',
    ];

    /* -------- Start - Relationships UP -------- */

    /**
     * Get the Projects that own the Details.
     * One(NonLinears) to Many(Details)
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\NonLinears
     */
    public function nonLinears()
    {
        // 'non_linear_id' is a field used in Details class to define the relationship between both objects
        return $this->belongsTo(NonLinears::class, 'non_linear_id');
    }

    /* -------- End - Relationships -------- */
}
