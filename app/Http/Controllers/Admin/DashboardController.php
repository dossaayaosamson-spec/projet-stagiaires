<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Order;
use App\Models\ShortStory;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'books' => Book::count(),
            'authors' => Author::count(),
            'stories' => ShortStory::count(),
            'sales' => Order::whereIn('statut', ['confirmée', 'livrée'])->sum('total'),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $latestBooks = Book::with('author')->latest()->take(4)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'latestBooks'));
    }
}
