<?php

namespace App\Http\Controllers\Api;

use App\Models\RunCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = RunCategory::where('is_active', true)
            ->orderBy('price')
            ->orderBy('id')
            ->get()
            ->map(fn (RunCategory $category) => $category -> toArrayPublic());

        return response()->json($categories, 200);
    }
}
