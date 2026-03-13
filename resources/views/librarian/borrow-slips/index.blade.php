<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý phiếu mượn') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-sm text-gray-600">Tất cả</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-green-50 rounded-lg shadow p-4">
                    <div class="text-sm text-green-700">Chờ đến lấy</div>
                    <div class="text-2xl font-bold text-green-800">{{ $stats['confirmed'] }}</div>
                </div>
                <div class="bg-blue-50 rounded-lg shadow p-4">
                    <div class="text-sm text-blue-700">Đang mượn</div>
                    <div class="text-2xl font-bold text-blue-800">{{ $stats['borrowing'] }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg shadow p-4">
                    <div class="text-sm text-gray-700">Đã trả</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['returned'] }}</div>
                </div>
                <div class="bg-red-50 rounded-lg shadow p-4">
                    <div class="text-sm text-red-700">Quá hạn</div>
                    <div class="text-2xl font-bold text-red-800">{{ $stats['overdue'] }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Search and Filter -->
                    <div class="mb-6">
                        <form method="GET" action="{{ route('librarian.borrow-slips.index') }}" class="flex flex-col md:flex-row gap-4">
                            <!-- Search -->
                            <div class="flex-grow">
                                <input type="text" 
                                       name="search" 
                                       value="{{ $search ?? '' }}" 
                                       placeholder="Tìm theo tên người dùng, email hoặc tên sách..." 
                                       class="w-full rounded border-gray-300 focus:ring focus:ring-blue-200 px-4 py-2">
                            </div>
                            
                            <!-- Status Filter (hidden input, controlled by buttons below) -->
                            <input type="hidden" name="status" value="{{ $status ?? '' }}">
                            
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Tìm kiếm
                            </button>
                        </form>

                        <!-- Status Filter Buttons -->
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('librarian.borrow-slips.index', ['search' => $search]) }}" 
                               class="px-4 py-2 rounded {{ !$status ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Tất cả
                            </a>
                            <a href="{{ route('librarian.borrow-slips.index', ['status' => 'confirmed', 'search' => $search]) }}" 
                               class="px-4 py-2 rounded {{ $status === 'confirmed' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Chờ đến lấy ({{ $stats['confirmed'] }})
                            </a>
                            <a href="{{ route('librarian.borrow-slips.index', ['status' => 'borrowing', 'search' => $search]) }}" 
                               class="px-4 py-2 rounded {{ $status === 'borrowing' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Đang mượn ({{ $stats['borrowing'] }})
                            </a>
                            <a href="{{ route('librarian.borrow-slips.index', ['status' => 'returned', 'search' => $search]) }}" 
                               class="px-4 py-2 rounded {{ $status === 'returned' ? 'bg-gray-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Đã trả ({{ $stats['returned'] }})
                            </a>
                            <a href="{{ route('librarian.borrow-slips.index', ['status' => 'overdue', 'search' => $search]) }}" 
                               class="px-4 py-2 rounded {{ $status === 'overdue' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Quá hạn ({{ $stats['overdue'] }})
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($borrows->count())
                        <!-- Table View for Desktop -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người mượn</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sách</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày mượn</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hạn trả</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($borrows as $borrow)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-4 whitespace-nowrap text-sm">#{{ $borrow->id }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $borrow->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $borrow->user->email }}</div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($borrow->book->title, 40) }}</div>
                                                <div class="text-sm text-gray-500">{{ $borrow->book->authors->pluck('name')->join(', ') }}</div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $borrow->borrow_date ? \Carbon\Carbon::parse($borrow->borrow_date)->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                                @if($borrow->due_date)
                                                    <span class="{{ $borrow->status === 'overdue' ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                                        {{ \Carbon\Carbon::parse($borrow->due_date)->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @php
                                                    $statusConfig = match($borrow->status) {
                                                        'confirmed' => ['label' => 'Chờ đến lấy', 'class' => 'bg-green-100 text-green-800'],
                                                        'borrowing' => ['label' => 'Đang mượn', 'class' => 'bg-blue-100 text-blue-800'],
                                                        'returned' => ['label' => 'Đã trả', 'class' => 'bg-gray-100 text-gray-800'],
                                                        'overdue' => ['label' => 'Quá hạn', 'class' => 'bg-red-100 text-red-800'],
                                                        default => ['label' => $borrow->status, 'class' => 'bg-gray-100 text-gray-800'],
                                                    };
                                                @endphp
                                                <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $statusConfig['class'] }}">
                                                    {{ $statusConfig['label'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('librarian.borrow-slips.show', $borrow) }}" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    Chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Card View for Mobile -->
                        <div class="md:hidden space-y-4">
                            @foreach($borrows as $borrow)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="text-sm text-gray-500">Phiếu #{{ $borrow->id }}</div>
                                        @php
                                            $statusConfig = match($borrow->status) {
                                                'confirmed' => ['label' => 'Chờ đến lấy', 'class' => 'bg-green-100 text-green-800'],
                                                'borrowing' => ['label' => 'Đang mượn', 'class' => 'bg-blue-100 text-blue-800'],
                                                'returned' => ['label' => 'Đã trả', 'class' => 'bg-gray-100 text-gray-800'],
                                                'overdue' => ['label' => 'Quá hạn', 'class' => 'bg-red-100 text-red-800'],
                                                default => ['label' => $borrow->status, 'class' => 'bg-gray-100 text-gray-800'],
                                            };
                                        @endphp
                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $statusConfig['class'] }}">
                                            {{ $statusConfig['label'] }}
                                        </span>
                                    </div>
                                    <div class="font-medium text-gray-900 mb-1">{{ $borrow->user->name }}</div>
                                    <div class="text-sm text-gray-600 mb-2">{{ $borrow->book->title }}</div>
                                    <div class="text-xs text-gray-500 space-y-1">
                                        @if($borrow->borrow_date)
                                            <div>Ngày mượn: {{ \Carbon\Carbon::parse($borrow->borrow_date)->format('d/m/Y') }}</div>
                                        @endif
                                        @if($borrow->due_date)
                                            <div>Hạn trả: {{ \Carbon\Carbon::parse($borrow->due_date)->format('d/m/Y') }}</div>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('librarian.borrow-slips.show', $borrow) }}" 
                                           class="text-sm text-blue-600 hover:text-blue-900">
                                            Xem chi tiết →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $borrows->appends(['status' => $status, 'search' => $search])->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-4">Không tìm thấy phiếu mượn nào.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
