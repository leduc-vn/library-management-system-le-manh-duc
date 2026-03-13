<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Manage Books') }}</h2>
            <a href="{{ route('librarian.books.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">{{ __('New Book') }}</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Publisher</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Authors</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categories</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng số</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Còn lại</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($books as $book)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($book->image_path)
                                            <a href="{{ asset('storage/'.$book->image_path) }}" target="_blank" rel="noopener">
                                                <img src="{{ asset('storage/'.$book->image_path) }}" alt="{{ $book->title }}" class="h-16 w-12 object-cover rounded">
                                            </a>
                                        @else
                                            <div class="h-16 w-12 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-400">No image</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $book->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $book->publisher?->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $book->authors->pluck('name')->join(', ') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $book->categories->pluck('name')->join(', ') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $book->total_quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $reserved = \App\Models\BorrowSlip::where('book_id', $book->id)
                                                ->whereIn('status', ['confirmed', 'borrowing'])
                                                ->count();
                                            $available = max(0, ($book->total_quantity ?? 0) - $reserved);
                                        @endphp
                                        <span class="{{ $available > 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">{{ $available }}</span>
                                        @if($reserved > 0)
                                            <span class="text-xs text-gray-500 block">({{ $reserved }} đang mượn)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('librarian.books.show', $book) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="{{ route('librarian.books.edit', $book) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <form action="{{ route('librarian.books.destroy', $book) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this book?')">Delete</button></form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $books->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
