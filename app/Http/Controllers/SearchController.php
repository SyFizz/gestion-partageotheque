<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return redirect()->back();
        }

        $items = Item::where('name', 'like', "%{$query}%")
            ->orWhere('identifier', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->where('is_archived', false)
            ->limit(10)
            ->get();

        $categories = Category::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->limit(5)
            ->get();

        $users = null;
        if (auth()->user()->hasPermission('view-user-details')) {
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->limit(5)
                ->get();
        }

        return view('search.results', compact('query', 'items', 'categories', 'users'));
    }
}
