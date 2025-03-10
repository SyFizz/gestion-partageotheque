<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('user');

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);
        $users = User::all();

        return view('payments.index', compact('payments', 'users'));
    }

    public function create()
    {
        $users = User::all();
        return view('payments.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:membership,caution,donation',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:payment_date',
            'notes' => 'nullable|string',
        ]);

        $payment = Payment::create([
            'user_id' => $request->user_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'expiry_date' => $request->expiry_date,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        ActivityLogger::log('Création', 'Paiement', $payment->id);

        return redirect()->route('payments.index')
            ->with('success', 'Paiement enregistré avec succès.');
    }

    public function show(Payment $payment)
    {
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $users = User::all();
        return view('payments.edit', compact('payment', 'users'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:membership,caution,donation',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:payment_date',
            'notes' => 'nullable|string',
        ]);

        $payment->update([
            'user_id' => $request->user_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'expiry_date' => $request->expiry_date,
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        ActivityLogger::log('Modification', 'Paiement', $payment->id);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Paiement mis à jour avec succès.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        ActivityLogger::log('Suppression', 'Paiement', $payment->id);

        return redirect()->route('payments.index')
            ->with('success', 'Paiement supprimé avec succès.');
    }
}
