<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Si l'utilisateur est un simple membre (pas administrateur ni bénévole)
        if (!$user->hasPermission('create-loan') && !$user->hasPermission('edit-role-permissions')) {
            return $this->memberDashboard($user);
        }

        // Sinon, afficher le tableau de bord complet pour les bénévoles et administrateurs
        return $this->adminDashboard();
    }

    private function memberDashboard($user)
    {
        // Emprunts actifs de l'utilisateur
        $activeLoans = Loan::with('item')
            ->where('user_id', $user->id)
            ->whereNull('return_date')
            ->orderBy('due_date')
            ->get();

        // Réservations actives de l'utilisateur
        $activeReservations = Reservation::with('item')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('priority_order')
            ->get();

        // Historique des emprunts récents
        $recentLoans = Loan::with('item')
            ->where('user_id', $user->id)
            ->whereNotNull('return_date')
            ->orderBy('return_date', 'desc')
            ->limit(5)
            ->get();

        // Cotisation active
        $activeMembership = Payment::where('user_id', $user->id)
            ->where('type', 'membership')
            ->where(function($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('expiry_date', 'desc')
            ->first();

        // Total des cautions versées
        $totalCaution = Payment::where('user_id', $user->id)
            ->where('type', 'caution')
            ->sum('amount');

        // Date d'adhésion (premier paiement)
        $firstPayment = Payment::where('user_id', $user->id)
            ->orderBy('payment_date')
            ->first();

        // Statistiques personnelles
        $stats = [
            'active_loans_count' => $activeLoans->count(),
            'reservations_count' => $activeReservations->count(),
            'total_loans_count' => Loan::where('user_id', $user->id)->count(),
            'late_returns_count' => Loan::where('user_id', $user->id)
                ->whereNull('return_date')
                ->whereDate('due_date', '<', now())
                ->count()
        ];

        return view('dashboard.member', compact(
            'activeLoans',
            'activeReservations',
            'recentLoans',
            'activeMembership',
            'totalCaution',
            'firstPayment',
            'stats'
        ));
    }

    private function adminDashboard()
    {
        // Code actuel du tableau de bord pour administrateurs et bénévoles
        // Statistiques générales
        $stats = [
            'total_items' => Item::where('is_archived', false)->count(),
            'items_on_loan' => Item::whereHas('status', function($query) {
                $query->where('slug', 'on-loan');
            })->count(),
            'available_items' => Item::whereHas('status', function($query) {
                $query->where('slug', 'in-stock');
            })->count(),
            'pending_returns' => Loan::whereNull('return_date')
                ->whereDate('due_date', '<', now())
                ->count(),
            'active_reservations' => Reservation::where('is_active', true)->count(),
            'pending_validations' => User::where('is_validated', false)->count(),
        ];

        // Prochains retours (prochains 7 jours)
        $upcomingReturns = Loan::with(['user', 'item'])
            ->whereNull('return_date')
            ->whereDate('due_date', '>=', now())
            ->whereDate('due_date', '<=', now()->addDays(7))
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Emprunts en retard
        $overdueLoans = Loan::with(['user', 'item'])
            ->whereNull('return_date')
            ->whereDate('due_date', '<', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Catégories les plus populaires
        $popularCategories = Category::withCount(['items' => function($query) {
            $query->whereHas('loans', function($q) {
                $q->whereNull('return_date');
            });
        }])
            ->orderByDesc('items_count')
            ->limit(5)
            ->get();

        // Données pour graphique utilisateurs
        $userStats = User::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();

        // S'assurer que tous les mois sont inclus même s'il n'y a pas de données
        $userChartData = $this->fillMissingMonths($userStats, 6);

        // Données pour graphique emprunts
        $loanStats = Loan::select(
            DB::raw('DATE_FORMAT(loan_date, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )
            ->where('loan_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();

        $loanChartData = $this->fillMissingMonths($loanStats, 6);

        // Emprunts actifs par jour pour les 30 derniers jours
        $activeLoansData = [];
        for ($i = 0; $i <= 30; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = Loan::where('loan_date', '<=', $date)
                ->where(function($query) use ($date) {
                    $query->whereNull('return_date')
                        ->orWhere('return_date', '>', $date);
                })
                ->count();

            $activeLoansData[now()->subDays($i)->format('Y-m-d')] = $count;
        }

        $activeLoanChartData = [
            'labels' => array_keys(array_reverse($activeLoansData)),
            'data' => array_values(array_reverse($activeLoansData))
        ];

        return view('dashboard.admin', compact(
            'stats',
            'upcomingReturns',
            'overdueLoans',
            'popularCategories',
            'userChartData',
            'loanChartData',
            'activeLoanChartData'
        ));
    }

    private function fillMissingMonths($data, $monthCount)
    {
        $months = [];
        $result = ['labels' => [], 'data' => []];

        // Initialiser tous les mois à 0
        for ($i = $monthCount - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $months[$month] = 0;
        }

        // Remplir avec les données réelles
        foreach ($data as $item) {
            if (isset($months[$item['month']])) {
                $months[$item['month']] = $item['count'];
            }
        }

        // Formater pour Chart.js
        foreach ($months as $month => $count) {
            $result['labels'][] = Carbon::createFromFormat('Y-m', $month)->format('M Y');
            $result['data'][] = $count;
        }

        return $result;
    }
}
