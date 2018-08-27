<?php namespace App\Repositories\Posts;

/**
 * Class EloquentPostsRepository
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\Posts\Posts;
use App\Repositories\DbRepository;
use App\Exceptions\GeneralException;
use App\Models\Access\User\User;

class EloquentPostsRepository extends DbRepository
{
    /**
     * Posts Model
     *
     * @var Object
     */
    public $model;

    /**
     * Posts Title
     *
     * @var string
     */
    public $moduleTitle = 'Posts';

    /**
     * Table Headers
     *
     * @var array
     */
    public $tableHeaders = [
        'username'          => 'Creator',
        'tag_username'      => 'Tagged User',
        'media'             => 'Media',
        'description'       => 'Description',
        'is_image'          => 'Image',
        'is_video'          => 'Video',
        "actions"         => "Actions"
    ];

    /**
     * Table Columns
     *
     * @var array
     */
    public $tableColumns = [
        'username' =>   [
                'data'          => 'username',
                'name'          => 'username',
                'searchable'    => true,
                'sortable'      => true
            ],
		'tag_username' =>   [
                'data'          => 'tag_username',
                'name'          => 'tag_username',
                'searchable'    => true,
                'sortable'      => true
            ],
		
		'media' =>   [
                'data'          => 'media',
                'name'          => 'media',
                'searchable'    => true,
                'sortable'      => true
            ],
		'description' =>   [
                'data'          => 'description',
                'name'          => 'description',
                'searchable'    => true,
                'sortable'      => true
            ],
		'is_image' =>   [
                'data'          => 'is_image',
                'name'          => 'is_image',
                'searchable'    => true,
                'sortable'      => true
            ],
		'is_video' =>   [
                'data'          => 'is_video',
                'name'          => 'is_video',
                'searchable'    => true,
                'sortable'      => true
            ],
	    'actions' => [
            'data'          => 'actions',
            'name'          => 'actions',
            'searchable'    => false,
            'sortable'      => false
        ]
    ];

    /**
     * Is Admin
     *
     * @var boolean
     */
    protected $isAdmin = false;

    /**
     * Admin Route Prefix
     *
     * @var string
     */
    public $adminRoutePrefix = 'admin';

    /**
     * Client Route Prefix
     *
     * @var string
     */
    public $clientRoutePrefix = 'frontend';

    /**
     * Admin View Prefix
     *
     * @var string
     */
    public $adminViewPrefix = 'backend';

    /**
     * Client View Prefix
     *
     * @var string
     */
    public $clientViewPrefix = 'frontend';

    /**
     * Module Routes
     *
     * @var array
     */
    public $moduleRoutes = [
        'listRoute'     => 'posts.index',
        'createRoute'   => 'posts.create',
        'storeRoute'    => 'posts.store',
        'editRoute'     => 'posts.edit',
        'updateRoute'   => 'posts.update',
        'deleteRoute'   => 'posts.destroy',
        'dataRoute'     => 'posts.get-list-data'
    ];

    /**
     * Module Views
     *
     * @var array
     */
    public $moduleViews = [
        'listView'      => 'posts.index',
        'createView'    => 'posts.create',
        'editView'      => 'posts.edit',
        'deleteView'    => 'posts.destroy',
    ];

    /**
     * Construct
     *
     */
    public function __construct()
    {
        $this->model = new Posts;
    }

    /**
     * Create Posts
     *
     * @param array $input
     * @return mixed
     */
    public function create($input)
    {
        $input = $this->prepareInputData($input, true);
        $model = $this->model->create($input);

        if($model)
        {
            return $model;
        }

        return false;
    }

    /**
     * Update Posts
     *
     * @param int $id
     * @param array $input
     * @return bool|int|mixed
     */
    public function update($id, $input)
    {
        $model = $this->model->find($id);

        if($model)
        {
            $input = $this->prepareInputData($input);

            return $model->update($input);
        }

        return false;
    }

    /**
     * Destroy Posts
     *
     * @param int $id
     * @return mixed
     * @throws GeneralException
     */
    public function destroy($id)
    {
        $model = $this->model->find($id);

        if($model)
        {
            return $model->delete();
        }

        return  false;
    }

    /**
     * Get All
     *
     * @param string $orderBy
     * @param string $sort
     * @return mixed
     */
    public function getAll($condition = array(), $orderBy = 'id', $sort = 'asc')
    {
        if(isset($condition))
        {
            return $this->model->where($condition)
            ->where('is_accepted', 1)
            ->with(['user', 'tag_user'])->orderBy($orderBy, $sort)->get();
        }

        return $this->model->with(['user', 'tag_user'])
            ->where('is_accepted', 1)
            ->orderBy($orderBy, $sort)->get();
    }

    /**
     * Get All
     *
     * @param string $orderBy
     * @param string $sort
     * @return mixed
     */
    public function filterAll1($condition = array(), $search, $orderBy = 'id', $sort = 'asc')
    {
        if(isset($condition))
        {
            return $this->model
                ->where('description', 'LIKE', '%' . $search . '%')
                ->where($condition)->with(['user', 'tag_user'])->orderBy($orderBy, $sort)->get();
        }

        return $this->model->where('description', 'LIKE', '%' . $search . '%')->with(['user', 'tag_user'])->orderBy($orderBy, $sort)->get();
    }

    /**
     * Get All
     *
     * @param string $orderBy
     * @param string $sort
     * @return mixed
     */
    public function filterAll($condition = array(), $search, $orderBy = 'id', $sort = 'asc')
    {
        if(isset($condition))
        {
            $userIds = User::where('name','LIKE', '%' . $search . '%')
                ->orWhere('username','LIKE', '%' . $search . '%')
                ->orWhere('email','LIKE', '%' . $search . '%')
                ->orWhere('phone','LIKE', '%' . $search . '%')
                ->pluck('id')->toArray();

            return $this->model
                ->whereIn('user_id', $userIds)
                ->orWhereIn('tag_user_id', $userIds)
                ->with(['user', 'tag_user'])
                ->orderBy($orderBy, $sort)->get();
        }

        return $this->model->where('description', 'LIKE', '%' . $search . '%')->with(['user', 'tag_user'])->orderBy($orderBy, $sort)->get();
    }

    /**
     * Get by Id
     *
     * @param int $id
     * @return mixed
     */
    public function getById($id = null)
    {
        if($id)
        {
            return $this->model->find($id);
        }

        return false;
    }

    /**
     * Get Table Fields
     *
     * @return array
     */
    public function getTableFields()
    {
        return [
            $this->model->getTable().'.*'
        ];
    }

    /**
     * @return mixed
     */
    public function getForDataTable()
    {
        return $this->model->select($this->getTableFields())->get();
    }

    /**
     * Set Admin
     *
     * @param boolean $isAdmin [description]
     */
    public function setAdmin($isAdmin = false)
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Prepare Input Data
     *
     * @param array $input
     * @param bool $isCreate
     * @return array
     */
    public function prepareInputData($input = array(), $isCreate = false)
    {
        if($isCreate)
        {
            $input = array_merge($input, ['user_id' => access()->user()->id]);
        }

        return $input;
    }

    /**
     * Get Table Headers
     *
     * @return string
     */
    public function getTableHeaders()
    {
        if($this->isAdmin)
        {
            return json_encode($this->setTableStructure($this->tableHeaders));
        }

        $clientHeaders = $this->tableHeaders;

        unset($clientHeaders['username']);

        return json_encode($this->setTableStructure($clientHeaders));
    }

    /**
     * Get Table Columns
     *
     * @return string
     */
    public function getTableColumns()
    {
        if($this->isAdmin)
        {
            return json_encode($this->setTableStructure($this->tableColumns));
        }

        $clientColumns = $this->tableColumns;

        unset($clientColumns['username']);

        return json_encode($this->setTableStructure($clientColumns));
    }
}