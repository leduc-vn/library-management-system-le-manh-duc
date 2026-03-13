<?php

namespace App\Http\Controllers\Reader;

use App\Http\Controllers\Controller;
use App\Models\Book;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Book $book)
    {
        $book->load(['publisher','authors','categories']);
        return view('reader.books.show', compact('book'));
    }
}
