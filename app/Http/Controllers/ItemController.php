<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemStatus;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{

    public function index(Request $request)
    {
        $query = Item::with(['category', 'status']);

        // Gestion de l'affichage des objets archivés
        if ($request->filled('show_archived') && $request->show_archived == 1) {
            $query->where('is_archived', true);
        } else {
            $query->where('is_archived', false);
        }

        // Filtres restants
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('item_status_id', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('identifier', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate(15);
        $categories = Category::all();
        $statuses = ItemStatus::all();

        return view('items.index', compact('items', 'categories', 'statuses'));
    }

    public function create()
    {
        $categories = Category::all();
        $statuses = ItemStatus::all();
        return view('items.create', compact('categories', 'statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|unique:items',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'item_status_id' => 'required|exists:item_statuses,id',
            'caution_amount' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $data['image_path'] = $path;
        }

        $item = Item::create($data);
        ActivityLogger::log('Création', 'Objet', $item->id);

        return redirect()->route('items.index')
            ->with('success', 'Objet créé avec succès.');
    }

    public function show(Item $item)
    {
        $item->load(['category', 'status']);

        // Si l'utilisateur a le droit de voir l'historique
        if (auth()->user()->hasPermission('view-item-history')) {
            $item->load(['loans' => function ($query) {
                $query->orderBy('loan_date', 'desc')->limit(10);
            }, 'loans.user', 'reservations' => function ($query) {
                $query->where('is_active', true)->orderBy('priority_order');
            }, 'reservations.user']);
        }

        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        $statuses = ItemStatus::all();
        return view('items.edit', compact('item', 'categories', 'statuses'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|unique:items,identifier,' . $item->id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'item_status_id' => 'required|exists:item_statuses,id',
            'caution_amount' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }

            $path = $request->file('image')->store('items', 'public');
            $data['image_path'] = $path;
        }

        $item->update($data);
        ActivityLogger::log('Modification', 'Objet', $item->id);

        return redirect()->route('items.show', $item)
            ->with('success', 'Objet mis à jour avec succès.');
    }

    public function destroy(Item $item)
    {
        // Vérifier s'il y a des emprunts en cours
        if ($item->activeLoans()->count() > 0) {
            return back()->with('error', 'Impossible d\'archiver cet objet car il est actuellement emprunté.');
        }

        // Archiver plutôt que supprimer
        $item->update([
            'is_archived' => true,
            'item_status_id' => ItemStatus::where('slug', 'temporarily-unavailable')->first()->id
        ]);

        ActivityLogger::log('Archivage', 'Objet', $item->id);

        return redirect()->route('items.index')
            ->with('success', 'Objet archivé avec succès.');
    }

    public function duplicate(Item $item)
    {
        $newItem = $item->replicate();
        $newItem->identifier = $item->identifier . '-copy-' . time();
        $newItem->save();

        ActivityLogger::log('Copie', 'Objet', $newItem->id, 'Copié depuis l\'objet #' . $item->id);

        return redirect()->route('items.edit', $newItem)
            ->with('success', 'Objet dupliqué avec succès. Vous pouvez maintenant le modifier.');
    }
}
