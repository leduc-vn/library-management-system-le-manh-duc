<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sách đã mượn') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Filter by status -->
                    <div class="mb-6 flex flex-wrap gap-2">
                        <a href="{{ route('my-borrows.index') }}" 
                           class="px-4 py-2 rounded {{ !$status ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            Tất cả
                        </a>
                        <a href="{{ route('my-borrows.index', ['status' => 'confirmed']) }}" 
                           class="px-4 py-2 rounded {{ $status === 'confirmed' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            Chờ đến lấy
                        </a>
                        <a href="{{ route('my-borrows.index', ['status' => 'borrowing']) }}" 
                           class="px-4 py-2 rounded {{ $status === 'borrowing' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            Đang mượn
                        </a>
                        <a href="{{ route('my-borrows.index', ['status' => 'returned']) }}" 
                           class="px-4 py-2 rounded {{ $status === 'returned' ? 'bg-gray-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            Đã trả
                        </a>
                        <a href="{{ route('my-borrows.index', ['status' => 'overdue']) }}" 
                           class="px-4 py-2 rounded {{ $status === 'overdue' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            Quá hạn
                        </a>
                    </div>

                    @if($borrows->count())
                        <!-- List view -->
                        <div class="space-y-4">
                            @foreach($borrows as $borrow)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex flex-col md:flex-row gap-4">
                                        <!-- Book image -->
                                        <div class="flex-shrink-0">
                                            <div class="w-24 h-32 bg-gray-100 rounded overflow-hidden">
                                                @if($borrow->book->image_path)
                                                    <img src="{{ asset('storage/'.$borrow->book->image_path) }}" 
                                                         alt="{{ $borrow->book->title }}" 
                                                         class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">
                                                        No image
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Book info -->
                                        <div class="flex-grow">
                                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">
                                                <div class="flex-grow">
                                                    <h3 class="font-semibold text-lg">
                                                        <a href="{{ route('books.show', $borrow->book) }}" 
                                                           class="text-blue-600 hover:underline">
                                                            {{ $borrow->book->title }}
                                                        </a>
                                                    </h3>
                                                    <div class="text-sm text-gray-600 mt-1">
                                                        Tác giả: {{ $borrow->book->authors->pluck('name')->join(', ') ?: 'N/A' }}
                                                    </div>
                                                    @if($borrow->book->publisher)
                                                        <div class="text-sm text-gray-600">
                                                            NXB: {{ $borrow->book->publisher->name }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Status badge -->
                                                <div>
                                                    @php
                                                        $statusConfig = match($borrow->status) {
                                                            'confirmed' => ['label' => 'Chờ đến lấy', 'class' => 'bg-green-100 text-green-800'],
                                                            'borrowing' => ['label' => 'Đang mượn', 'class' => 'bg-blue-100 text-blue-800'],
                                                            'returned' => ['label' => 'Đã trả', 'class' => 'bg-gray-100 text-gray-800'],
                                                            'overdue' => ['label' => 'Quá hạn', 'class' => 'bg-red-100 text-red-800'],
                                                            default => ['label' => $borrow->status, 'class' => 'bg-gray-100 text-gray-800'],
                                                        };
                                                    @endphp
                                                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $statusConfig['class'] }}">
                                                        {{ $statusConfig['label'] }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Dates info -->
                                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 text-sm">
                                                @if($borrow->borrow_date)
                                                    <div>
                                                        <span class="text-gray-600">Ngày mượn:</span>
                                                        <span class="font-medium">{{ \Carbon\Carbon::parse($borrow->borrow_date)->format('d/m/Y') }}</span>
                                                    </div>
                                                @endif
                                                @if($borrow->due_date)
                                                    <div>
                                                        <span class="text-gray-600">Hạn trả:</span>
                                                        <span class="font-medium {{ $borrow->status === 'overdue' ? 'text-red-600' : '' }}">
                                                            {{ \Carbon\Carbon::parse($borrow->due_date)->format('d/m/Y') }}
                                                        </span>
                                                    </div>
                                                @endif
                                                @if($borrow->return_date)
                                                    <div>
                                                        <span class="text-gray-600">Ngày trả:</span>
                                                        <span class="font-medium">{{ \Carbon\Carbon::parse($borrow->return_date)->format('d/m/Y') }}</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="text-gray-600">Yêu cầu lúc:</span>
                                                    <span class="font-medium">{{ $borrow->created_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $borrows->appends(['status' => $status])->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <p class="mt-4">
                                @if($status)
                                    Không có sách nào với trạng thái này.
                                @else
                                    Bạn chưa mượn sách nào.
                                @endif
                            </p>
                            <a href="{{ route('dashboard') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Khám phá sách
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
