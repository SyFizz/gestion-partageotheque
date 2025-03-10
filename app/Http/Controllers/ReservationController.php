<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemStatus;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'item']);

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        } else {
            // Par défaut, afficher seulement les actives
            $query->where('is_active', true);
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

        $reservations = $query->orderBy('priority_order')->paginate(15);
        $users = User::all();

        return view('reservations.index', compact('reservations', 'users'));
    }

    public function create()
    {
        $users = User::whereHas('roles', function($query) {
            $query->whereHas('permissions', function($q) {
                $q->where('slug', 'reserve-item');
            });
        })->get();

        $items = Item::all(); // Tous les objets peuvent être réservés

        return view('reservations.create', compact('users', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'item_id' => 'required|exists:items,id',
            'reservation_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Calculer la date d'expiration (12 jours après la réservation)
        $expiryDate = Carbon::parse($request->reservation_date)->addDays(12);

        // Obtenir le dernier ordre de priorité pour cet objet
        $lastPriority = Reservation::where('item_id', $request->item_id)
            ->where('is_active', true)
            ->max('priority_order');

        $reservation = Reservation::create([
            'user_id' => $request->user_id,
            'item_id' => $request->item_id,
            'reservation_date' => $request->reservation_date,
            'expiry_date' => $expiryDate,
            'priority_order' => $lastPriority ? $lastPriority + 1 : 1,
            'is_active' => true,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        // Si c'est la première réservation et que l'objet est disponible, le marquer comme réservé
        if ($lastPriority === null && $reservation->item->status->slug === 'in-stock') {
            $reservedStatus = ItemStatus::where('slug', 'reserved')->first();
            $reservation->item->update(['item_status_id' => $reservedStatus->id]);
        }

        ActivityLogger::log('Création', 'Réservation', $reservation->id);

        return redirect()->route('reservations.index')
            ->with('success', 'Réservation créée avec succès.');
    }

    public function show(Reservation $reservation)
    {
        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $users = User::whereHas('roles', function($query) {
            $query->whereHas('permissions', function($q) {
                $q->where('slug', 'reserve-item');
            });
        })->get();

        return view('reservations.edit', compact('reservation', 'users'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reservation_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:reservation_date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $reservation->update([
            'user_id' => $request->user_id,
            'reservation_date' => $request->reservation_date,
            'expiry_date' => $request->expiry_date,
            'is_active' => $request->is_active ?? false,
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        ActivityLogger::log('Modification', 'Réservation', $reservation->id);

        return redirect()->route('reservations.index')
            ->with('success', 'Réservation mise à jour avec succès.');
    }

    public function reserve(Request $request, Item $item)
    {
        // Vérifier si l'objet est disponible pour la réservation
        if ($item->is_archived) {
            return back()->with('error', 'Cet objet est archivé et ne peut pas être réservé.');
        }

        $request->validate([
            'type' => 'required|in:next,period',
            'start_date' => 'required_if:type,period|date|nullable',
            'end_date' => 'required_if:type,period|date|after_or_equal:start_date|nullable',
        ]);

        $user = auth()->user();

        // Déterminer les dates de réservation
        if ($request->type === 'next') {
            // Réservation dès que l'objet est retourné
            $reservationDate = now();
            $expiryDate = now()->addDays(12);
        } else {
            // Réservation de date à date
            $reservationDate = $request->start_date;
            $expiryDate = $request->end_date;
        }

        // Obtenir le dernier ordre de priorité pour cet objet
        $lastPriority = Reservation::where('item_id', $item->id)
            ->where('is_active', true)
            ->max('priority_order');

        // Créer la réservation
        $reservation = Reservation::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'reservation_date' => $reservationDate,
            'expiry_date' => $expiryDate,
            'priority_order' => $lastPriority ? $lastPriority + 1 : 1,
            'is_active' => true,
            'notes' => $request->type === 'next' ? 'Réservation automatique dès retour' : 'Réservation pour période spécifique',
            'created_by' => $user->id,
        ]);

        // Journaliser l'activité
        ActivityLogger::log('create', 'Reservation', $reservation->id);

        return redirect()->route('items.show', $item)
            ->with('success', 'Objet réservé avec succès.');
    }

    public function destroy(Reservation $reservation)
    {
        // Si c'est la première réservation active et que l'objet est réservé
        if ($reservation->priority_order === 1 && $reservation->is_active && $reservation->item->status->slug === 'reserved') {
            // Vérifier s'il y a d'autres réservations actives pour cet objet
            $nextReservation = Reservation::where('item_id', $reservation->item_id)
                ->where('is_active', true)
                ->where('id', '!=', $reservation->id)
                ->orderBy('priority_order')
                ->first();

            if (!$nextReservation) {
                // Si pas d'autres réservations, remettre l'objet en stock
                $inStockStatus = ItemStatus::where('slug', 'in-stock')->first();
                $reservation->item->update(['item_status_id' => $inStockStatus->id]);
            }
        }

        $reservation->delete();
        ActivityLogger::log('Suppression', 'Réservation', $reservation->id);

        // Réorganiser les priorités
        $remainingReservations = Reservation::where('item_id', $reservation->item_id)
            ->where('is_active', true)
            ->orderBy('priority_order')
            ->get();

        $i = 1;
        foreach ($remainingReservations as $res) {
            $res->update(['priority_order' => $i++]);
        }

        return redirect()->route('reservations.index')
            ->with('success', 'Réservation supprimée avec succès.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'reservations' => 'required|array',
            'reservations.*.id' => 'required|exists:reservations,id',
            'reservations.*.priority_order' => 'required|integer|min:1',
        ]);

        foreach ($request->reservations as $res) {
            Reservation::where('id', $res['id'])->update([
                'priority_order' => $res['priority_order'],
                'updated_by' => Auth::id(),
            ]);
        }

        ActivityLogger::log('Modification de l\'ordre', 'Réservation');

        return response()->json(['success' => true]);
    }
}
