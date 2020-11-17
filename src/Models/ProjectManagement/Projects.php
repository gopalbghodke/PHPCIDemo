<?php

namespace Brunoocto\Sample\Models\ProjectManagement;

use Illuminate\Database\Eloquent\SoftDeletes;
use Brunoocto\Vmodel\Models\VmodelModel;

use Brunoocto\Sample\Models\ProjectManagement\Tasks;
use Brunoocto\Sample\Models\ProjectManagement\Comments;
use Brunoocto\Sample\Models\ProjectManagement\NonLinears;

/**
 * Projects model
 * (NOTE: Relationship might not follow a real business logic, but this is only for example)
 *
 * Tables needed:
 *  - 'projects' => The model storage
 *
 */
class Projects extends VmodelModel
{
    // Add "deleted_at"
    use SoftDeletes;

    /**
    * All Relationships
    *
    * @var array
    */
    protected static $model_relationships = [
        // Non-editable (usually DOWN relation)
        'children_tasks' => [ // Display name (always plural)
            false, // Unauthorize input
            true, // Visible
            'childrenTasks', // Method name, (must be camelCase of the display name)
            'hasMany', // Relation type
            Tasks::class, // Class name
        ],
        'children_comments' => [
            false,
            true,
            'childrenComments',
            'morphMany',
            Comments::class,
        ],
        'children_non_linears' => [
            false,
            true,
            'childrenNonLinears',
            'hasMany',
            NonLinears::class,
        ],
    ];

    /**
     * Storage validation rules
     *
     * @var array
     */
    protected static $rules = [
        'title' => 'required|max:1000',
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
    protected $table = 'projects';

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

    /* -------- Start - Relationships DOWN -------- */

    /**
     * Get all Tasks that belong to the Projects.
     * One(Projects) to Many(Tasks)
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Tasks::Collection
     */
    public function childrenTasks()
    {
        // 'project_id' is a field used in Tasks class to define the relationship between both objects
        return $this->hasMany(Tasks::class, 'project_id');
    }

    /**
     * Get all Comments that belong to the Projects.
     * One(Projects) to Many(Comments) Polymorphic
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Comments::Collection
     */
    public function childrenComments()
    {
        return $this->morphMany(Comments::class, 'other', 'other_type', 'other_id');
    }

    /**
     * Get all NonLinears that belong to the Projects.
     * One(Projects) to Many(NonLinears)
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\NonLinears::Collection
     */
    public function childrenNonLinears()
    {
        // 'project_id' is a field used in NonLinears class to define the relationship between both objects
        return $this->hasMany(NonLinears::class, 'project_id');
    }

    /* -------- End - Relationships -------- */
}
