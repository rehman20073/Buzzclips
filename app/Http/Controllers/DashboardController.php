<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Redirect creators to their admin panel
        if (Auth::user()->user_type === 'creator') {
            return redirect('/creator');
        }
        
        // Regular users go to the feed
        return redirect()->route('feed.index');
    }
}
