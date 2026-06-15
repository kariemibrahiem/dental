<?php

namespace App\Services\Admin;

use App\Models\Admin as ObjModel;
use App\Services\BaseService;
use Carbon\Carbon;
use Flasher\Laravel\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class AdminService extends BaseService
{
    protected string $folder = 'content/admin';
    protected string $route = 'admins';

    public function __construct(ObjModel $objModel , protected Role $roleModel)
    {
        parent::__construct($objModel);
    }

    public function index($request)
    {
        if ($request->ajax()) {
            $obj = $this->getDataTable();
            return DataTables::of($obj)

                ->editColumn("created_at" , function($obj){
                    return Carbon::parse($obj->created_at)->translatedFormat('Y-m-d');
                })
                ->addColumn("role" , function($obj){
                    return $obj->roles->first() ? $obj->roles->first()->name : '';
                })
                ->addColumn('action', function ($obj) {
                            $user = Auth::guard('admin')->user();
                            $buttons = '';

                            if ($user && $user->can($this->route . "_edit")) {
                                $buttons .= '
                                    <a href="' . route($this->route . '.edit', $obj->id) . '" 
                                    class="btn btn-sm btn-primary me-1" 
                                    title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>';
                            }

                            if ($user && $user->can($this->route . "_delete")) {
                                $buttons .= '
                                    <button type="button" 
                                            class="btn btn-sm btn-danger delete-confirm" 
                                            data-url="' . route($this->route . '.destroy', $obj->id) . '" 
                                            title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>';
                            }


                            return $buttons;
                        })
                       
                        ->editColumn('image', function ($obj) {
                            return $this->imageDatatable($obj->image);
                        })
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
        } else {
            return view($this->folder . '/index', [
                'createRoute' => route($this->route . '.create'),
                'bladeName' => "",
                'route' => $this->route,
            ]);
        }
    }

    public function create()
    {
        return view("{$this->folder}/partials/create", [
            'storeRoute' => route("{$this->route}.store"),
            "roles" => $this->roleModel->get(),
        ]);
    }

    public function store($data)
    {
        try {
            $data['code'] = rand(100000, 999999) . Str::random(5); 

            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $this->handleFile($data['image'], $this->route, 'image');
                $data['image'] = $path;
            }



            $roleId = $data['role_id'];
            unset($data['role_id']);
             
            $model = $this->createData(
                collect($data)->only($this->model->getFillable())->toArray()
            );

            $role = $this->roleModel->find($roleId);
            if ($role) {
                $model->assignRole($role->name);
            }


            if (request()->ajax()) {
                return response()->json(['status' => 200, 'message' => "تمت العملية بنجاح"]);
            }

            

            

            return redirect()->route("{$this->route}.index")->with('success', 'تمت العملية بنجاح');

        } catch (\Exception $e) {

            if (request()->ajax()) {
                return response()->json([
                    'status' => 500,
                    'message' => 'حدث خطأ ما.' . $e->getMessage(),
                    'خطأ' => $e->getMessage()
                ]);
            }

            return redirect()->back()->with('error', 'حدث خطأ ما.' . $e->getMessage());
        }
    }





    public function edit($obj)
    {
        return view("{$this->folder}/partials/edit", [
            'obj' => $obj,
            'updateRoute' => route("{$this->route}.update", $obj->id),
            "roles" => $this->roleModel->get(),
        ]);
    }

    public function update($data, $id)
    {
        $oldObj = $this->getById($id);

        try {
            $roleId = $data['role_id'] ?? null;
            unset($data['role_id']);

            if($data['password'] == null){
                $data = Arr::except($data, ['password']);
            } else {
                $data['password'] = $data['password'];
            }

            
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $this->handleFile($data['image'], $this->route, 'image');
                $data['image'] = $path;
            } elseif (!isset($data['image'])) {
                unset($data['image']);
            }



            $this->updateData($id, $data);

            if ($roleId) {
                $role = $this->roleModel->find($roleId);
                if ($role) {
                    $oldObj->syncRoles([$role->name]);
                }
            }

            if (request()->ajax()) {
                return response()->json(['status' => 200, 'message' => "تمت العملية بنجاح"]);
            }

            return redirect()->route("{$this->route}.index")->with('success', 'تمت العملية بنجاح');

        } catch (\Exception $e) {

            if (request()->ajax()) {
                return response()->json([
                    'status' => 500,
                    'message' => 'حدث خطأ ما.' . $e->getMessage(),
                    'خطأ' => $e->getMessage()
                ]);
            }

            return redirect()->back()->with('error', 'حدث خطأ ما. ' . $e->getMessage());
        }
    }
    public function profile(){
        $admin = Auth::guard('admin')->user();
        return view("{$this->folder}/profile", [
            'admin' => $admin,
            'updateRoute' => route("{$this->route}.update", $admin->id),
        ]);
    }
}
