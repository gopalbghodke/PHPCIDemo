<?php

namespace Brunoocto\Sample\Models\ProjectManagement;

use Illuminate\Database\Eloquent\SoftDeletes;
use Brunoocto\Vmodel\Models\VmodelModel;

use Brunoocto\Sample\Models\ProjectManagement\Files;
use Brunoocto\Sample\Models\ProjectManagement\Comments;
use Brunoocto\Sample\Models\ProjectManagement\Milestones;
use Brunoocto\Sample\Models\ProjectManagement\Projects;

/**
 * Tasks model
 * (NOTE: Relationship might not follow a real business logic, but this is only for example)
 *
 * Tables needed:
 *  - 'tasks' => The model storage
 *
 */
class Tasks extends VmodelModel
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
        // Non-editable (usually DOWN relation)
        'children_files' => [
            false,
            true,
            'childrenFiles', // Must be camelCase of the display name
            'morphToMany',
            Files::class,
        ],
        'children_comments' => [
            false,
            true,
            'childrenComments',
            'morphMany',
            Comments::class,
        ],
        'children_milestones' => [
            false,
            true,
            'childrenMilestones',
            'belongsToMany',
            Milestones::class,
        ],
    ];

    /**
     * Storage validation rules
     *
     * @var array
     */
    protected static $rules = [
        'title' => 'required|max:1000',
        'projects' => 'required', // Required relation (We use can Polymorphic version 'relationships' to simplify the reading, but we can also use 'projects' => 'required' instead)
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
    protected $table = 'tasks';

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
        'content',
    ];

    /**
     * Force the output format of some keys
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string',
        'content' => 'string',
    ];


    /* -------- Start - Relationships UP -------- */

    /**
     * Get the Projects that own the Tasks.
     * One(Projects) to Many(Tasks)
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Projects
     */
    public function projects()
    {
        // 'project_id' is a field used in Tasks class to define the relationship between both objects
        return $this->belongsTo(Projects::class, 'project_id');
    }

    /* -------- Start - Relationships DOWN -------- */

    /**
     * Get all Files that belong to the Tasks.
     * Many(Tasks) to Many(Files) Polymorphic
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Files::Collection
     */
    public function childrenFiles()
    {
        /**
         * Files::class => The class name of the Relation
         * 'other' => Does define the prefix {prefix}_id, {prefix}_type, but it is unused here since we redeclare them in later parameters
         * 'other_x_file' => Pivot table name {parent}_x_{child}
         * 'other_id' => This is the ID of current model (Tasks)
         * 'file_id' => This is the ID of the relation (Files)
         * withPivot('visibility') => adding 'visibility' does add this relation value (specific to the combo) to the result
         */
        return $this->morphToMany(Files::class, 'other', 'other_x_file', 'other_id', 'file_id')->withPivot('visibility');
    }

    /**
     * Get all Comments that belong to the Tasks.
     * One(Tasks) to Many(Comments) Polymorphic
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Comments::Collection
     */
    public function childrenComments()
    {
        return $this->morphMany(Comments::class, 'other', 'other_type', 'other_id');
    }

    /**
     * Get all Milestone that belong to the Tasks.
     * Many(Tasks) to Many(Milestones)
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Milestones::Collection
     */
    public function childrenMilestones()
    {
        return $this->belongsToMany(Milestones::class, 'task_x_milestone', 'task_id', 'milestone_id')->withPivot('priority');
    }

    /* -------- End - Relationships -------- */
}
