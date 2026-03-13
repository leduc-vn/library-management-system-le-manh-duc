<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // simple inline middleware to ensure only librarians access
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
        $categories = Category::orderBy('name')->paginate(20);
        return view('librarian.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('librarian.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($data);

        return redirect()->route('librarian.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('librarian.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($data);

        return redirect()->route('librarian.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('librarian.categories.index')->with('success', 'Category deleted.');
    }
}
