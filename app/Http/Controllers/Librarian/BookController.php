<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookController extends Controller
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

    public function index()
    {
        $books = Book::with(['publisher','authors','categories'])->orderBy('title')->paginate(10);
        return view('librarian.books.index', compact('books'));
    }

    public function create()
    {
        $publishers = Publisher::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('librarian.books.create', compact('publishers','authors','categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'page_count' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'publisher_id' => 'nullable|exists:publishers,id',
            'total_quantity' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:10240',
            'authors' => 'nullable|array',
            'authors.*' => 'exists:authors,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $data['image_path'] = $path;
        }

        $book = Book::create([
            'title' => $data['title'],
            'page_count' => $data['page_count'] ?? null,
            'description' => $data['description'] ?? null,
            'publisher_id' => $data['publisher_id'] ?? null,
            'total_quantity' => $data['total_quantity'] ?? 0,
            'image_path' => $data['image_path'] ?? null,
        ]);

        if (!empty($data['authors'])) {
            $book->authors()->sync($data['authors']);
        }

        if (!empty($data['categories'])) {
            $book->categories()->sync($data['categories']);
        }

        return redirect()->route('librarian.books.index')->with('success', 'Book created.');
    }

    public function edit(Book $book)
    {
        $publishers = Publisher::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $book->load(['authors','categories','publisher']);
        return view('librarian.books.edit', compact('book','publishers','authors','categories'));
    }

    public function show(Book $book)
    {
        $book->load(['publisher','authors','categories']);
        return view('librarian.books.show', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'page_count' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'publisher_id' => 'nullable|exists:publishers,id',
            'total_quantity' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:10240',
            'authors' => 'nullable|array',
            'authors.*' => 'exists:authors,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $data['image_path'] = $path;
        }

        $book->update([
            'title' => $data['title'],
            'page_count' => $data['page_count'] ?? null,
            'description' => $data['description'] ?? null,
            'publisher_id' => $data['publisher_id'] ?? null,
            'total_quantity' => $data['total_quantity'] ?? 0,
            'image_path' => $data['image_path'] ?? $book->image_path,
        ]);

        $book->authors()->sync($data['authors'] ?? []);
        $book->categories()->sync($data['categories'] ?? []);

        return redirect()->route('librarian.books.index')->with('success', 'Book updated.');
    }

    public function destroy(Book $book)
    {
        $book->authors()->detach();
        $book->categories()->detach();
        $book->delete();
        return redirect()->route('librarian.books.index')->with('success', 'Book deleted.');
    }
}
