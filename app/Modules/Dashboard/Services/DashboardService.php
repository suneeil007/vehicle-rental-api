<?php

namespace App\Modules\Dashboard\Services;

use Carbon\Carbon;

use App\Modules\Booking\Models\Booking;
use App\Modules\Trip\Models\Trip;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Invoice\Models\Invoice;
use App\Modules\Payment\Models\Payment;

class DashboardService
{
    /**
     * Main dashboard summary.
     */
    public function getSummary(): array
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        return [

            // Vehicles
            'total_vehicles' => Vehicle::count(),

            'available_vehicles' => Vehicle::where(
                'status',
                'available'
            )->count(),

            'rented_vehicles' => Vehicle::where(
                'status',
                'rented'
            )->count(),

            // Trips
            'active_trips' => Trip::whereIn('status', [
                'picked_up',
                'on_trip',
            ])->count(),

            'completed_trips' => Trip::where(
                'status',
                'completed'
            )->count(),

            // Bookings
            'today_bookings' => Booking::whereDate(
                'created_at',
                $today
            )->count(),

            'pending_bookings' => Booking::where(
                'status',
                'pending'
            )->count(),

            // Revenue
            'today_revenue' => Payment::whereDate(
                'paid_at',
                $today
            )->sum('amount'),

            'monthly_revenue' => Payment::where(
                'paid_at',
                '>=',
                $monthStart
            )->sum('amount'),

            // Invoice Due
            'outstanding_due' => Invoice::sum('due_amount'),

            'unpaid_invoices' => Invoice::whereIn('status', [
                'issued',
                'partially_paid',
            ])->count(),
        ];
    }

    /**
     * Revenue chart for last N days.
     */
    public function getRevenueChart(int $days = 30): array
    {
        $start = Carbon::today()->subDays($days - 1);

        $payments = Payment::selectRaw('DATE(paid_at) as date, SUM(amount) as total')
            ->whereDate('paid_at', '>=', $start)
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $chart = [];

        for ($i = 0; $i < $days; $i++) {

            $date = $start->copy()->addDays($i)->toDateString();

            $chart[] = [
                'date' => $date,
                'amount' => (float) ($payments[$date] ?? 0),
            ];
        }

        return $chart;
    }

    /**
     * Top earning vehicles.
     */
    public function getVehicleUtilization(int $limit = 5): array
    {
        return Trip::selectRaw('
                vehicle_id,
                COUNT(*) as total_trips,
                SUM(total_amount) as revenue
            ')
            ->with('vehicle:id,name,slug')
            ->where('status', 'completed')
            ->groupBy('vehicle_id')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->map(function ($item) {

                return [
                    'vehicle' => [
                        'id' => $item->vehicle?->id,
                        'slug' => $item->vehicle?->slug,
                        'name' => $item->vehicle?->name,
                    ],
                    'total_trips' => (int) $item->total_trips,
                    'revenue' => (float) $item->revenue,
                ];
            })
            ->toArray();
    }
}