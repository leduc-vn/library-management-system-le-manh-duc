<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;

class PublisherController extends Controller
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
        $publishers = Publisher::orderBy('name')->paginate(20);
        return view('librarian.publishers.index', compact('publishers'));
    }

    public function create()
    {
        return view('librarian.publishers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:publishers,name',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
        ]);

        Publisher::create($data);

        return redirect()->route('librarian.publishers.index')->with('success', 'Publisher created.');
    }

    public function edit(Publisher $publisher)
    {
        return view('librarian.publishers.edit', compact('publisher'));
    }

    public function update(Request $request, Publisher $publisher)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:publishers,name,' . $publisher->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
        ]);

        $publisher->update($data);

        return redirect()->route('librarian.publishers.index')->with('success', 'Publisher updated.');
    }

    public function destroy(Publisher $publisher)
    {
        $publisher->delete();
        return redirect()->route('librarian.publishers.index')->with('success', 'Publisher deleted.');
    }
}
