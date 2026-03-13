<?php

use App\Http\Controllers\ProfileController;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    // librarians keep their dashboard
    if ($user && method_exists($user, 'isLibrarian') && $user->isLibrarian()) {
        return view('librarian.dashboard');
    }

    // Reader dashboard shows books with filter/search
    $search = request()->query('q');
    $category = request()->query('category');

    $categories = Category::orderBy('name')->get();

    $booksQuery = Book::with(['authors', 'publisher', 'categories']);

    if ($category) {
        $booksQuery->whereHas('categories', function ($q) use ($category) {
            $q->where('id', $category);
        });
    }

    if ($search) {
        $booksQuery->where('title', 'like', "%{$search}%");
    }

    $books = $booksQuery->orderBy('title')->paginate(10);

    return view('reader.dashboard', compact('books', 'categories', 'search', 'category'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Reader book routes
Route::middleware(['auth','verified'])->group(function () {
    Route::get('books/{book}', [App\Http\Controllers\Reader\BookController::class, 'show'])->name('books.show');
    Route::post('borrow/{book}', [App\Http\Controllers\Reader\BorrowController::class, 'store'])->name('borrow.store');
    Route::get('my-borrows', [App\Http\Controllers\Reader\MyBorrowsController::class, 'index'])->name('my-borrows.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Librarian management routes (categories)
Route::middleware(['auth','verified'])->prefix('librarian')->name('librarian.')->group(function () {
    Route::resource('categories', App\Http\Controllers\Librarian\CategoryController::class)->except(['show']);
    Route::resource('publishers', App\Http\Controllers\Librarian\PublisherController::class)->except(['show']);
    Route::resource('authors', App\Http\Controllers\Librarian\AuthorController::class)->except(['show']);
    Route::resource('books', App\Http\Controllers\Librarian\BookController::class)->except(['show']);
    Route::get('books/{book}', [App\Http\Controllers\Librarian\BookController::class, 'show'])->name('books.show');
    // Librarian user listing and detail
    Route::get('users', [App\Http\Controllers\Librarian\UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [App\Http\Controllers\Librarian\UserController::class, 'show'])->name('users.show');
    // Librarian borrow slips management
    Route::get('borrow-slips', [App\Http\Controllers\Librarian\BorrowSlipController::class, 'index'])->name('borrow-slips.index');
    Route::get('borrow-slips/{borrowSlip}', [App\Http\Controllers\Librarian\BorrowSlipController::class, 'show'])->name('borrow-slips.show');
    Route::patch('borrow-slips/{borrowSlip}/status', [App\Http\Controllers\Librarian\BorrowSlipController::class, 'updateStatus'])->name('borrow-slips.update-status');
});

require __DIR__.'/auth.php';
