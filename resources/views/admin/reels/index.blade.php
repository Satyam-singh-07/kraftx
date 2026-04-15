<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Reels</h2>
            <a href="{{ route('admin.reels.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm font-bold">
                Add Reel
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($reels as $reel)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col group">
                    <div class="relative aspect-[9/16] bg-gray-100 dark:bg-gray-900">
                        @if($reel->thumbnail)
                            <img src="{{ asset('storage/' . $reel->thumbnail) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-4">
                            <a href="{{ route('admin.reels.edit', $reel->id) }}" class="p-2 bg-white rounded-full text-blue-600 hover:bg-blue-50 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </a>
                            <form action="{{ route('admin.reels.destroy', $reel->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this reel?')" class="p-2 bg-white rounded-full text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                        <div class="absolute top-2 right-2">
                            <span class="px-2 py-1 text-[10px] font-bold rounded-full {{ $reel->status ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                {{ $reel->status ? 'ACTIVE' : 'INACTIVE' }}
                            </span>
                        </div>
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $reel->title ?? 'Untitled Reel' }}</h3>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1 truncate">{{ $reel->video_url }}</p>
                        @if($reel->product)
                            <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-700 flex items-center text-[10px] text-gray-500">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                Linked: {{ $reel->product->name }}
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                    <p class="text-gray-500 dark:text-gray-400">No reels added yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.admin>
