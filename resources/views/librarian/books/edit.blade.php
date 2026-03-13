<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Book') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white">
                    @if ($errors->any())
                        <div class="mb-4 text-sm text-red-700">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('librarian.books.update', $book) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" value="{{ old('title', $book->title) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Publisher</label>
                                <select name="publisher_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">--</option>
                                    @foreach($publishers as $p)
                                        <option value="{{ $p->id }}" @if(old('publisher_id', $book->publisher_id)==$p->id) selected @endif>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                <input type="number" name="total_quantity" value="{{ old('total_quantity', $book->total_quantity) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>

                        <div x-data='{
                            authorsList: @json($authors->map(fn($a)=>['id'=>$a->id,'name'=>$a->name])),
                            categoriesList: @json($categories->map(fn($c)=>['id'=>$c->id,'name'=>$c->name])),
                            authorQuery: "",
                            categoryQuery: "",
                            openAuthors: false,
                            openCategories: false,
                            selectedAuthors: @json(old('authors', $book->authors->pluck('id')->toArray())),
                            selectedCategories: @json(old('categories', $book->categories->pluck('id')->toArray())),
                            imagePreview: null,
                            _currentBlob: null,
                            handleFileChange(event){
                                const input = event.target;
                                const file = input.files && input.files[0];
                                if(!file){
                                    if(this._currentBlob){ URL.revokeObjectURL(this._currentBlob); this._currentBlob = null }
                                    this.imagePreview = null;
                                    return;
                                }
                                if(this._currentBlob){ URL.revokeObjectURL(this._currentBlob); this._currentBlob = null }
                                try{
                                    const blobUrl = URL.createObjectURL(file);
                                    this._currentBlob = blobUrl;
                                    this.imagePreview = blobUrl;
                                }catch(e){ console.log("createObjectURL failed", e) }
                                const reader = new FileReader();
                                reader.onload = (ev)=>{ this.imagePreview = ev.target.result };
                                reader.readAsDataURL(file);
                                console.log("handleFileChange - file chosen", file.name);
                            },
                        }' class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Authors</label>

                            <div class="mt-2 flex items-center gap-3">
                                <button type="button" @click="openAuthors = true" class="px-3 py-1 bg-gray-100 border rounded">Choose authors</button>
                                <div class="text-sm text-gray-600">Selected: <span x-text="selectedAuthors.length"></span></div>
                            </div>

                            <!-- hidden inputs for form submission -->
                            <template x-for="id in selectedAuthors" :key="id">
                                <input type="hidden" name="authors[]" :value="id">
                            </template>

                            <div class="mt-2">
                                <template x-if="selectedAuthors.length">
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="id in selectedAuthors" :key="id">
                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded" x-text="(authorsList.find(a => a.id == id) || {}).name"></span>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- Authors modal -->
                            <div x-show="openAuthors" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-6">
                                <div @click.away="openAuthors = false" class="w-full max-w-2xl bg-white rounded shadow-lg overflow-hidden">
                                    <div class="p-4 border-b flex items-center justify-between">
                                        <div class="flex-1">
                                            <input x-model="authorQuery" placeholder="Search authors..." class="w-full border rounded px-2 py-1">
                                        </div>
                                        <div class="ms-4">
                                            <button @click="openAuthors = false" class="px-3 py-1 text-sm text-gray-700">Close</button>
                                        </div>
                                    </div>
                                    <div class="p-4 max-h-80 overflow-auto">
                                        <template x-for="a in authorsList.filter(a => a.name.toLowerCase().includes(authorQuery.toLowerCase()))" :key="a.id">
                                            <div class="flex items-center justify-between py-1">
                                                <div class="flex items-center gap-3">
                                                    <input type="checkbox" :value="a.id" x-model="selectedAuthors">
                                                    <div x-text="a.name"></div>
                                                </div>
                                            </div>
                                        </template>
                                        <div x-show="authorsList.filter(a => a.name.toLowerCase().includes(authorQuery.toLowerCase())).length == 0" class="text-sm text-gray-500">No authors found.</div>
                                    </div>
                                    <div class="p-4 border-t text-right">
                                        <button @click="openAuthors = false" class="px-4 py-2 bg-blue-600 text-white rounded">Done</button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700">Categories</label>
                                <div class="mt-2 flex items-center gap-3">
                                    <button type="button" @click="openCategories = true" class="px-3 py-1 bg-gray-100 border rounded">Choose categories</button>
                                    <div class="text-sm text-gray-600">Selected: <span x-text="selectedCategories.length"></span></div>
                                </div>

                                <!-- hidden inputs for categories -->
                                <template x-for="id in selectedCategories" :key="id">
                                    <input type="hidden" name="categories[]" :value="id">
                                </template>

                                <!-- Categories modal -->
                                <div x-show="openCategories" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-6">
                                    <div @click.away="openCategories = false" class="w-full max-w-2xl bg-white rounded shadow-lg overflow-hidden">
                                        <div class="p-4 border-b flex items-center justify-between">
                                            <div class="flex-1">
                                                <input x-model="categoryQuery" placeholder="Search categories..." class="w-full border rounded px-2 py-1">
                                            </div>
                                            <div class="ms-4">
                                                <button @click="openCategories = false" class="px-3 py-1 text-sm text-gray-700">Close</button>
                                            </div>
                                        </div>
                                        <div class="p-4 max-h-80 overflow-auto">
                                            <template x-for="c in categoriesList.filter(c => c.name.toLowerCase().includes(categoryQuery.toLowerCase()))" :key="c.id">
                                                <div class="flex items-center justify-between py-1">
                                                    <div class="flex items-center gap-3">
                                                        <input type="checkbox" :value="c.id" x-model="selectedCategories">
                                                        <div x-text="c.name"></div>
                                                    </div>
                                                </div>
                                            </template>
                                            <div x-show="categoriesList.filter(c => c.name.toLowerCase().includes(categoryQuery.toLowerCase())).length == 0" class="text-sm text-gray-500">No categories found.</div>
                                        </div>
                                        <div class="p-4 border-t text-right">
                                            <button @click="openCategories = false" class="px-4 py-2 bg-blue-600 text-white rounded">Done</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <template x-if="selectedCategories.length">
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="id in selectedCategories" :key="id">
                                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded" x-text="(categoriesList.find(c => c.id == id) || {}).name"></span>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Image</label>

                            <!-- image preview displayed below -->

                            <div class="mb-2">
                                <template x-if="imagePreview">
                                    <img :src="imagePreview" alt="Preview" class="h-24 mb-2 rounded">
                                </template>

                                @if($book->image_path)
                                    <div id="existing-image" x-show="!imagePreview" class="mb-2"><img src="{{ asset('storage/'.$book->image_path) }}" alt="" class="h-24"></div>
                                @endif
                            </div>

                            <!-- JS fallback preview (non-Alpine) -->
                            <div class="mt-2 mb-2">
                                <img id="js-image-preview" alt="Preview" class="h-24 mb-2 rounded hidden">
                            </div>

                            <input id="js-image-input" type="file" name="image" class="mt-1 block w-full" @change="handleFileChange($event)">
                        </div>

                        <script>
                        (function(){
                            const input = document.getElementById('js-image-input');
                            const preview = document.getElementById('js-image-preview');
                            const existing = document.getElementById('existing-image');
                            if(!input || !preview) return;
                            input.addEventListener('change', function(e){
                                const f = input.files && input.files[0];
                                if(!f){ preview.classList.add('hidden'); preview.src = ''; if(existing) existing.style.display = ''; return; }
                                const reader = new FileReader();
                                reader.onload = function(ev){ preview.src = ev.target.result; preview.classList.remove('hidden'); if(existing) existing.style.display = 'none'; };
                                reader.readAsDataURL(f);
                            });
                        })();
                        </script>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $book->description) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('librarian.books.index') }}" class="mr-3">Cancel</a>
                            <button class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
