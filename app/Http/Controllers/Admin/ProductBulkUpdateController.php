<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ProductBulkUpdateService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductBulkUpdateController extends Controller
{
    public function __construct(private ProductBulkUpdateService $bulkUpdateService) {}

    public function export()
    {
        $spreadsheet = $this->bulkUpdateService->export();
        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, 'kraftx-product-bulk-update-' . now()->format('Y-m-d') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'bulk_update_file' => ['required', 'file', 'mimes:xlsx', 'max:20480'],
            ], [
                'bulk_update_file.mimes' => 'Please upload an Excel .xlsx file downloaded from the product bulk update section.',
            ]);
        } catch (ValidationException $e) {
            return back()->withInput()->with('error', implode(' ', $e->validator->errors()->all()));
        }

        try {
            $result = $this->bulkUpdateService->import($request->file('bulk_update_file')->getRealPath());
            return view('admin.products.bulk-update-result', compact('result'));
        } catch (\Throwable $e) {
            report($e);
            return view('admin.products.bulk-update-result', [
                'result' => ['updated' => 0, 'skipped' => 0, 'errors' => [$e->getMessage()]],
            ]);
        }
    }
}
