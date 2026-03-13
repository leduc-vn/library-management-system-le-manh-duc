<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reader Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Sidebar: categories for md+ -->
                        <aside class="hidden md:block">
                            <div class="bg-gray-50 border rounded p-4">
                                <h4 class="font-semibold mb-3">Thể loại</h4>
                                <ul class="space-y-2">
                                    <li>
                                        <a href="{{ route('dashboard', array_merge(request()->query(), ['category' => ''])) }}" class="block px-2 py-1 rounded {{ empty($category) ? 'bg-blue-100 text-blue-700' : 'hover:bg-gray-100' }}">Tất cả thể loại</a>
                                    </li>
                                    @foreach($categories as $cat)
                                        <li>
                                            <a href="{{ route('dashboard', array_merge(request()->query(), ['category' => $cat->id])) }}" class="block px-2 py-1 rounded {{ isset($category) && $category == $cat->id ? 'bg-blue-100 text-blue-700' : 'hover:bg-gray-100' }}">{{ $cat->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </aside>

                        <!-- Main content -->
                        <div class="md:col-span-3">
                            <!-- mobile category select + search -->
                            <form method="GET" action="{{ route('dashboard') }}" class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="sm:col-span-2 flex">
                                    <input type="text" name="q" value="{{ old('q', $search ?? '') }}" placeholder="Tìm theo tên sách..." class="w-full rounded-l border-gray-300 focus:ring focus:ring-blue-200 px-3 py-2" />
                                    <button type="submit" class="px-4 bg-blue-600 text-white rounded-r">Tìm</button>
                                </div>

                                <div class="sm:col-span-1">
                                    <select name="category" onchange="this.form.submit()" class="w-full rounded border-gray-300 px-3 py-2 md:hidden">
                                        <option value="">-- Tất cả thể loại --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" @if(isset($category) && $category == $cat->id) selected @endif>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>

                            @if($books->count())
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach($books as $book)
                                <div class="border rounded overflow-hidden bg-white">
                                    <a href="{{ route('books.show', $book) }}" class="block">
                                        <div class="h-48 bg-gray-50 flex items-center justify-center">
                                            @if($book->image_path)
                                                <img src="{{ asset('storage/'.$book->image_path) }}" alt="{{ $book->title }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="text-gray-400">No image</div>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="p-4">
                                        <h3 class="font-semibold text-sm"><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a></h3>
                                        <div class="text-xs text-gray-500">{{ $book->authors->pluck('name')->join(', ') }}</div>
                                        <div class="mt-2 text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($book->description, 120) }}</div>

                                        <div class="mt-3">
                                            @php
                                                // Tính cả sách đã xác nhận và đang mượn
                                                $currently = \App\Models\BorrowSlip::where('book_id', $book->id)
                                                    ->whereIn('status', ['confirmed', 'borrowing'])
                                                    ->count();
                                                $available = max(0, ($book->total_quantity ?? 0) - $currently);
                                            @endphp

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

                                            <div class="flex items-center justify-between mb-2">
                                                <div class="text-xs text-gray-600">
                                                    <span class="font-medium">Còn lại:</span> 
                                                    <span class="{{ $available > 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">{{ $available }}/{{ $book->total_quantity ?? 0 }}</span>
                                                </div>
                                                @if($currently > 0)
                                                    <div class="text-xs text-gray-500">
                                                        ({{ $currently }} đang mượn/đặt)
                                                    </div>
                                                @endif
                                            </div>

                                            @if($userHas)
                                                @php
                                                    $label = match($userStatus) {
                                                        'confirmed' => 'Chờ đến lấy',
                                                        'borrowing' => 'Đang mượn',
                                                        default => 'Đã mượn',
                                                    };
                                                @endphp
                                                <button type="button" disabled class="inline-flex items-center px-3 py-1 bg-gray-300 text-gray-700 rounded text-sm">{{ $label }}</button>
                                            @elseif($available > 0)
                                                <form method="POST" action="{{ route('borrow.store', $book) }}">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">Mượn</button>
                                                </form>
                                            @else
                                                <div class="text-sm text-red-600 font-medium">Hết sách</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">{{ $books->appends(request()->query())->links() }}</div>
                    @else
                        <div class="text-gray-600">Không tìm thấy sách.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
