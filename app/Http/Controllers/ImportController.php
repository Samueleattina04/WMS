<?php

namespace App\Http\Controllers;

use App\Imports\ProductsImport;
use App\Models\Import;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index()
    {
        $imports = Import::with('createdBy')->orderByDesc('created_at')->paginate(20);
        return view('imports.index', compact('imports'));
    }

    public function create()
    {
        $this->authorize('create', Import::class);
        return view('imports.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Import::class);
        $request->validate([
            'type' => 'required|in:products,movements,stock',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'type.required' => 'Seleziona il tipo di import.',
            'file.required' => 'Seleziona un file.',
            'file.mimes' => 'Il file deve essere in formato Excel (.xlsx, .xls) o CSV.',
            'file.max' => 'Il file non può superare i 10MB.',
        ]);

        $company = app('currentCompany');
        $filename = $request->file('file')->store('imports', 'local');

        $importRecord = Import::create([
            'company_id' => $company->id,
            'filename' => $filename,
            'type' => $request->type,
            'status' => 'processing',
            'created_by_user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        try {
            $importer = match ($request->type) {
                'products' => new ProductsImport($company, $importRecord),
                default => throw new \Exception('Tipo import non supportato.'),
            };

            Excel::import($importer, storage_path('app/' . $filename));

            $importRecord->update([
                'status' => 'completed',
                'rows_total' => $importer->getRowCount(),
                'rows_imported' => $importer->getImportedCount(),
                'rows_failed' => $importer->getFailedCount(),
                'error_log' => $importer->getErrors(),
            ]);
        } catch (\Exception $e) {
            $importRecord->update([
                'status' => 'failed',
                'error_log' => [['row' => 0, 'error' => $e->getMessage()]],
            ]);
        }

        return redirect()->route('imports.show', $importRecord)->with('success', 'Import completato.');
    }

    public function show(Import $import)
    {
        return view('imports.show', compact('import'));
    }

    public function downloadTemplate(string $type)
    {
        $templates = [
            'products' => resource_path('templates/import_prodotti.xlsx'),
            'movements' => resource_path('templates/import_movimenti.xlsx'),
        ];

        if (! isset($templates[$type])) {
            abort(404);
        }

        return response()->download($templates[$type]);
    }
}
