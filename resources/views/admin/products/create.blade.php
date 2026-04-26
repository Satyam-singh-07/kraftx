<x-layouts.admin>
    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            .editor-toolbar {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
                padding: 8px;
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-bottom: none;
                border-radius: 8px 8px 0 0;
                align-items: center;
            }
            .editor-toolbar button, .editor-toolbar select {
                padding: 4px 8px;
                background: white;
                border: 1px solid #ced4da;
                border-radius: 4px;
                cursor: pointer;
                transition: all 0.2s;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
            }
            .editor-toolbar button:hover {
                background: #e9ecef;
            }
            .editor-toolbar select {
                padding: 0 4px;
                outline: none;
            }
            .editor-area {
                min-height: 250px;
                padding: 16px;
                border: 1px solid #ced4da;
                border-radius: 0 0 8px 8px;
                background: white;
                outline: none;
                font-size: 16px;
                line-height: 1.5;
            }
            .editor-area:focus {
                border-color: #86b7fe;
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            }
            /* Styling for common tags inside editor */
            .editor-area h1 { font-size: 2em; font-weight: bold; }
            .editor-area h2 { font-size: 1.5em; font-weight: bold; }
            .editor-area h3 { font-size: 1.17em; font-weight: bold; }
            .editor-area ul { list-style-type: disc; padding-left: 20px; }
            .editor-area ol { list-style-type: decimal; padding-left: 20px; }

            .dark .editor-toolbar { background: #374151; border-color: #4b5563; }
            .dark .editor-toolbar button, .dark .editor-toolbar select { background: #1f2937; border-color: #4b5563; color: white; }
            .dark .editor-area { background: #1f2937; border-color: #4b5563; color: white; }
        </style>
    @endpush

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Create Product</h2>
            <a href="{{ route('admin.products.index') }}"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Back to List</a>
        </div>

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6"
            enctype="multipart/form-data" id="productForm">
            @csrf

            <x-admin.card title="Basic Information">
                <div class="space-y-5 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Product Name
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name') }}"
                            placeholder="Enter product name"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Short
                            Description</label>
                        <textarea name="short_description" rows="2" placeholder="Brief summary of the product..."
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">{{ old('short_description') }}</textarea>
                    </div>

                    <!-- Custom Rich Text Editor for Full Description -->
                    <div class="mb-3">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Full Description *</label>
                        <div class="editor-toolbar" data-target="description">
                            <select class="editor-font-size" data-command="fontSize">
                                <option value="3">Normal</option>
                                <option value="1">Small</option>
                                <option value="2">Medium</option>
                                <option value="4">Large</option>
                                <option value="5">Extra Large</option>
                                <option value="6">Huge</option>
                                <option value="7">Giant</option>
                            </select>
                            <select class="editor-format" data-command="formatBlock">
                                <option value="p">Paragraph</option>
                                <option value="h1">Heading 1</option>
                                <option value="h2">Heading 2</option>
                                <option value="h3">Heading 3</option>
                                <option value="h4">Heading 4</option>
                            </select>
                            <div class="mx-1 h-6 border-l border-gray-300"></div>
                            <button type="button" data-command="bold" title="Bold"><i class="fas fa-bold"></i></button>
                            <button type="button" data-command="italic" title="Italic"><i class="fas fa-italic"></i></button>
                            <button type="button" data-command="underline" title="Underline"><i class="fas fa-underline"></i></button>
                            <div class="mx-1 h-6 border-l border-gray-300"></div>
                            <button type="button" data-command="insertUnorderedList" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                            <button type="button" data-command="insertOrderedList" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                            <div class="mx-1 h-6 border-l border-gray-300"></div>
                            <button type="button" data-command="justifyLeft" title="Align Left"><i class="fas fa-align-left"></i></button>
                            <button type="button" data-command="justifyCenter" title="Align Center"><i class="fas fa-align-center"></i></button>
                            <button type="button" data-command="justifyRight" title="Align Right"><i class="fas fa-align-right"></i></button>
                        </div>
                        <div class="editor-area" contenteditable="true" id="editor-description" placeholder="Enter detailed information here..."></div>
                        <input type="hidden" name="description" id="input-description" value="{{ old('description') }}">
                    </div>

                    <!-- Custom Rich Text Editor for Perfect Placement -->
                    <div class="mb-3">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Perfect Placement</label>
                        <div class="editor-toolbar" data-target="perfect_placement">
                            <select class="editor-font-size" data-command="fontSize">
                                <option value="3">Normal</option>
                                <option value="1">Small</option>
                                <option value="2">Medium</option>
                                <option value="4">Large</option>
                                <option value="5">Extra Large</option>
                                <option value="6">Huge</option>
                                <option value="7">Giant</option>
                            </select>
                            <select class="editor-format" data-command="formatBlock">
                                <option value="p">Paragraph</option>
                                <option value="h1">Heading 1</option>
                                <option value="h2">Heading 2</option>
                                <option value="h3">Heading 3</option>
                                <option value="h4">Heading 4</option>
                            </select>
                            <div class="mx-1 h-6 border-l border-gray-300"></div>
                            <button type="button" data-command="bold" title="Bold"><i class="fas fa-bold"></i></button>
                            <button type="button" data-command="italic" title="Italic"><i class="fas fa-italic"></i></button>
                            <button type="button" data-command="underline" title="Underline"><i class="fas fa-underline"></i></button>
                            <div class="mx-1 h-6 border-l border-gray-300"></div>
                            <button type="button" data-command="insertUnorderedList" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                            <button type="button" data-command="insertOrderedList" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                        </div>
                        <div class="editor-area" contenteditable="true" id="editor-perfect_placement" placeholder="Where would this product look best?..."></div>
                        <input type="hidden" name="perfect_placement" id="input-perfect_placement" value="{{ old('perfect_placement') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Product Video URL</label>
                        <input type="text" name="video_url" value="{{ old('video_url') }}"
                            placeholder="YouTube or Vimeo link"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Product Media">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Main Image (Primary)</label>
                        <input type="file" name="main_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('main_image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Gallery Images</label>
                        <input type="file" name="gallery_images[]" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Size & Weight Image</label>
                        <input type="file" name="size_weight_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Pricing & Inventory">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Regular Price (₹) *</label>
                        <input type="number" step="0.01" name="price" required value="{{ old('price') }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Sale Price (₹)</label>
                        <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Stock *</label>
                        <input type="number" name="stock" required value="{{ old('stock', 0) }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" placeholder="AUTO-GENERATE"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Organization & Visibility">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Collections</label>
                        <select name="collection_ids[]" multiple class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                            @foreach($collections as $collection)
                                <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple collections.</p>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="status" id="status" value="1" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="status" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Active (Visible on store)</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="featured" id="featured" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="featured" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Featured Product</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_trending" id="is_trending" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_trending" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Trending Product</label>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <div class="flex justify-end pt-4 pb-12">
                <button type="submit" class="px-10 py-4 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 font-bold text-lg">
                    Create Product
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editors = ['description', 'perfect_placement'];
            
            editors.forEach(id => {
                const editor = document.getElementById('editor-' + id);
                const input = document.getElementById('input-' + id);
                const toolbar = document.querySelector(`.editor-toolbar[data-target="${id}"]`);
                
                if (input.value) {
                    editor.innerHTML = input.value;
                }

                // Button commands
                toolbar.querySelectorAll('button').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const command = this.getAttribute('data-command');
                        document.execCommand(command, false, null);
                        editor.focus();
                    });
                });

                // Select commands (Font Size / Format)
                toolbar.querySelectorAll('select').forEach(select => {
                    select.addEventListener('change', function() {
                        const command = this.getAttribute('data-command');
                        const value = this.value;
                        
                        if (command === 'formatBlock') {
                            document.execCommand(command, false, `<${value}>`);
                        } else {
                            document.execCommand(command, false, value);
                        }
                        editor.focus();
                    });
                });

                editor.addEventListener('input', function() {
                    input.value = this.innerHTML;
                });
            });

            document.getElementById('productForm').addEventListener('submit', function() {
                editors.forEach(id => {
                    document.getElementById('input-' + id).value = document.getElementById('editor-' + id).innerHTML;
                });
            });
        });
    </script>
    @endpush
</x-layouts.admin>
