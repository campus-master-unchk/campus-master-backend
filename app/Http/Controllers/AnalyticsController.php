<?php

namespace App\Http\Controllers;

use App\Core\Domain\Entities\{User, Student, Teacher, Module, Department, Submission, Announcement, Course};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function getStatisticsDashboard()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'total_modules'  => Module::count(),
            'total_courses' => Course::count(),
            //recent users
            'recent_users' => User::orderBy('created_at', 'desc')->where('user_type', 'student')->orWhere('user_type', 'teacher')->take(10)->get(),
            //recent announcements
            'recent_announcements' => Announcement::orderBy('created_at', 'desc')->take(10)->get(),
        ];

        return response()->json($stats);
    }

    public function getDashboardData()
    {
        // --- 1. Évolution des inscriptions (6 derniers mois) ---
        // Format pour AreaChart : [{month: 'Jan', students: 10, teachers: 2}, ...]
        $months = collect(range(5, 0))->map(function($i) {
            return Carbon::now()->subMonths($i)->format('M');
        });

        $registrations = User::select(
                DB::raw("DATE_FORMAT(created_at, '%b') as month"),
                DB::raw("SUM(CASE WHEN user_type = 'student' THEN 1 ELSE 0 END) as students"),
                DB::raw("SUM(CASE WHEN user_type = 'teacher' THEN 1 ELSE 0 END) as teachers")
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $evolutionChart = $months->map(function($month) use ($registrations) {
            return [
                'month' => $month,
                'students' => $registrations->get($month)->students ?? 0,
                'teachers' => $registrations->get($month)->teachers ?? 0,
            ];
        });

        // --- 2. Répartition par Département ---
        // Format pour PieChart : [{name: 'Informatique', value: 40, fill: '#8884d8'}, ...]
        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
        $distributionChart = Department::withCount('specialities')
            ->get()
            ->map(function ($dept, $index) use ($colors) {
                return [
                    'name' => $dept->name,
                    'value' => $dept->specialities_count,
                    'fill' => $colors[$index % count($colors)]
                ];
            });

        // --- 3. Performance des Modules (Top 5 en nombre de devoirs) ---
        // Format pour BarChart : [{module: 'Algèbre', submissions: 120}, ...]
        $modulePerformance = Module::withCount('submissions')
            ->orderBy('submissions_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($module) {
                return [
                    'module' => $module->name,
                    'submissions' => $module->submissions_count
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'evolution' => $evolutionChart,      // Pour AreaChart
                'distribution' => $distributionChart, // Pour PieChart
                'performance' => $modulePerformance,  // Pour BarChart
                'kpis' => [
                    'total_users' => User::count(),
                    'active_modules' => Module::count(),
                    'new_submissions' => Submission::where('created_at', '>=', Carbon::now()->subDays(7))->count()
                ]
            ]
        ]);
    }
}
