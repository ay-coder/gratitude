<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\NotesTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Notes\EloquentNotesRepository;

class APINotesController extends BaseApiController
{
    /**
     * Notes Transformer
     *
     * @var Object
     */
    protected $notesTransformer;

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
    protected $primaryKey = 'notesId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentNotesRepository();
        $this->notesTransformer = new NotesTransformer();
    }

    /**
     * List of All Notes
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
        $items      = $this->repository->model->with('user')
        ->where('user_id', $userInfo->id)
        ->offset($offset)
        ->orderBy('id', 'DESC')
        ->limit($perPage)
        ->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->notesTransformer->transformAllNotes($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Notes!'
            ], 'No Notes Found !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $input      = $request->all();
        $input      = array_merge($input, ['user_id' => $userInfo->id]);
        $model      = $this->repository->create($input);

        if($model)
        {
            $responseData = $this->notesTransformer->singleNoteTransform($model);

            return $this->successResponse($responseData, 'Notes is Created Successfully');
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
                $responseData = $this->notesTransformer->transform($itemData);

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
        if($request->has('note_id') && $request->has('notes'))
        {
            $model = $this->repository->model->where('id', $request->get('note_id'))->first();

            if(isset($model))
            {
                $model->notes = $request->get('notes');

                if($model->save())
                {
                     $responseData = $this->notesTransformer->singleNoteTransform($model);
                    return $this->successResponse($responseData, 'Note Edited Successfully');
                }
            }
        }
        
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or No Note Found'
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
        if($request->has('note_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $model      = $this->repository->model->where([
                'id'        => $request->get('note_id'),
                'user_id'   => $userInfo->id
            ])->first();

            if(isset($model))
            {
                if($model->delete())
                {
                    return $this->successResponse([
                        'success' => 'Notes Deleted'
                    ], 'Notes is Deleted Successfully');   
                }
            }
        }
        
        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs or No Notes found !'
        ], 'Something went wrong !');
    }
}