<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\FollowersTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Followers\EloquentFollowersRepository;
use App\Models\Access\User\User;

class APIFollowersController extends BaseApiController
{
    /**
     * Followers Transformer
     *
     * @var Object
     */
    protected $followersTransformer;

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
    protected $primaryKey = 'followersId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentFollowersRepository();
        $this->followersTransformer = new FollowersTransformer();
    }

    /**
     * Get Suggestions
     * 
     * @param Request $request
     * @return json
     */
    public function getSuggestions(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $keyword    = $request->has('keyword') ? $request->get('keyword') : '';
        $offset     = $request->has('offset') ? $request->get('offset') : 0;
        $perPage    = $request->has('per_page') ? $request->get('per_page') : 100;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $followerIds = $this->repository->model->where([
            'follower_id' => $userInfo->id
        ])->pluck('user_id')->toArray();

        $query = User::with(['followers', 'followings'])
        ->where('id', '!=', $userInfo->id)
        ->where('is_archive', 1)
        ->whereNotIn('id', $followerIds);

        if(isset($keyword) && strlen($keyword) > 1)
        {
            $query->where(function($q) use($keyword)
            {
                $q->where('name', 'LIKE', '%'. $keyword .'%')
                    ->orWhere('name', 'LIKE', '%'. $keyword .'%');
            }) ; 
        }
        
        
        $items = $query->offset($offset)
        ->limit($perPage)
        ->get();


        $items = $items->filter(function($item)
        {
            $item->followers = count($item->followers);
            $item->followings = count($item->followings);
            return $item;
        });

        $items = $items->sortByDesc('followings');

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->followersTransformer->followerSuggestionTransform($userInfo, $items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Follower Suggestions!'
            ], 'Unable to find Follower Suggestions');
    }

    /**
     * List of All Followers
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $offset     = $request->has('offset') ? $request->get('offset') : 0;
        $perPage    = $request->has('per_page') ? $request->get('per_page') : 100;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $followerIds      = $this->repository->model
        ->where('follower_id', $userInfo->id)
        ->pluck('user_id')->toArray();

        $items = User::with(['followers', 'followings'])
        ->whereIn('id', $followerIds)
        ->offset($offset)
        ->limit($perPage)
        ->get()
        ->filter(function($item)
        {
            $item->followers = count($item->followers);
            $item->followings = count($item->followings);
            return $item;
        });

        $items = $items->sortByDesc('followings');

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->followersTransformer->followerTransform($userInfo, $items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Followers!'
            ], 'No Followers Found !');
    }


    /**
     * Add Follow
     *
     * @param Request $request
     * @return string
     */
    public function addFollow(Request $request)
    {
        if($request->has('user_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();

            if($request->get('user_id') == $userInfo->id)
            {
               return $this->setStatusCode(400)->failureResponse([
                    'reason' => 'Invalid Follow Request !'
                ], 'Invalid Follow Request!'); 
            }

            $isExist    = $this->repository->model->where([
                'user_id'       => $request->get('user_id'),
                'follower_id'   => $userInfo->id
            ])->first();

            if(isset($isExist))
            {
                return $this->setStatusCode(400)->failureResponse([
                    'reason' => 'Already following !'
                ], 'Already following !');
            }

            $followData = [
                'user_id'       => $request->get('user_id'),
                'follower_id'   => $userInfo->id
            ];

            $model = $this->repository->model->create($followData);

            if($model)
            {
                $responseData = [
                    'message' => 'Followed Successfully !'
                ];
                return $this->successResponse($responseData, 'Followed Successfully');
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }

    /**
     * Add Follow
     *
     * @param Request $request
     * @return string
     */
    public function removeFollow(Request $request)
    {
        if($request->get('user_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $isExist    = $this->repository->model->where([
                'user_id'       => $request->get('user_id'),
                'follower_id'   => $userInfo->id
            ])->first();

            if(! $isExist)
            {
                return $this->setStatusCode(400)->failureResponse([
                    'reason' => 'Already unfollowed !'
                ], 'Already unfollowed!');
            }

            if($isExist->delete())
            {
                $responseData = [
                    'message' => 'UnFollowed Successfully !'
                ];
                return $this->successResponse($responseData, 'UnFollowed Successfully');
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }
    

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $model = $this->repository->create($request->all());

        if($model)
        {
            $responseData = $this->followersTransformer->transform($model);

            return $this->successResponse($responseData, 'Followers is Created Successfully');
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
                $responseData = $this->followersTransformer->transform($itemData);

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
                $responseData   = $this->followersTransformer->transform($itemData);

                return $this->successResponse($responseData, 'Followers is Edited Successfully');
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
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $status = $this->repository->destroy($itemId);

            if($status)
            {
                return $this->successResponse([
                    'success' => 'Followers Deleted'
                ], 'Followers is Deleted Successfully');
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }
}