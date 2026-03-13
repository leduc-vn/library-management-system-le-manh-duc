<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Chi tiết phiếu mượn #') }}{{ $borrowSlip->id }}
            </h2>
            <a href="{{ route('librarian.borrow-slips.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                ← Quay lại danh sách
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Status Badge -->
                    <div class="mb-6">
                        @php
                            $statusConfig = match($borrowSlip->status) {
                                'confirmed' => ['label' => 'Chờ đến lấy', 'class' => 'bg-green-100 text-green-800'],
                                'borrowing' => ['label' => 'Đang mượn', 'class' => 'bg-blue-100 text-blue-800'],
                                'returned' => ['label' => 'Đã trả', 'class' => 'bg-gray-100 text-gray-800'],
                                'overdue' => ['label' => 'Quá hạn', 'class' => 'bg-red-100 text-red-800'],
                                default => ['label' => $borrowSlip->status, 'class' => 'bg-gray-100 text-gray-800'],
                            };
                        @endphp
                        <span class="inline-block px-4 py-2 rounded-full text-sm font-medium {{ $statusConfig['class'] }}">
                            {{ $statusConfig['label'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- User Information -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4 text-gray-800">Thông tin người mượn</h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <span class="text-gray-600">Họ tên:</span>
                                    <a href="{{ route('librarian.users.show', $borrowSlip->user) }}" class="ml-2 font-medium text-blue-600 hover:underline">
                                        {{ $borrowSlip->user->name }}
                                    </a>
                                </div>
                                <div>
                                    <span class="text-gray-600">Email:</span>
                                    <span class="ml-2 font-medium">{{ $borrowSlip->user->email }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Book Information -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4 text-gray-800">Thông tin sách</h3>
                            <div class="flex gap-4">
                                @if($borrowSlip->book->image_path)
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('storage/'.$borrowSlip->book->image_path) }}" 
                                             alt="{{ $borrowSlip->book->title }}" 
                                             class="w-20 h-28 object-cover rounded">
                                    </div>
                                @endif
                                <div class="space-y-2 text-sm flex-grow">
                                    <div>
                                        <a href="{{ route('librarian.books.show', $borrowSlip->book) }}" class="font-medium text-blue-600 hover:underline">
                                            {{ $borrowSlip->book->title }}
                                        </a>
                                    </div>
                                    <div class="text-gray-600">
                                        Tác giả: {{ $borrowSlip->book->authors->pluck('name')->join(', ') ?: 'N/A' }}
                                    </div>
                                    @if($borrowSlip->book->publisher)
                                        <div class="text-gray-600">
                                            NXB: {{ $borrowSlip->book->publisher->name }}
                                        </div>
                                    @endif
                                    @if($borrowSlip->book->isbn)
                                        <div class="text-gray-600">
                                            ISBN: {{ $borrowSlip->book->isbn }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Borrow Details -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Thông tin mượn trả</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="bg-gray-50 p-4 rounded">
                                <div class="text-gray-600 mb-1">Ngày yêu cầu</div>
                                <div class="font-medium">{{ $borrowSlip->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            @if($borrowSlip->borrow_date)
                                <div class="bg-gray-50 p-4 rounded">
                                    <div class="text-gray-600 mb-1">Ngày mượn</div>
                                    <div class="font-medium">{{ \Carbon\Carbon::parse($borrowSlip->borrow_date)->format('d/m/Y') }}</div>
                                </div>
                            @endif
                            @if($borrowSlip->due_date)
                                <div class="bg-gray-50 p-4 rounded">
                                    <div class="text-gray-600 mb-1">Hạn trả</div>
                                    <div class="font-medium {{ $borrowSlip->status === 'overdue' ? 'text-red-600' : '' }}">
                                        {{ \Carbon\Carbon::parse($borrowSlip->due_date)->format('d/m/Y') }}
                                    </div>
                                </div>
                            @endif
                            @if($borrowSlip->return_date)
                                <div class="bg-gray-50 p-4 rounded">
                                    <div class="text-gray-600 mb-1">Ngày trả</div>
                                    <div class="font-medium">{{ \Carbon\Carbon::parse($borrowSlip->return_date)->format('d/m/Y') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Status Update Form -->
                    <div class="mt-6 pt-6 border-t">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Cập nhật trạng thái</h3>
                        
                        @if(session('error'))
                            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('librarian.borrow-slips.update-status', $borrowSlip) }}" class="flex flex-col sm:flex-row gap-4">
                            @csrf
                            @method('PATCH')
                            
                            <div class="flex-grow">
                                <select name="status" class="w-full rounded border-gray-300 focus:ring focus:ring-blue-200" {{ $borrowSlip->status === 'returned' ? 'disabled' : '' }}>
                                    @php
                                        $statusLabels = [
                                            'confirmed' => 'Chờ đến lấy',
                                            'borrowing' => 'Đang mượn',
                                            'returned' => 'Đã trả',
                                            'overdue' => 'Quá hạn',
                                        ];
                                    @endphp
                                    @foreach($availableStatuses as $status)
                                        <option value="{{ $status }}" {{ $borrowSlip->status === $status ? 'selected' : '' }}>
                                            {{ $statusLabels[$status] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <button type="submit" 
                                    class="px-6 py-2 rounded {{ $borrowSlip->status === 'returned' ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700' }} text-white"
                                    {{ $borrowSlip->status === 'returned' ? 'disabled' : '' }}>
                                Cập nhật
                            </button>
                        </form>
                        
                        <div class="mt-4 text-sm text-gray-600">
                            <p class="mb-2"><strong>Quy trình chuyển trạng thái:</strong></p>
                            <div class="bg-blue-50 p-4 rounded">
                                <div class="flex items-center gap-2 text-xs flex-wrap">
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded font-medium">Chờ đến lấy</span>
                                    <span class="text-gray-400">→</span>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded font-medium">Đang mượn</span>
                                    <span class="text-gray-400">→</span>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded font-medium">Đã trả</span>
                                </div>
                                <div class="mt-3 text-xs">
                                    <p class="text-red-600 font-medium">Lưu ý: Khi đang mượn, nếu quá hạn có thể chuyển sang "Quá hạn" trước khi trả.</p>
                                </div>
                            </div>
                            <ul class="list-disc list-inside space-y-1 text-xs mt-3">
                                <li><strong>Chờ đến lấy:</strong> Sách đã được đặt, chờ người dùng đến lấy sách tại thư viện</li>
                                <li><strong>Đang mượn:</strong> Người dùng đã nhận sách và đang mượn</li>
                                <li><strong>Quá hạn:</strong> Đã quá hạn trả sách (chỉ từ "Đang mượn")</li>
                                <li><strong>Đã trả:</strong> Sách đã được trả lại thư viện (trạng thái cuối)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
