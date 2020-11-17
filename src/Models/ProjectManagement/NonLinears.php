<?php

namespace Brunoocto\Sample\Models\ProjectManagement;

use Illuminate\Database\Eloquent\SoftDeletes;
use Brunoocto\Vmodel\Models\VmodelModel;

use Brunoocto\Sample\Models\ProjectManagement\Projects;
use Brunoocto\Sample\Models\ProjectManagement\Details;
use Brunoocto\Sample\Models\ProjectManagement\Files;

/**
 * NonLinears model
 * This model does not have any meaning in a Project management system,
 * but it exists to test the "_" underscore in a model name.
 *
 * Tables needed:
 *  - 'non_linears' => The model storage
 *
 */
class NonLinears extends VmodelModel
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
        'projects' => [ // Display name (always plural)
            true, // Authorize input
            true, // Visible
            'projects', // Method name
            'belongsTo', // Relation type
            Projects::class, // Class name
        ],
        'details' => [
            false,
            true,
            'details',
            'hasMany',
            Details::class,
        ],
        'files' => [
            false,
            true,
            'files',
            'morphToMany',
            Files::class,
        ],
    ];

    /**
     * Storage validation rules
     *
     * @var array
     */
    protected static $rules = [
        'link' => 'required|max:1000',
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
    protected $table = 'non_linears';

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
        'link',
    ];

    /**
     * Force the output format of some keys
     *
     * @var array
     */
    protected $casts = [
        'link' => 'string',
    ];

    /* -------- Start - Relationships UP -------- */

    /**
     * Get the Projects that own the NonLinears.
     * One(Projects) to Many(NonLinears)
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Projects
     */
    public function projects()
    {
        // 'project_id' is a field used in NonLinears class to define the relationship between both objects
        return $this->belongsTo(Projects::class, 'project_id');
    }

    /* -------- Start - Relationships DOWN -------- */

    /**
     * Get all Details that belong to the NonLinears.
     * One(NonLinears) to Many(Details)
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Details::Collection
     */
    public function details()
    {
        // 'project_id' is a field used in Details class to define the relationship between both objects
        return $this->hasMany(Details::class, 'non_linear_id');
    }

    /**
     * Files::class => The class name of the Relation
     * 'other' => Does define the prefix {prefix}_id, {prefix}_type, but it is unused here since we redeclare them in later parameters
     * 'other_x_file' => Pivot table name {parent}_x_{child}
     * 'other_id' => This is the ID of current model (NonLinears)
     * 'file_id' => This is the ID of the relation (Files)
     * withPivot('visibility') => adding 'visibility' does add this relation value (specific to the combo) to the result
     */
    public function files()
    {
        return $this->morphToMany(Files::class, 'other', 'other_x_file', 'other_id', 'file_id')->withPivot('visibility');
    }

    /* -------- End - Relationships -------- */
}
