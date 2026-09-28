<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{User, Category};
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $search     = trim((string) $request->input('q', ''));
        $categoryId = (int) $request->input('category', 0);

        $query = User::with(['craftsmanProfile.category'])
            ->where('role', 'craftsman')
            ->where('is_active', true)
            ->whereHas('craftsmanProfile', fn($q) => $q->where('is_approved', true));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($categoryId > 0) {
            $query->whereHas('craftsmanProfile', fn($q) => $q->where('category_id', $categoryId));
        }

        $craftsmen = $query->paginate(10)->withQueryString();
        $categories = Category::active()->get();

        return view('client.search-results', compact('craftsmen', 'categories', 'search', 'categoryId'));
    }
}
