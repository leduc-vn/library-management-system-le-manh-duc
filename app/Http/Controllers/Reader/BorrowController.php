<?php

namespace App\Http\Controllers\Reader;

use App\Http\Controllers\Controller;
use App\Models\BorrowSlip;
use App\Models\Book;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Book $book)
    {
        $user = $request->user();

        // prevent duplicate borrow requests by same user
        $exists = BorrowSlip::where('book_id', $book->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['confirmed','borrowing'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Bạn đã mượn hoặc đang có yêu cầu mượn quyển này.');
        }

        // Availability check: total_quantity - (confirmed + borrowing) > 0
        // Cả sách đã xác nhận và đang mượn đều cần tính vào số lượng đã được đặt
        $currentlyReserved = BorrowSlip::where('book_id', $book->id)
            ->whereIn('status', ['confirmed', 'borrowing'])
            ->count();
        $available = max(0, ($book->total_quantity ?? 0) - $currentlyReserved);

        if ($available <= 0) {
            return back()->with('error', 'Không còn sách để mượn hiện tại. Tất cả đã được đặt hoặc đang được mượn.');
        }

        $now = Carbon::now();
        $due = $now->copy()->addDays(14);

        BorrowSlip::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrow_date' => $now,
            'due_date' => $due,
            'status' => 'confirmed',
        ]);

        return redirect()->route('books.show', $book)->with('success', 'Đặt sách thành công! Vui lòng đến thư viện để lấy sách.');
    }
}
