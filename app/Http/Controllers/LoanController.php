<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemStatus;
use App\Models\Loan;
use App\Models\User;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with(['user', 'item']);

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('return_date');
            } elseif ($request->status === 'returned') {
                $query->whereNotNull('return_date');
            }
        }

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('item', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('identifier', 'like', "%{$search}%");
            });
        }

        $loans = $query->orderBy('loan_date', 'desc')->paginate(15);
        $users = User::all();

        return view('loans.index', compact('loans', 'users'));
    }

    public function create()
    {
        $users = User::whereHas('roles', function($query) {
            $query->whereHas('permissions', function($q) {
                $q->where('slug', 'reserve-item');
            });
        })->get();

        $items = Item::whereHas('status', function($query) {
            $query->where('slug', 'in-stock');
        })->get();

        return view('loans.create', compact('users', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'item_id' => 'required|exists:items,id',
            'loan_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:loan_date',
            'notes' => 'nullable|string',
        ]);

        // Vérifier si l'objet est disponible
        $item = Item::findOrFail($request->item_id);
        if ($item->status->slug !== 'in-stock') {
            return back()->with('error', 'Cet objet n\'est pas disponible pour l\'emprunt.')
                ->withInput();
        }

        $loan = Loan::create([
            'user_id' => $request->user_id,
            'item_id' => $request->item_id,
            'loan_date' => $request->loan_date,
            'due_date' => $request->due_date,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        // Mettre à jour le statut de l'objet
        $onLoanStatus = ItemStatus::where('slug', 'on-loan')->first();
        $item->update(['item_status_id' => $onLoanStatus->id]);

        ActivityLogger::log('Enregistrement', 'Emprunt', $loan->id);

        return redirect()->route('loans.index')
            ->with('success', 'Emprunt enregistré avec succès.');
    }

    public function show(Loan $loan)
    {
        return view('loans.show', compact('loan'));
    }

    public function edit(Loan $loan)
    {
        $users = User::whereHas('roles', function($query) {
            $query->whereHas('permissions', function($q) {
                $q->where('slug', 'reserve-item');
            });
        })->get();

        return view('loans.edit', compact('loan', 'users'));
    }

    public function update(Request $request, Loan $loan)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'loan_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:loan_date',
            'notes' => 'nullable|string',
        ]);

        $loan->update([
            'user_id' => $request->user_id,
            'loan_date' => $request->loan_date,
            'due_date' => $request->due_date,
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        ActivityLogger::log('Modification', 'Emprunt', $loan->id);

        return redirect()->route('loans.index')
            ->with('success', 'Emprunt mis à jour avec succès.');
    }

    public function destroy(Loan $loan)
    {
        // Si le prêt n'est pas encore retourné, il faut d'abord remettre l'objet en stock
        if (!$loan->return_date) {
            $inStockStatus = ItemStatus::where('slug', 'in-stock')->first();
            $loan->item->update(['item_status_id' => $inStockStatus->id]);
        }

        $loan->delete();
        ActivityLogger::log('Suppression', 'Emprunt', $loan->id);

        return redirect()->route('loans.index')
            ->with('success', 'Emprunt supprimé avec succès.');
    }

    public function returnItem(Request $request, Loan $loan)
    {
        // Vérifier si le prêt est déjà retourné
        if ($loan->return_date) {
            return back()->with('error', 'Cet emprunt a déjà été retourné.');
        }

        $loan->update([
            'return_date' => Carbon::now(),
            'updated_by' => Auth::id(),
        ]);

        // Mettre à jour le statut de l'objet
        $inStockStatus = ItemStatus::where('slug', 'in-stock')->first();
        $loan->item->update(['item_status_id' => $inStockStatus->id]);

        // Vérifier s'il y a des réservations en attente
        $pendingReservation = $loan->item->reservations()->where('is_active', true)->orderBy('priority_order')->first();
        if ($pendingReservation) {
            // Changer le statut de l'objet à réservé
            $reservedStatus = ItemStatus::where('slug', 'reserved')->first();
            $loan->item->update(['item_status_id' => $reservedStatus->id]);

            // Envoyer une notification (à implémenter plus tard)
        }

        ActivityLogger::log('Retour', 'Emprunt', $loan->id);

        return redirect()->route('loans.index')
            ->with('success', 'Retour enregistré avec succès.');
    }

    public function extend(Request $request, Loan $loan)
    {
        $request->validate([
            'due_date' => 'required|date|after:' . $loan->due_date,
        ]);

        $loan->update([
            'due_date' => $request->due_date,
            'updated_by' => Auth::id(),
        ]);

        ActivityLogger::log('Prolongation', 'Emprunt', $loan->id);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Emprunt prolongé avec succès.');
    }
}
