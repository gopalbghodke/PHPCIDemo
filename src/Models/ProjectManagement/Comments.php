<?php

namespace Brunoocto\Sample\Models\ProjectManagement;

use Brunoocto\Vmodel\Models\VmodelModel;

use Brunoocto\Sample\Models\ProjectManagement\Files;
use Brunoocto\Sample\Models\ProjectManagement\Projects;
use Brunoocto\Sample\Models\ProjectManagement\Tasks;

/**
 * Comments model
 * (NOTE: Relationship might not follow a real business logic, but this is only for example)
 *
 * Tables needed:
 *  - 'comments' => The model storage
 *
 */
class Comments extends VmodelModel
{
    // We don't import SoftDeletes since the column "deleted_at" does not exists and we disallow delete/restore operations
    // This also means that a call [DELETE]/comments/{id} do a hard deletion here, and [PUT]/comments/{id}/force_delete and [PUT]/comments/{id}/force_delete do not exist.

    /**
    * All Relationships
    *
    * @var array
    */
    protected static $model_relationships = [
        // Editable (usually UP relation)
        'projects' => [ // Display name (always plural)
            true, // Authorize input
            true, // Visible
            'projects', // Method name
            'morphTo', // Relation type
            Projects::class, // Class name
        ],
        'tasks' => [
            true,
            true,
            'tasks',
            'morphTo',
            Tasks::class,
        ],
        'files' => [
            true,
            true,
            'files',
            'morphTo',
            Files::class,
        ],
        'comments' => [
            true,
            true,
            'comments',
            'morphTo',
            Comments::class,
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
    ];

    /**
     * Storage validation rules
     *
     * @var array
     */
    protected static $rules = [
        'content' => 'required',
        'relationships' => 'required', // Required relation (polymorphic version, it means it requires at least one of the relationships, not all)
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
    protected $table = 'comments';

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
        'content',
    ];

    /**
     * Default CRUD restriction per model class
     * 0100:  R
     * 1100: CR
     * 1110: CRU
     * 1111: CRUD (default)
     *
     *  @var array
     */
    protected $crud = '1100';

    /**
     * Default CRUD restriction per model class for the owner
     * 0100:  R
     * 1100: CR
     * 1110: CRU
     * 1111: CRUD (default)
     *
     *  @var array
     */
    protected $crud_owner = '1100';


    /* -------- Start - Relationships UP -------- */

    /**
     * Get all Models that own the Comments.
     * Many(Comments) to One(Project) Polymorphic
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Projects::Collection
     */
    public function projects()
    {
        /**
         * Files::class => The class name of the Relation (It's slightly different than the orginal)
         * 'other_type' => The column that stores the relation type (=table name)
         * 'other_id' => The column that stores the relation id
         *
         * morphTo will return an empty collection if other_type value is different than the table name of the class
         */
        return $this->morphTo(Projects::class, 'other_type', 'other_id');
    }

    /**
     * Get all Models that own the Comments.
     * Many(Comments) to One(Tasks) Polymorphic
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Tasks::Collection
     */
    public function tasks()
    {
        return $this->morphTo(Tasks::class, 'other_type', 'other_id');
    }

    /**
     * Get all Models that own the Comments.
     * Many(Comments) to One(Project) Polymorphic
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Comments::Collection
     */
    public function comments()
    {
        return $this->morphTo(Comments::class, 'other_type', 'other_id');
    }

    /**
     * Get all Models that own the Comments.
     * Many(Comments) to One(Files) Polymorphic
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Files::Collection
     */
    public function files()
    {
        return $this->morphTo(Files::class, 'other_type', 'other_id');
    }

    /* -------- Start - Relationships DOWN -------- */

    /**
     * Get all Files that belong to the Comments.
     * Many(Comments) to Many(Files) Polymorphic
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Files::Collection
     */
    public function childrenFiles()
    {
        /**
         * Files::class => The class name of the Relation
         * 'other' => Does define the prefix {prefix}_id, {prefix}_type, but it is unused here since we redeclare them in later parameters
         * 'files_x_other' => Pivot table name
         * 'other_id' => This is the ID of current model (Comments)
         * 'file_id' => This is the ID of the relation (Files)
         * withPivot('visibility') => adding 'visibility' does add this relation value (specific to the combo) to the result
         */
        return $this->morphToMany(Files::class, 'other', 'other_x_file', 'other_id', 'file_id')->withPivot('visibility');
    }

    /**
     * Get all Comments that belong to the Comments.
     * One(Comments) to Many(Comments)
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Comments::Collection
     */
    public function childrenComments()
    {
        return $this->morphMany(Comments::class, 'other', 'other_type', 'other_id');
    }

    /* -------- End - Relationships -------- */

    /**
     * Disable Deletion
     *
     * @return false
     */
    public function delete()
    {
        \LinckoJson::error(405, 'Operation not allowed.');
        return false;
    }

    /**
     * Disable Restoration
     * Note that we need to use restoreItem(), not restore()
     *
     * @return false
     */
    public function restoreItem()
    {
        \LinckoJson::error(405, 'Operation not allowed.');
        return false;
    }

    /**
     * Save the model
     *
     * @return boolean
     */
    public function save(array $options = [])
    {
        // Disable Update (PATCH)
        if (isset($this->id)) {
            \LinckoJson::error(405, 'Operation not allowed.');
            return false;
        }
        return parent::save($options);
    }
}
