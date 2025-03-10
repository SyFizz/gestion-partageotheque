<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function search(Request $request)
    {
        // Si on recherche un ID spécifique
        if ($request->has('id')) {
            return Item::where('id', $request->id)
                ->select('id', 'name', 'identifier')
                ->get();
        }

        // Recherche par nom ou identifiant
        $query = $request->get('q', '');

        return Item::where('name', 'like', "%{$query}%")
            ->orWhere('identifier', 'like', "%{$query}%")
            ->where('is_archived', false)
            ->whereHas('status', function($q) {
                $q->where('slug', 'in-stock');
            })
            ->select('id', 'name', 'identifier')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->name = $item->name . ' (' . $item->identifier . ')';
                return $item;
            });
    }
}
