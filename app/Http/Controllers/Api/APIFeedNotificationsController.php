<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\FeedNotificationsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\FeedNotifications\EloquentFeedNotificationsRepository;

class APIFeedNotificationsController extends BaseApiController
{
    /**
     * FeedNotifications Transformer
     *
     * @var Object
     */
    protected $feednotificationsTransformer;

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
    protected $primaryKey = 'feednotificationsId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentFeedNotificationsRepository();
        $this->feednotificationsTransformer = new FeedNotificationsTransformer();
    }


    /**
     * Get All Notifications
     *
     * @param Request $request
     * @return json
     */
    public function getAllNotifications(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $offset     = $request->has('offset') ? $request->get('offset') : 0;
        $perPage    = $request->has('per_page') ? $request->get('per_page') : 100;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $items      = $this->repository->model->with([
            'user', 'from_user', 'feed'
        ])
        ->where('is_read', 0)
        ->where('user_id', $userInfo->id)
        ->offset($offset)
        ->limit($perPage)
        ->orderBy('id', 'desc')
        ->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->feednotificationsTransformer->transformAllNotifications($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Notifications!'
            ], 'No Notifications Found !');
    }

    /**
     * List of All FeedNotifications
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $paginate   = $request->get('paginate') ? $request->get('paginate') : false;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $items      = $paginate ? $this->repository->model->orderBy($orderBy, $order)->paginate($paginate)->items() : $this->repository->getAll($orderBy, $order);

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->feednotificationsTransformer->transformCollection($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find FeedNotifications!'
            ], 'No FeedNotifications Found !');
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
            $responseData = $this->feednotificationsTransformer->transform($model);

            return $this->successResponse($responseData, 'FeedNotifications is Created Successfully');
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
                $responseData = $this->feednotificationsTransformer->transform($itemData);

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
                $responseData   = $this->feednotificationsTransformer->transform($itemData);

                return $this->successResponse($responseData, 'FeedNotifications is Edited Successfully');
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
                    'success' => 'FeedNotifications Deleted'
                ], 'FeedNotifications is Deleted Successfully');
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Delete
     *
     * @param Request $request
     * @return string
     */
    public function clearAll(Request $request)
    {
        $userInfo = $this->getAuthenticatedUser();
        $status   = $this->repository->model->where([
            'user_id' => $userInfo->id
        ])->update([
            'is_read' => 1
        ]);

        if($status)
        {
            return $this->successResponse([
                    'success' => 'Cleared All Notifications'
                ], 'Cleared All Notifications');
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs or No Notifications Found!'
        ], 'Invalid Inputs or No Notifications Found!');
    }
}