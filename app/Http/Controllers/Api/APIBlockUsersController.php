<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\BlockUsersTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\BlockUsers\EloquentBlockUsersRepository;
use App\Models\Access\User\User;

class APIBlockUsersController extends BaseApiController
{
    /**
     * BlockUsers Transformer
     *
     * @var Object
     */
    protected $blockusersTransformer;

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
    protected $primaryKey = 'blockusersId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentBlockUsersRepository();
        $this->blockusersTransformer = new BlockUsersTransformer();
    }

    /**
     * List of All BlockUsers
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $userInfo       = $this->getAuthenticatedUser();
        $userModel      = new User;   
        $blockUserIds   = access()->getBlockUserIds($userInfo->id);

        if(1==1)
        {
            $blockedUsers = $userModel->whereIn('id', $blockUserIds)
            ->get();

            if(isset($blockedUsers) && count($blockedUsers))
            {
                $itemsOutput = $this->blockusersTransformer->blockUsersTransform($blockedUsers);

                if(count($itemsOutput) && isset($itemsOutput))
                {
                    return $this->successResponse($itemsOutput);
                }

                return $this->successResponse([], 'No Result Found !');
            }
        }
        

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Connections!'
            ], 'No Connections Found !');       
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        if($request->has('block_user_id') )
        {
            $userInfo = $this->getAuthenticatedUser();

            if($userInfo->id == $request->get('block_user_id'))
            {
                return $this->setStatusCode(200)->failureResponse([
                    'reason' => 'You can not block yourself!'
                ], 'You can not block yourself!');
            }

            $isExists = $this->repository->model->where([
                'user_id'       => $userInfo->id,
                'block_user_id' => $request->get('block_user_id')
            ])->first();

            if(isset($isExists))
            {
                return $this->setStatusCode(200)->failureResponse([
                    'reason' => 'Already Blocked!'
                ], 'Already Blocked!');
            }

            $status = $this->repository->model->create([
                'user_id'       => $userInfo->id,
                'block_user_id' => $request->get('block_user_id')
            ]);

            if($status)
            {
                $responseData = [
                    'message' => 'User blocked successfully'
                ];
                return $this->successResponse($responseData, 'User blocked successfully');
            }
            
        }
        
        return $this->setStatusCode(200)->failureResponse([
            'reason' => 'Invalid Input'
            ], 'Invalid Input');
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
                $responseData = $this->blockusersTransformer->transform($itemData);

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
                $responseData   = $this->blockusersTransformer->transform($itemData);

                return $this->successResponse($responseData, 'BlockUsers is Edited Successfully');
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
        if($request->has('block_user_id') )
        {
            $userInfo = $this->getAuthenticatedUser();

            if($userInfo->id == $request->get('block_user_id'))
            {
                return $this->setStatusCode(200)->failureResponse([
                    'reason' => 'You can not Unblock yourself!'
                ], 'You can not unblock yourself!');
            }

            $isExists = $this->repository->model->where([
                'user_id'       => $userInfo->id,
                'block_user_id' => $request->get('block_user_id')
            ])->first();

            if(isset($isExists))
            {
                $status = $isExists->delete();

                if($status)
                {
                    $responseData = [
                        'message' => 'User Unblocked successfully'
                    ];
                    return $this->successResponse($responseData, 'User Unblocked successfully');
                }
            }

            return $this->setStatusCode(200)->failureResponse([
                    'reason' => 'Already UnBlocked!'
                ], 'Already UnBlocked!');
        }
        
        return $this->setStatusCode(200)->failureResponse([
            'reason' => 'Invalid Input'
        ], 'Invalid Input');
    }
}