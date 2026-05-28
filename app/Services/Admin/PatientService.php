<?php

namespace App\Services\Admin;

use App\Models\Patient as ObjModel;
use App\Models\Activity;
use App\Models\Doctor;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class PatientService extends BaseService
{
    protected string $folder = 'content/patient';
    protected string $route = 'patients';

    public function __construct(ObjModel $objModel)
    {
        parent::__construct($objModel);
    }

    public function index($request)
    {
        if ($request->ajax()) {
            $obj = $this->model->with('doctor')->latest()->get();
            return DataTables::of($obj)
                ->editColumn("date", function ($obj) {
                    return Carbon::parse($obj->date)->format('Y-m-d');
                })
                ->addColumn("doctor_name", function ($obj) {
                    return $obj->doctor ? $obj->doctor->name : '-';
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
                                <i class="fas fa-trash-alt"></i> Delete Patient
                            </button>';
                    }

                    return $buttons;
                })
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
        } else {
            $doctors = Doctor::orderBy('name')->get();
            return view($this->folder . '/index', [
                'route' => $this->route,
                'doctors' => $doctors,
            ]);
        }
    }

    public function store($data)
    {
        try {
            if (empty($data['date'])) {
                $data['date'] = Carbon::today()->toDateString();
            }

            $model = $this->createData(
                collect($data)->only($this->model->getFillable())->toArray()
            );

            // Log activity
            Activity::create([
                'description' => "Patient {$model->name} was added",
                'type' => 'patient_added'
            ]);

            if (request()->ajax()) {
                return response()->json(['status' => 200, 'message' => "Patient added successfully"]);
            }

            return redirect()->route("{$this->route}.index")->with('success', 'Patient added successfully');
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

    public function destroyPatient($id)
    {
        try {
            $patient = $this->getById($id);
            if ($patient) {
                $name = $patient->name;
                $patient->delete();

                // Log activity
                Activity::create([
                    'description' => "Patient {$name} was deleted",
                    'type' => 'patient_deleted'
                ]);

                return response()->json(['status' => 200, 'message' => "Patient deleted successfully"]);
            }
            return response()->json(['status' => 405, 'message' => "Patient not found"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }
}
