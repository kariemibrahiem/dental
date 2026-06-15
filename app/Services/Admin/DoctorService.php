<?php

namespace App\Services\Admin;

use App\Models\Doctor as ObjModel;
use App\Models\Activity;
use App\Services\BaseService;
use App\Support\IdentityGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class DoctorService extends BaseService
{
    protected string $folder = 'content/doctor';
    protected string $route = 'doctors';

    public function __construct(ObjModel $objModel)
    {
        parent::__construct($objModel);
    }

    public function index($request)
    {
        if ($request->ajax()) {
            $obj = $this->getDataTable();
            return DataTables::of($obj)
                ->editColumn("created_at", function ($obj) {
                    return Carbon::parse($obj->created_at)->translatedFormat('Y-m-d');
                })
                ->addColumn('action', function ($obj) {
                    $user = Auth::guard('admin')->user();
                    $buttons = '';

                    if ($user && $user->can($this->route . "_delete")) {
                        $buttons .= '
                            <button type="button" 
                                    class="btn btn-sm btn-danger delete-confirm" 
                                    data-url="' . route($this->route . '.destroy', $obj->id) . '" 
                                    title="Delete">
                                <i class="fas fa-trash-alt"></i> Delete Doctor
                            </button>';
                    }

                    return $buttons;
                })
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
        } else {
            return view($this->folder . '/index', [
                'route' => $this->route,
            ]);
        }
    }

    public function store($data)
    {
        try {
            $data['email'] = $data['email'] ?? IdentityGenerator::uniqueEmail(ObjModel::class, $data['name'] ?? 'doctor', 'doctor');
            $data['password'] = $data['password'] ?? IdentityGenerator::temporaryPassword();

            $model = $this->createData(
                collect($data)->only($this->model->getFillable())->toArray()
            );

            // Log activity
            Activity::create([
                'description' => "Doctor {$model->name} was added",
                'type' => 'doctor_added'
            ]);

            if (request()->ajax()) {
                return response()->json(['status' => 200, 'message' => "Doctor added successfully"]);
            }

            return redirect()->route("{$this->route}.index")->with('success', 'Doctor added successfully');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Something went wrong: ' . $e->getMessage()
                ]);
            }

            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroyDoctor($id)
    {
        try {
            $doctor = $this->getById($id);
            if ($doctor) {
                $name = $doctor->name;
                $doctor->delete();

                // Log activity
                Activity::create([
                    'description' => "Doctor {$name} was deleted",
                    'type' => 'doctor_deleted'
                ]);

                return response()->json(['status' => 200, 'message' => "Doctor deleted successfully"]);
            }
            return response()->json(['status' => 405, 'message' => "Doctor not found"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }
}
