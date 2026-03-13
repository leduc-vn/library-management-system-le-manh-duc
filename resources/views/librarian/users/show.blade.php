<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $user->name }}</h2>
            <div>
                <a href="{{ route('librarian.users.index') }}" class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-800 rounded">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white">
                    <div class="grid grid-cols-3 gap-6">
                        <div class="col-span-1">
                            <div class="w-full h-40 rounded border flex items-center justify-center text-2xl text-gray-600">{{ strtoupper(substr($user->name ?? '', 0, 1) ?: '?') }}</div>
                        </div>
                        <div class="col-span-2">
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold">Details</h3>
                                <div class="mt-2 text-sm text-gray-700">
                                    <p><strong>Name:</strong> {{ $user->name }}</p>
                                    <p><strong>Email:</strong> {{ $user->email }}</p>
                                    <p><strong>Registered:</strong> {{ $user->created_at?->format('Y-m-d H:i') }}</p>
                                    <p><strong>Roles:</strong>
                                        @if($user->roles->count())
                                            @foreach($user->roles as $role)
                                                <span class="inline-block px-2 py-1 mr-2 rounded bg-gray-100 text-sm text-gray-700">{{ $role->name }}</span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold">Other</h3>
                                <div class="mt-2 text-sm text-gray-700">
                                    <p><strong>Last updated:</strong> {{ $user->updated_at?->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
