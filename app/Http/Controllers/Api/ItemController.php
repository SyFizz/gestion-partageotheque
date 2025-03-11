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
            $item = Item::find($request->id);
            if ($item) {
                return response()->json([
                    'id' => $item->id,
                    'name' => $item->name . ' (' . $item->identifier . ')'
                ]);
            }
            return response()->json(null);
        }

        // Pour différencier si la recherche est pour une réservation ou un emprunt
        $isForReservation = $request->has('for_reservation') && $request->for_reservation;

        // Recherche par nom ou identifiant
        $query = $request->get('q', '');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $itemsQuery = Item::where(function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('identifier', 'like', "%{$query}%");
        })
            ->where('is_archived', false);

        // Si c'est pour un emprunt, on ne prend que les objets disponibles
        if (!$isForReservation) {
            $itemsQuery->whereHas('status', function($q) {
                $q->where('slug', 'in-stock');
            });
        }

        $items = $itemsQuery->select('id', 'name', 'identifier')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name . ' (' . $item->identifier . ')'
                ];
            });

        return response()->json($items);
    }
}
