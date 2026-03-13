<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\BorrowSlip;
use Illuminate\Http\Request;

class BorrowSlipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || ! method_exists($user, 'isLibrarian') || ! $user->isLibrarian()) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');
        
        $query = BorrowSlip::with(['user', 'book.authors', 'book.publisher'])
            ->orderBy('created_at', 'desc');

        // Filter by status if provided
        if ($status && in_array($status, ['confirmed', 'borrowing', 'returned', 'overdue'])) {
            $query->where('status', $status);
        }

        // Search by user name or book title
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('book', function($bookQuery) use ($search) {
                    $bookQuery->where('title', 'like', "%{$search}%");
                });
            });
        }

        $borrows = $query->paginate(15);

        // Get statistics
        $stats = [
            'total' => BorrowSlip::count(),
            'confirmed' => BorrowSlip::where('status', 'confirmed')->count(),
            'borrowing' => BorrowSlip::where('status', 'borrowing')->count(),
            'returned' => BorrowSlip::where('status', 'returned')->count(),
            'overdue' => BorrowSlip::where('status', 'overdue')->count(),
        ];

        return view('librarian.borrow-slips.index', compact('borrows', 'status', 'search', 'stats'));
    }

    public function show(BorrowSlip $borrowSlip)
    {
        $borrowSlip->load(['user', 'book.authors', 'book.publisher', 'book.categories']);
        $availableStatuses = $this->getAvailableStatuses($borrowSlip->status);
        return view('librarian.borrow-slips.show', compact('borrowSlip', 'availableStatuses'));
    }

    /**
     * Get available next statuses based on current status
     */
    private function getAvailableStatuses($currentStatus)
    {
        $statusFlow = [
            'confirmed' => ['confirmed', 'borrowing'],
            'borrowing' => ['borrowing', 'returned', 'overdue'],
            'returned' => ['returned'],
            'overdue' => ['overdue', 'returned'],
        ];

        return $statusFlow[$currentStatus] ?? ['confirmed'];
    }

    public function updateStatus(Request $request, BorrowSlip $borrowSlip)
    {
        $request->validate([
            'status' => 'required|in:confirmed,borrowing,returned,overdue',
        ]);

        $oldStatus = $borrowSlip->status;
        $newStatus = $request->status;

        // Check if status transition is valid
        $availableStatuses = $this->getAvailableStatuses($oldStatus);
        if (!in_array($newStatus, $availableStatuses)) {
            return redirect()->back()->with('error', 'Không thể chuyển trạng thái từ "' . $this->getStatusLabel($oldStatus) . '" sang "' . $this->getStatusLabel($newStatus) . '". Vui lòng tuân thủ quy trình.');
        }

        // Update status
        $borrowSlip->status = $newStatus;

        // Set return_date if status is 'returned'
        if ($newStatus === 'returned' && !$borrowSlip->return_date) {
            $borrowSlip->return_date = now();
        }

        // Set borrow_date if status changes to 'borrowing' and not set yet
        if ($newStatus === 'borrowing' && !$borrowSlip->borrow_date) {
            $borrowSlip->borrow_date = now();
        }

        $borrowSlip->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái phiếu mượn thành công.');
    }

    /**
     * Get status label in Vietnamese
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'confirmed' => 'Chờ đến lấy',
            'borrowing' => 'Đang mượn',
            'returned' => 'Đã trả',
            'overdue' => 'Quá hạn',
        ];

        return $labels[$status] ?? $status;
    }
}
