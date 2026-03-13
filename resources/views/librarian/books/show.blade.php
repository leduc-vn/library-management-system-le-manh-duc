<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $book->title }}</h2>
            <div>
                <a href="{{ route('librarian.books.edit', $book) }}" class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded">Edit</a>
                <a href="{{ route('librarian.books.index') }}" class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-800 rounded">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white">
                    <div class="grid grid-cols-3 gap-6">
                        <div class="col-span-1">
                            @if($book->image_path)
                                <img src="{{ asset('storage/'.$book->image_path) }}" alt="{{ $book->title }}" class="w-full rounded border">
                            @else
                                <div class="w-full h-64 bg-gray-100 rounded flex items-center justify-center text-gray-400">No image</div>
                            @endif
                        </div>
                        <div class="col-span-2">
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold">Details</h3>
                                <div class="mt-2 text-sm text-gray-700">
                                    <p><strong>Title:</strong> {{ $book->title }}</p>
                                    <p><strong>Publisher:</strong> {{ $book->publisher?->name ?? '-' }}</p>
                                    <p><strong>Authors:</strong> {{ $book->authors->pluck('name')->join(', ') ?: '-' }}</p>
                                    <p><strong>Categories:</strong> {{ $book->categories->pluck('name')->join(', ') ?: '-' }}</p>
                                </div>
                                
                                @php
                                    $confirmed = \App\Models\BorrowSlip::where('book_id', $book->id)->where('status', 'confirmed')->count();
                                    $borrowing = \App\Models\BorrowSlip::where('book_id', $book->id)->where('status', 'borrowing')->count();
                                    $reserved = $confirmed + $borrowing;
                                    $available = max(0, ($book->total_quantity ?? 0) - $reserved);
                                @endphp
                                
                                <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <h4 class="font-semibold text-gray-700 mb-3">Tình trạng sách</h4>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Tổng số:</span>
                                            <span class="font-bold text-gray-800">{{ $book->total_quantity ?? 0 }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Còn lại:</span>
                                            <span class="font-bold {{ $available > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $available }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Chờ đến lấy:</span>
                                            <span class="font-semibold text-green-600">{{ $confirmed }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Đang mượn:</span>
                                            <span class="font-semibold text-blue-600">{{ $borrowing }}</span>
                                        </div>
                                    </div>
                                    @if($confirmed > 0)
                                        <div class="mt-3 pt-3 border-t border-gray-300">
                                            <a href="{{ route('librarian.borrow-slips.index', ['status' => 'confirmed']) }}" class="text-xs text-blue-600 hover:underline">
                                                → Xem danh sách chờ đến lấy
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold">Description</h3>
                                <div class="mt-2 text-sm text-gray-700">{!! nl2br(e($book->description ?: 'No description')) !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
