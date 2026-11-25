<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegistrantController extends Controller
{
    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('perPage', 10)));
        $search = $request->query('search');

        $registrants = Registrant::with('category')
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->orderByDesc('registered_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'data' => $registrants->getCollection()->map->toArrayAdmin(),
            'meta' => [
                'page'      => $registrants->currentPage(),
                'per_page'  => $registrants->perPage(),
                'total'     => $registrants->total(),
                'last_page' => $registrants->lastPage(),
            ],
        ]);
    }

    public function updateGoodie(Request $request, $id)
    {
        $registrant = Registrant::find($id);

        if (! $registrant) {
            return response()->json(['error' => 'Registrant not found'], 404);
        }

        $registrant->update($request->validate([
            'goodie_status' => ['required', Rule::in(['pending', 'collected'])],
        ]));

        return response()->json($registrant->load('category')->toArrayAdmin());
    }
}