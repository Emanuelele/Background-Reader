<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;


class LogController extends Controller
{
    /**
     * Visualizza la lista dei log, con filtro per categoria.
     *
     * @param string|null $category
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $category = null)
    {
        // Ottieni il termine di ricerca (se esiste)
        $search = $request->input('search', null);

        // Query di base
        $query = Log::query();

        // Filtra per categoria, se fornita
        if ($category) {
            $query->where('category', $category);
        }

        // Filtra per termine di ricerca nei metadati
        if ($search) {
            $query->where(function ($q) use ($search) {
                // Cerca nei metadati (spacchettati come JSON)
                $q->whereRaw("JSON_EXTRACT(metadata, '$.*') LIKE ?", ["%$search%"]);
            });
        }

        // Applica paginazione
        $logs = $query->paginate(20);

        // Ottieni tutte le colonne dai log
        $columns = collect($logs->items())->flatMap(function ($log) {
            $normalColumns = array_keys($log->getAttributes());
            $metadataColumns = $log->metadata ? array_keys($log->metadata) : [];

            $normalColumns = array_diff($normalColumns, ['created_at', 'updated_at']);

            return array_merge($normalColumns, $metadataColumns);
        })->unique();

        // Aggiungi manualmente 'created_at' alla fine
        $columns = $columns->merge(['created_at']);

        return view('logs', compact('logs', 'columns'));
    }
    
    

    /**
     * Salva un nuovo log.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'nullable|string|max:50',
            'metadata' => 'nullable|array',
        ]);

        $log = Log::create($validated);

        return response()->json($log, 201);
    }

    /**
     * Mostra un log specifico.
     */
    public function show(Log $log)
    {
        return response()->json($log);
    }
}

