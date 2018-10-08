<?php namespace App\Http\Controllers\Backend\Categories;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Repositories\Categories\EloquentCategoriesRepository;
use Html;

/**
 * Class AdminCategoriesController
 */
class AdminCategoriesController extends Controller
{
    /**
     * Categories Repository
     *
     * @var object
     */
    public $repository;

    /**
     * Create Success Message
     *
     * @var string
     */
    protected $createSuccessMessage = "Categories Created Successfully!";

    /**
     * Edit Success Message
     *
     * @var string
     */
    protected $editSuccessMessage = "Categories Edited Successfully!";

    /**
     * Delete Success Message
     *
     * @var string
     */
    protected $deleteSuccessMessage = "Categories Deleted Successfully";

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository = new EloquentCategoriesRepository;
    }

    /**
     * Categories Listing
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->repository->setAdmin(true)->getModuleView('listView'))->with([
            'repository' => $this->repository
        ]);
    }

    /**
     * Categories View
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view($this->repository->setAdmin(true)->getModuleView('createView'))->with([
            'repository' => $this->repository
        ]);
    }

    /**
     * Categories Store
     *
     * @return \Illuminate\View\View
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $input = array_merge($input, ['icon' => 'default.png']);

        if($request->file('icon'))
        {
            $imageName  = rand(11111, 99999) . '_icon.' . $request->file('icon')->getClientOriginalExtension();
            $request->file('icon')->move(base_path() . '/public/uploads/categories/', $imageName);
            $input = array_merge($input, ['icon' => $imageName]);
        }
        
        $this->repository->create($input);

        return redirect()->route($this->repository->setAdmin(true)->getActionRoute('listRoute'))->withFlashSuccess($this->createSuccessMessage);
    }

    /**
     * Categories Edit
     *
     * @return \Illuminate\View\View
     */
    public function edit($id, Request $request)
    {
        $item = $this->repository->findOrThrowException($id);

        return view($this->repository->setAdmin(true)->getModuleView('editView'))->with([
            'item'          => $item,
            'repository'    => $this->repository
        ]);
    }

    /**
     * Categories Show
     *
     * @return \Illuminate\View\View
     */
    public function show($id, Request $request)
    {
        $item = $this->repository->findOrThrowException($id);

        return view($this->repository->setAdmin(true)->getModuleView('editView'))->with([
            'item'          => $item,
            'repository'    => $this->repository
        ]);
    }


    /**
     * Categories Update
     *
     * @return \Illuminate\View\View
     */
    public function update($id, Request $request)
    {   
        $input = $request->all();
        
        if($request->file('icon'))
        {
            $imageName  = rand(11111, 99999) . '_icon.' . $request->file('icon')->getClientOriginalExtension();
            $request->file('icon')->move(base_path() . '/public/uploads/categories/', $imageName);
            $input = array_merge($input, ['icon' => $imageName]);
        }
        $status = $this->repository->update($id, $input);

        return redirect()->route($this->repository->setAdmin(true)->getActionRoute('listRoute'))->withFlashSuccess($this->editSuccessMessage);
    }

    /**
     * Categories Destroy
     *
     * @return \Illuminate\View\View
     */
    public function destroy($id)
    {
        $status = $this->repository->destroy($id);

        return redirect()->route($this->repository->setAdmin(true)->getActionRoute('listRoute'))->withFlashSuccess($this->deleteSuccessMessage);
    }

    /**
     * Get Table Data
     *
     * @return json|mixed
     */
    public function getTableData()
    {
        return Datatables::of($this->repository->getForDataTable())
            ->escapeColumns(['id', 'sort'])
            ->addColumn('icon', function ($item) {
                return  Html::image('/uploads/categories/'.$item->icon, 'Icon', ['width' => 60, 'height' => 60]);
            })
            ->addColumn('actions', function ($item) {
                return $item->admin_action_buttons;
            })
            ->make(true);
    }
}