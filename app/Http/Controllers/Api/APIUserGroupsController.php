<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\UserGroupsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\UserGroups\EloquentUserGroupsRepository;
use App\Models\UserGroupMembers\UserGroupMembers;

class APIUserGroupsController extends BaseApiController
{
    /**
     * UserGroups Transformer
     *
     * @var Object
     */
    protected $usergroupsTransformer;

    /**
     * Repository
     *
     * @var Object
     */
    protected $repository;

    /**
     * PrimaryKey
     *
     * @var string
     */
    protected $primaryKey = 'usergroupsId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentUserGroupsRepository();
        $this->usergroupsTransformer = new UserGroupsTransformer();
    }

    /**
     * List of All UserGroups
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $groupIds   = UserGroupMembers::where([
            'member_id' => $userInfo->id
        ])->pluck('group_id');
        $items      = $this->repository->model->with([
            'group_members', 'group_members.user'
        ])
        ->whereIn('id', $groupIds)
        ->get();
        
        if(isset($items) && count($items))
        {
            $itemsOutput = $this->usergroupsTransformer->transformUserGroupsWithMembers($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find UserGroups!'
            ], 'No UserGroups Found !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        if($request->has('title'))
        {
            $input      = $request->all();
            $userInfo   = $this->getAuthenticatedUser();
            $isExist    = $this->repository->model->where([
                'title' => $request->get('title')
            ])->first();

            if(isset($isExist) && isset($isExist->id))
            {
                return $this->setStatusCode(400)->failureResponse([
                    'reason' => 'Group Name already exists !'
                    ], 'Group Name already exists !');
            }

            $model = $this->repository->model->create([
                'user_id'   => $userInfo->id,
                'title'     => $request->get('title')
            ]);

            if($model)
            {
                if(isset($input['group_members']))
                {
                    $members = explode(',', $input['group_members']);
                    $groupMemberData[] = [
                        'group_id'  => $model->id,
                        'user_id'   => $userInfo->id,
                        'member_id' => $userInfo->id
                    ];

                    foreach($members as $member)
                    {
                        if($member == $userInfo->id)
                            continue;

                        $groupMemberData[] = [
                            'group_id'  => $model->id,
                            'user_id'   => $model->user_id,
                            'member_id' => $member
                        ];
                    }

                    if(count($groupMemberData))
                    {
                        $model->group_members()->insert($groupMemberData);
                    }
                }
                
                $responseData = [
                    'message' => 'Group Created Successfully'
                ];
                return $this->successResponse($responseData, 'Group Created Successfully');   
            }

        }
        
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }

    /**
     * View
     *
     * @param Request $request
     * @return string
     */
    public function show(Request $request)
    {
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $itemData = $this->repository->getById($itemId);

            if($itemData)
            {
                $responseData = $this->usergroupsTransformer->transform($itemData);

                return $this->successResponse($responseData, 'View Item');
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or Item not exists !'
            ], 'Something went wrong !');
    }

    /**
     * Edit
     *
     * @param Request $request
     * @return string
     */
    public function edit(Request $request)
    {
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $status = $this->repository->update($itemId, $request->all());

            if($status)
            {
                $itemData       = $this->repository->getById($itemId);
                $responseData   = $this->usergroupsTransformer->transform($itemData);

                return $this->successResponse($responseData, 'UserGroups is Edited Successfully');
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Delete
     *
     * @param Request $request
     * @return string
     */
    public function delete(Request $request)
    {
        if($request->has('group_id'))
        {
            $userInfo = $this->getAuthenticatedUser();
            $isExist  = $this->repository->model->with('group_members')->where([
                'user_id'   => $userInfo->id,
                'id'        => $request->get('group_id')
            ])->first();

            if(count($isExist) == 0 )
            {
                return $this->setStatusCode(404)->failureResponse([
                    'reason' => 'No Group Exists!'
                ], 'No Group Exists!');
            }

            if($isExist->delete())
            {
                return $this->successResponse([
                    'success' => 'Group Deleted Successfully'
                ], 'Group Deleted Successfully');
            }
        }
        
        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }
}