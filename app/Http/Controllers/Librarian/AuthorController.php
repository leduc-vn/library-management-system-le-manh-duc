<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
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
        $authors = Author::orderBy('name')->paginate(20);
        return view('librarian.authors.index', compact('authors'));
    }

    public function create()
    {
        return view('librarian.authors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:authors,name',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
        ]);

        Author::create($data);

        return redirect()->route('librarian.authors.index')->with('success', 'Author created.');
    }

    public function edit(Author $author)
    {
        return view('librarian.authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:authors,name,' . $author->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
        ]);

        $author->update($data);

        return redirect()->route('librarian.authors.index')->with('success', 'Author updated.');
    }

    public function destroy(Author $author)
    {
        $author->delete();
        return redirect()->route('librarian.authors.index')->with('success', 'Author deleted.');
    }
}
