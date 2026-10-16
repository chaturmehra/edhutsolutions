<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Orders\OrderApproval;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $search = request()->string('q')->toString();
        $status = request()->string('status')->toString();

        return view('admin.orders.index', [
            'orders' => Order::query()
                ->with('user')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('number', 'like', '%'.$search.'%')
                            ->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%'.$search.'%'));
                    });
                })
                ->when(in_array($status, ['pending', 'approved', 'rejected'], true), fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load('user', 'items.course', 'approver');

        return view('admin.orders.show', ['order' => $order]);
    }

    public function approve(Request $request, Order $order, OrderApproval $approval): RedirectResponse
    {
        $data = $request->validate(['admin_note' => ['nullable', 'string', 'max:1000']]);
        $approval->approve($order, $request->user(), $data['admin_note'] ?? null);

        return back()->with('status', 'Order '.$order->number.' approved. The student can open the course.');
    }

    public function reject(Request $request, Order $order, OrderApproval $approval): RedirectResponse
    {
        $data = $request->validate(['admin_note' => ['nullable', 'string', 'max:1000']]);
        $approval->reject($order, $request->user(), $data['admin_note'] ?? null);

        return back()->with('status', 'Order '.$order->number.' rejected.');
    }
}
