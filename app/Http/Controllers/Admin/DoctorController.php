<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DoctorService as ObjService;
use Illuminate\Http\Request;

class DoctorController extends Controller
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
        ]);
        return $this->objService->store($data);
    }

    public function destroy($id)
    {
        return $this->objService->destroyDoctor($id);
    }

    public function deleteSelected(Request $request)
    {
        return $this->objService->deleteSelected($request);
    }
}
