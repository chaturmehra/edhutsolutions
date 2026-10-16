<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactEnquiry;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $hour = now()->hour;
        $enquiryTotal = ContactEnquiry::query()->count();
        $orderTotal = Order::query()->count();
        $courseTotal = Course::query()->count();

        return view('admin.dashboard', [
            'greeting' => $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening'),
            'counts' => [
                'courses' => $courseTotal,
                'active_courses' => Course::query()->active()->count(),
                'students' => User::query()->where('role', 'student')->count(),
                'orders' => $orderTotal,
                'pending_orders' => Order::query()->where('status', 'pending')->count(),
                'enquiries' => ContactEnquiry::query()->where('status', 'new')->count(),
                'enquiry_total' => $enquiryTotal,
            ],
            'title' => 'Dashboard',
        ]);
    }
}
