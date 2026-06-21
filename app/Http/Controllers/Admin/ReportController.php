<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $reports = Report::with('patient')->latest()->get();
            return DataTables::of($reports)
                ->editColumn("created_at", function ($obj) {
                    return Carbon::parse($obj->created_at)->translatedFormat('Y-m-d');
                })
                ->addColumn("patient_name", function ($obj) {
                    return $obj->patient ? $obj->patient->name : '-';
                })
                ->editColumn("image_path", function ($obj) {
                    $src = asset('storage/' . $obj->image_path);
                    return '<img src="' . $src . '" 
                                 onclick="window.open(\'' . $src . '\')" 
                                 class="avatar avatar-md rounded" 
                                 style="cursor:pointer; object-fit: cover;" 
                                 width="80" height="80">';
                })
                ->addColumn('action', function ($obj) {
                    return '
                        <button type="button" 
                                class="btn btn-sm btn-danger delete-confirm" 
                                data-url="' . route('reports.destroy', $obj->id) . '" 
                                title="Delete">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>';
                })
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
        }

        return view('content.report.index');
    }

    public function export()
    {
        $fileName = 'medical-reports-' . now()->format('Y-m-d-His') . '.csv';
        $reports = Report::with(['patient', 'doctor'])->latest()->get();

        return response()->streamDownload(function () use ($reports) {
            $output = fopen('php://output', 'w');

            // UTF-8 BOM keeps Arabic text readable when opening the file in Excel.
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'ID',
                'Patient',
                'Doctor',
                'Report Title',
                'Description',
                'Scan Document',
                'Uploaded At',
            ]);

            foreach ($reports as $report) {
                fputcsv($output, [
                    $report->id,
                    $report->patient?->name ?? '-',
                    $report->doctor?->name ?? '-',
                    $report->title,
                    $report->description,
                    $report->image_path ? asset('storage/' . $report->image_path) : '-',
                    optional($report->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function destroy($id)
    {
        try {
            $report = Report::find($id);
            if ($report) {
                $report->delete();
                return response()->json(['status' => 200, 'message' => "Report deleted successfully"]);
            }
            return response()->json(['status' => 405, 'message' => "Report not found"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    public function destroySelected(Request $request)
    {
        try {
            $ids = $request->input('ids');
            if (is_array($ids) && count($ids)) {
                Report::whereIn('id', $ids)->delete();
                return response()->json(['status' => 200, 'message' => "Selected reports deleted successfully"]);
            }
            return response()->json(['status' => 405, 'message' => "No reports selected"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }
}
