<x-layouts.admin>
    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            .editor-toolbar { display: flex; flex-wrap: wrap; gap: 5px; padding: 8px; background: #f8f9fa; border: 1px solid #dee2e6; border-bottom: none; border-radius: 8px 8px 0 0; align-items: center; }
            .editor-toolbar button, .editor-toolbar select { padding: 4px 8px; background: white; border: 1px solid #ced4da; border-radius: 4px; cursor: pointer; transition: all 0.2s; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 13px; }
            .editor-toolbar button:hover { background: #e9ecef; }
            .editor-area { min-height: 400px; padding: 16px; border: 1px solid #ced4da; border-radius: 0 0 8px 8px; background: white; outline: none; font-size: 16px; line-height: 1.5; }
            .editor-area h1 { font-size: 2em; font-weight: bold; }
            .editor-area h2 { font-size: 1.5em; font-weight: bold; }
            .editor-area ul { list-style-type: disc; padding-left: 20px; }
            .editor-area ol { list-style-type: decimal; padding-left: 20px; }
            .dark .editor-toolbar { background: #374151; border-color: #4b5563; }
            .dark .editor-toolbar button, .dark .editor-toolbar select { background: #1f2937; border-color: #4b5563; color: white; }
            .dark .editor-area { background: #1f2937; border-color: #4b5563; color: white; }
        </style>
    @endpush

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-4 gap-6"
        x-data="{
            tagInputValue: '',
            selectedTags: {{ $blogPost->tags->map(fn($t) => ['id' => $t->id, 'name' => $t->name])->toJson() }},
            addTagFromInput() {
                const val = this.tagInputValue.trim().replace(/,$/, '');
                if (val && !this.isTagSelected(val)) {
                    this.selectedTags.push({ id: null, name: val });
                }
                this.tagInputValue = '';
            },
            toggleTag(tag) {
                const index = this.selectedTags.findIndex(t => t.name.toLowerCase() === tag.name.toLowerCase());
                if (index === -1) {
                    this.selectedTags.push(tag);
                } else {
                    this.selectedTags.splice(index, 1);
                }
            },
            removeTag(index) {
                this.selectedTags.splice(index, 1);
            },
            removeLastTag() {
                this.selectedTags.pop();
            },
            isTagSelected(name) {
                return this.selectedTags.some(t => t.name.toLowerCase() === name.toLowerCase());
            }
        }"
    >
        <div class="lg:col-span-3 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Edit Blog Post: {{ $blogPost->title }}</h2>
                <a href="{{ route('admin.blog-posts.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">Back to List</a>
            </div>

            <form action="{{ route('admin.blog-posts.update', $blogPost->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="postForm">
                @csrf
                @method('PUT')

                <x-admin.card title="Post Content">
                    <div class="space-y-5 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Title *</label>
                            <input type="text" name="title" required value="{{ old('title', $blogPost->title) }}" placeholder="Post title"
                                class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none focus:ring-2 focus:ring-blue-500/20">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Slug (URL)</label>
                            <input type="text" name="slug" value="{{ old('slug', $blogPost->slug) }}" placeholder="post-slug"
                                class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Excerpt</label>
                            <textarea name="excerpt" rows="2" placeholder="Brief summary of the post..."
                                class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">{{ old('excerpt', $blogPost->excerpt) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Content *</label>
                            <div class="editor-toolbar" data-target="content">
                                <select class="editor-format" data-command="formatBlock">
                                    <option value="p">Paragraph</option>
                                    <option value="h1">Heading 1</option>
                                    <option value="h2">Heading 2</option>
                                    <option value="h3">Heading 3</option>
                                </select>
                                <div class="mx-1 h-6 border-l border-gray-300"></div>
                                <button type="button" data-command="bold" title="Bold"><i class="fas fa-bold"></i></button>
                                <button type="button" data-command="italic" title="Italic"><i class="fas fa-italic"></i></button>
                                <button type="button" data-command="underline" title="Underline"><i class="fas fa-underline"></i></button>
                                <div class="mx-1 h-6 border-l border-gray-300"></div>
                                <button type="button" data-command="insertUnorderedList" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                                <button type="button" data-command="insertOrderedList" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                                <div class="mx-1 h-6 border-l border-gray-300"></div>
                                <button type="button" data-command="createLink" title="Link"><i class="fas fa-link"></i></button>
                                <button type="button" id="insert-product-btn" title="Insert Product" class="text-blue-600 border-blue-200 bg-blue-50 hover:bg-blue-100 font-medium">
                                    <i class="fas fa-shopping-bag mr-1"></i> Product
                                </button>
                            </div>
                            <div class="editor-area" contenteditable="true" id="editor-content"></div>
                            <input type="hidden" name="content" id="input-content" value="{{ old('content', $blogPost->content) }}">
                        </div>
                    </div>
                </x-admin.card>

                <x-admin.card title="Post Settings">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Category *</label>
                            <select name="blog_category_id" required class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('blog_category_id', $blogPost->blog_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Featured Image</label>
                            @if($blogPost->featured_image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($blogPost->featured_image) }}" alt="" class="w-20 h-20 object-cover rounded">
                                </div>
                            @endif
                            <input type="file" name="featured_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('featured_image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tags</label>
                            <div class="space-y-3">
                                <!-- Modern Tag Input -->
                                <div class="flex flex-wrap gap-2 p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg min-h-[42px] focus-within:ring-2 focus-within:ring-blue-500/20" id="tag-input-container">
                                    <template x-for="(tag, index) in selectedTags" :key="index">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300 rounded text-xs font-medium">
                                            <span x-text="tag.name"></span>
                                            <button type="button" @click="removeTag(index)" class="hover:text-blue-900">&times;</button>
                                        </span>
                                    </template>
                                    <input type="text" 
                                        @keydown.enter.prevent="addTagFromInput"
                                        @keydown.comma.prevent="addTagFromInput"
                                        @keydown.backspace="if (tagInputValue === '') removeLastTag()"
                                        x-model="tagInputValue"
                                        placeholder="Type and press enter..."
                                        class="flex-1 bg-transparent border-none outline-none text-sm dark:text-white min-w-[120px] focus:ring-0 p-0">
                                </div>

                                <!-- Hidden inputs for form submission -->
                                <template x-for="tag in selectedTags" :key="tag.id || tag.name">
                                    <input type="hidden" name="tags[]" :value="tag.name">
                                </template>

                                <!-- Quick Select Tags -->
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Quick Select Existing</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($tags as $tag)
                                            <button type="button" 
                                                @click="toggleTag({ id: {{ $tag->id }}, name: '{{ $tag->name }}' })"
                                                :class="isTagSelected('{{ $tag->name }}') ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700'"
                                                class="px-3 py-1 text-xs border rounded-pill transition-all hover:border-blue-500">
                                                {{ $tag->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4 pt-6">
                            <div class="flex items-center">
                                <input type="hidden" name="status" value="0">
                                <input type="checkbox" name="status" id="status" value="1" {{ $blogPost->status ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="status" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Published</label>
                            </div>
                            <div class="flex items-center">
                                <input type="hidden" name="is_featured" value="0">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ $blogPost->is_featured ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="is_featured" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Featured Post</label>
                            </div>
                            <div class="flex items-center">
                                <input type="hidden" name="is_home" value="0">
                                <input type="checkbox" name="is_home" id="is_home" value="1" {{ $blogPost->is_home ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="is_home" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Show on Home Page</label>
                            </div>                        </div>
                    </div>
                </x-admin.card>

                <x-admin.card title="Advanced SEO">
                    <div class="grid grid-cols-1 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Title</label>
                            <input type="text" name="seo[meta_title]" value="{{ old('seo.meta_title', $blogPost->seoMeta->meta_title ?? '') }}" placeholder="SEO title"
                                class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Description</label>
                            <textarea name="seo[meta_description]" rows="2" class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">{{ old('seo.meta_description', $blogPost->seoMeta->meta_description ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Keywords</label>
                            <input type="text" name="seo[meta_keywords]" value="{{ old('seo.meta_keywords', $blogPost->seoMeta->meta_keywords ?? '') }}" placeholder="keyword1, keyword2"
                                class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Canonical URL</label>
                            <input type="url" name="seo[canonical_url]" value="{{ old('seo.canonical_url', $blogPost->seoMeta->canonical_url ?? '') }}" placeholder="https://..."
                                class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Robots</label>
                            <select name="seo[meta_robots]" class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                                <option value="index,follow" {{ old('seo.meta_robots', $blogPost->seoMeta->meta_robots ?? 'index,follow') === 'index,follow' ? 'selected' : '' }}>index,follow</option>
                                <option value="noindex,follow" {{ old('seo.meta_robots', $blogPost->seoMeta->meta_robots ?? '') === 'noindex,follow' ? 'selected' : '' }}>noindex,follow</option>
                                <option value="noindex,nofollow" {{ old('seo.meta_robots', $blogPost->seoMeta->meta_robots ?? '') === 'noindex,nofollow' ? 'selected' : '' }}>noindex,nofollow</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">OG Image</label>
                            @if(isset($blogPost->seoMeta->og_image))
                                <div class="mb-2">
                                    <img src="{{ Storage::url($blogPost->seoMeta->og_image) }}" alt="" class="w-20 h-20 object-cover rounded">
                                </div>
                            @endif
                            <input type="file" name="seo[og_image]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>
                </x-admin.card>

                <div class="flex justify-end pt-4 pb-12">
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold">
                        Update Post
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar for Product Helper -->
        <div class="space-y-6">
            <x-admin.card title="Insert Products">
                <div class="mt-4 space-y-4">
                    <input type="text" id="product-search" placeholder="Search products..." 
                        class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    
                    <div id="product-list" class="space-y-2 max-h-[600px] overflow-auto">
                        <p class="text-xs text-gray-500">Loading products...</p>
                    </div>
                </div>
            </x-admin.card>
            
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-800">
                <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-2">How to use</h4>
                <p class="text-xs text-blue-700 dark:text-blue-400 leading-relaxed">
                    Click <strong>"Insert"</strong> on any product to add a "Buy Now" card to your post. 
                    Or manually use <code>[product id=XX]</code>.
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editor = document.getElementById('editor-content');
            const input = document.getElementById('input-content');
            const toolbar = document.querySelector('.editor-toolbar');
            
            if (input.value) editor.innerHTML = input.value;

            toolbar.querySelectorAll('button:not(#insert-product-btn)').forEach(btn => {
                btn.addEventListener('click', function() {
                    const command = this.getAttribute('data-command');
                    if (command === 'createLink') {
                        const url = prompt('Enter the link URL:');
                        if (url) document.execCommand(command, false, url);
                    } else {
                        document.execCommand(command, false, null);
                    }
                    editor.focus();
                });
            });

            toolbar.querySelector('select').addEventListener('change', function() {
                document.execCommand('formatBlock', false, `<${this.value}>`);
                editor.focus();
            });

            editor.addEventListener('input', () => input.value = editor.innerHTML);
            document.getElementById('postForm').addEventListener('submit', () => input.value = editor.innerHTML);

            // Product Picker Logic
            const productSearch = document.getElementById('product-search');
            const productList = document.getElementById('product-list');
            const insertProductBtn = document.getElementById('insert-product-btn');
            let debounceTimer;

            async function fetchProducts(query = '') {
                try {
                    const res = await axios.get(`{{ route('admin.products.search') }}?q=${query}`);
                    renderProductList(res.data);
                } catch (e) {
                    console.error('Failed to fetch products');
                }
            }

            function renderProductList(products) {
                if (products.length === 0) {
                    productList.innerHTML = '<p class="text-xs text-gray-500 py-4 text-center">No products found</p>';
                    return;
                }

                productList.innerHTML = products.map(p => `
                    <div class="flex items-center gap-3 p-2 border rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <img src="${p.image}" class="w-10 h-10 object-cover rounded" alt="">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium truncate dark:black">${p.name}</p>
                            <p class="text-[10px] text-gray-500">₹${p.price}</p>
                        </div>
                        <button type="button" onclick="insertProductShortcode(${p.id})" 
                            class="px-2 py-1 bg-blue-600 text-white text-[10px] rounded hover:bg-blue-700 transition-colors">
                            Insert
                        </button>
                    </div>
                `).join('');
            }

            window.insertProductShortcode = function(id) {
                const shortcode = `[product id=${id}]`;
                document.execCommand('insertHTML', false, `<div>${shortcode}</div><p><br></p>`);
                editor.focus();
            };

            productSearch.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => fetchProducts(this.value), 300);
            });

            insertProductBtn.addEventListener('click', () => {
                productSearch.focus();
            });

            // Initial load
            fetchProducts();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @endpush
</x-layouts.admin>
