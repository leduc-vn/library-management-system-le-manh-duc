<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $book->title }}</h2>
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-800 rounded">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 text-sm text-red-700">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="col-span-1">
                            @if($book->image_path)
                                <img src="{{ asset('storage/'.$book->image_path) }}" alt="{{ $book->title }}" class="w-full rounded border">
                            @else
                                <div class="w-full h-64 bg-gray-100 rounded flex items-center justify-center text-gray-400">No image</div>
                            @endif
                        </div>
                        <div class="col-span-2">
                            <h3 class="text-lg font-semibold mb-2">Details</h3>
                            <p><strong>Title:</strong> {{ $book->title }}</p>
                            <p><strong>Publisher:</strong> {{ $book->publisher?->name ?? '-' }}</p>
                            <p><strong>Authors:</strong> {{ $book->authors->pluck('name')->join(', ') ?: '-' }}</p>
                            <p><strong>Categories:</strong> {{ $book->categories->pluck('name')->join(', ') ?: '-' }}</p>
                            
                            @php
                                $reserved = \App\Models\BorrowSlip::where('book_id', $book->id)
                                    ->whereIn('status', ['confirmed', 'borrowing'])
                                    ->count();
                                $available = max(0, ($book->total_quantity ?? 0) - $reserved);
                            @endphp
                            
                            <div class="mt-3 p-3 bg-gray-50 rounded border">
                                <p class="font-semibold text-gray-700 mb-2">Tình trạng sách:</p>
                                <div class="grid grid-cols-3 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-600">Tổng số:</span>
                                        <span class="font-bold text-gray-800">{{ $book->total_quantity ?? 0 }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Đã mượn/đặt:</span>
                                        <span class="font-bold text-orange-600">{{ $reserved }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Còn lại:</span>
                                        <span class="font-bold {{ $available > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $available }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <p class="mt-4">{!! nl2br(e($book->description ?: 'No description')) !!}</p>

                            <div class="mt-6">
                                @php
                                    $userHas = false;
                                    $userStatus = null;
                                    if(auth()->check()) {
                                        $userBorrow = \App\Models\BorrowSlip::where('book_id', $book->id)
                                            ->where('user_id', auth()->id())
                                            ->whereIn('status', ['confirmed','borrowing'])
                                            ->first();
                                        if($userBorrow) {
                                            $userHas = true;
                                            $userStatus = $userBorrow->status;
                                        }
                                    }
                                @endphp

                                @if($userHas)
                                    @php
                                        $label = match($userStatus) {
                                            'confirmed' => 'Chờ đến lấy',
                                            'borrowing' => 'Đang mượn',
                                            default => 'Đã mượn',
                                        };
                                    @endphp
                                    <button type="button" disabled class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded">{{ $label }}</button>
                                @else
                                    <form method="POST" action="{{ route('borrow.store', $book) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded">Mượn sách</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
