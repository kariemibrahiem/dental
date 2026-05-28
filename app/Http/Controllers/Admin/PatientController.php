<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\PatientService as ObjService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct(protected ObjService $objService) {}

    public function index(Request $request)
    {
        return $this->objService->index($request);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'doctor_id' => 'required|exists:doctors,id',
            'result' => 'required|in:Healthy,Cavity,Infection',
            'date' => 'nullable|date',
        ]);
        return $this->objService->store($data);
    }

    public function destroy($id)
    {
        return $this->objService->destroyPatient($id);
    }

    public function deleteSelected(Request $request)
    {
        return $this->objService->deleteSelected($request);
    }
}
