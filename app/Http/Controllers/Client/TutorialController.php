<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Tutorial, Category};
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = (int) $request->input('category', 0);

        $query = Tutorial::with('category')->active();
        if ($categoryId > 0) $query->where('category_id', $categoryId);
        $tutorials = $query->latest()->get();

        $categories = Category::active()->get();

        return view('client.tutorials', compact('tutorials', 'categories', 'categoryId'));
    }
}
