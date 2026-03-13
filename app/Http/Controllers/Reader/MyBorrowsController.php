<?php

namespace App\Http\Controllers\Reader;

use App\Http\Controllers\Controller;
use App\Models\BorrowSlip;
use Illuminate\Http\Request;

class MyBorrowsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = $request->query('status');
        
        $query = BorrowSlip::with(['book.authors', 'book.publisher'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        // Filter by status if provided
        if ($status && in_array($status, ['pending', 'confirmed', 'borrowing', 'returned', 'overdue'])) {
            $query->where('status', $status);
        }

        $borrows = $query->paginate(10);

        return view('reader.borrows.index', compact('borrows', 'status'));
    }
}
