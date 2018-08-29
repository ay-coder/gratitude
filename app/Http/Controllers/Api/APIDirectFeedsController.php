<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\DirectFeedsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\DirectFeeds\EloquentDirectFeedsRepository;

class APIDirectFeedsController extends BaseApiController
{
    /**
     * DirectFeeds Transformer
     *
     * @var Object
     */
    protected $directfeedsTransformer;

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
    protected $primaryKey = 'directfeedsId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentDirectFeedsRepository();
        $this->directfeedsTransformer = new DirectFeedsTransformer();
    }

    /**
     * List of All DirectFeeds
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
            $itemsOutput = $this->directfeedsTransformer->transformCollection($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find DirectFeeds!'
            ], 'No DirectFeeds Found !');
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
            $input = $request->all();

            if(isset($input['feed_images']) && count($input['feed_images']))
            {
                $feedImages = [];

                foreach($input['feed_images'] as $image)
                {
                    $imageName  = rand(11111, 99999) . '_feed.' . $image->getClientOriginalExtension();

                    $image->move(base_path() . '/public/uploads/feeds/', $imageName);

                    $feedImages[] = [
                        'feed_id' => $model->id,
                        'image'   => $imageName 
                    ];
                }

                if(count($feedImages))
                {
                    $model->feed_images()->insert($feedImages);
                }
            }

            $responseData = [
                'message' => 'Feed Created successfully'
            ];

            return $this->successResponse($responseData, 'Feed Created Successfully');
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
                $responseData = $this->directfeedsTransformer->transform($itemData);

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
                $responseData   = $this->directfeedsTransformer->transform($itemData);

                return $this->successResponse($responseData, 'DirectFeeds is Edited Successfully');
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
                    'success' => 'DirectFeeds Deleted'
                ], 'DirectFeeds is Deleted Successfully');
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }
}