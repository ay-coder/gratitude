<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\UserGroupsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\UserGroups\EloquentUserGroupsRepository;
use App\Models\UserGroupMembers\UserGroupMembers;
use App\Models\Access\User\User;

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

                $allMembers = User::whereIn('id', $members)->get();

                foreach($allMembers as $groupMember)
                {
                    $text       = $userInfo->name . ' added you to a group.';
                    $payload    = [
                        'mtitle'            => '',
                        'mdesc'             => $text,
                        'user_id'           => $groupMember->id,
                        'mtype'             => 'ADDED_GROUP_MEMBER'
                    ];
                    $storeNotification = [
                        'user_id'           => $groupMember->id,
                        'from_user_id'      => $userInfo->id,
                        'description'       => $text,
                        'notification_type' => 'ADDED_GROUP_MEMBER'
                    ];

                    access()->addNotification($storeNotification);
                    access()->sentPushNotification($groupMember, $payload);
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

    /**
     * Add Member
     *
     * @param Request $request
     * @return string
     */
    public function addMember(Request $request)
    {
        if($request->has('group_id') && $request->has('member_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $model      = $this->repository->model->with('group_members')->where([
                'user_id'   => $userInfo->id,
                'id'        => $request->get('group_id')
            ])->first();

            if(count($model) == 0 )
            {
                return $this->setStatusCode(404)->failureResponse([
                    'reason' => 'No Group Exists!'
                ], 'No Group Exists!');
            }

            $memberId = $request->get('member_id');
            $isExist  = $model->group_members->where('member_id', $memberId)->first();

            if($isExist)
            {
                return $this->setStatusCode(404)->failureResponse([
                    'reason' => 'Member already Exists!'
                ], 'Member already Exists!');
            }

            $status = $model->group_members()->create([
                'group_id'  => $request->get('group_id'),
                'user_id'   => $userInfo->id,
                'member_id' => $memberId
            ]);

            if($status)
            {
                return $this->successResponse([
                    'success' => 'Member added Successfully'
                ], 'Member added Successfully');
            }
        }
        
        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Remove Member
     *
     * @param Request $request
     * @return string
     */
    public function removeMember(Request $request)
    {
        if($request->has('group_id') && $request->has('member_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $model      = $this->repository->model->with('group_members')->where([
                'user_id'   => $userInfo->id,
                'id'        => $request->get('group_id')
            ])->first();

            if(count($model) == 0 )
            {
                return $this->setStatusCode(404)->failureResponse([
                    'reason' => 'No Group Exists!'
                ], 'No Group Exists!');
            }

            $memberId = $request->get('member_id');
            $isExist  = $model->group_members->where('member_id', $memberId)->first();

            if(!$isExist)
            {
                return $this->setStatusCode(404)->failureResponse([
                    'reason' => 'Member already left the Group!'
                ], 'Member already left the Group!');
            }

            $status = $model->group_members()->where([
                'group_id'  => $request->get('group_id'),
                'member_id' => $memberId
            ])->delete();

            if($status)
            {
                return $this->successResponse([
                    'success' => 'Member removed Successfully'
                ], 'Member removed Successfully');
            }
        }
        
        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * left Group
     *
     * @param Request $request
     * @return string
     */
    public function leftGroup(Request $request)
    {
        if($request->has('group_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $model      = UserGroupMembers::where([
                'member_id'   => $userInfo->id,
                'group_id'    => $request->get('group_id')
            ])->first();

            if(count($model) == 0 )
            {
                return $this->setStatusCode(404)->failureResponse([
                    'reason' => 'No Group Exists!'
                ], 'No Group Exists!');
            }

            if($model->delete())
            {
                return $this->successResponse([
                    'success' => 'Group Left Successfully'
                ], 'Group Left Successfully');
            }
        }
        
        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }
    
}