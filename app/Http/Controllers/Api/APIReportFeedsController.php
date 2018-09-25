<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\ReportFeedsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\ReportFeeds\EloquentReportFeedsRepository;

class APIReportFeedsController extends BaseApiController
{
    /**
     * ReportFeeds Transformer
     *
     * @var Object
     */
    protected $reportfeedsTransformer;

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
    protected $primaryKey = 'reportfeedsId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentReportFeedsRepository();
        $this->reportfeedsTransformer = new ReportFeedsTransformer();
    }

    /**
     * List of All ReportFeeds
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $paginate   = $request->get('paginate') ? $request->get('paginate') : false;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'ASC';
        $items      = $paginate ? $this->repository->model->orderBy($orderBy, $order)->paginate($paginate)->items() : $this->repository->getAll($orderBy, $order);

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->reportfeedsTransformer->transformCollection($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find ReportFeeds!'
            ], 'No ReportFeeds Found !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        if($request->has('feed_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $isExists   = $this->repository->model->where([
                'feed_id'       => $request->get('feed_id'),
                'user_id'       => $userInfo->id,
            ])->first();

            if(isset($isExists) && isset($isExists->id))
            {
                return $this->setStatusCode(400)->failureResponse([
                    'reason' => 'Feed already reported!'
                    ], 'Feed already reported!');
            }

            $model      = $this->repository->model->create([
                'feed_id'       => $request->get('feed_id'),
                'user_id'       => $userInfo->id,
                'description'   => $request->has('description') ? $request->get('description')  : ''
            ]);

            if($model)
            {
                $responseData = [
                    'message' => 'Feed Reported successfully!'
                ];

                return $this->successResponse($responseData, 'Feed Reported successfully!');
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Feed Id'
            ], 'Invalid Feed ');
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
                $responseData = $this->reportfeedsTransformer->transform($itemData);

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
                $responseData   = $this->reportfeedsTransformer->transform($itemData);

                return $this->successResponse($responseData, 'ReportFeeds is Edited Successfully');
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
                    'success' => 'ReportFeeds Deleted'
                ], 'ReportFeeds is Deleted Successfully');
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }
}