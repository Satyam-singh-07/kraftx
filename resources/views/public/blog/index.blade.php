<x-layout title="Our Blog">
    <style>
        .blog-hero { padding: 60px 0; background: #fbf8f4; margin-bottom: 60px; }
        .blog-card { border-radius: 20px; overflow: hidden; background: #fff; transition: all 0.3s ease; height: 100%; border: 1px solid #eee; }
        .blog-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); }
        .blog-image { aspect-ratio: 16/10; overflow: hidden; }
        .blog-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .blog-card:hover .blog-image img { transform: scale(1.05); }
        .blog-content { padding: 24px; }
        .blog-meta { font-size: 13px; color: #888; margin-bottom: 12px; display: flex; align-items: center; gap: 15px; }
        .blog-title { font-size: 20px; font-weight: 600; line-height: 1.4; margin-bottom: 12px; }
        .blog-title a { color: #111; text-decoration: none; }
        .blog-title a:hover { color: #b58b21; }
        .blog-excerpt { color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 20px; }
        
        .blog-sidebar-widget { background: #fff; border: 1px solid #eee; border-radius: 15px; padding: 24px; margin-bottom: 30px; }
        .widget-title { font-size: 18px; font-weight: 600; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #b58b21; display: inline-block; }
        .category-list { list-style: none; padding: 0; }
        .category-list li { margin-bottom: 12px; }
        .category-list a { display: flex; justify-content: space-between; color: #444; text-decoration: none; font-size: 14px; transition: color 0.2s; }
        .category-list a:hover { color: #b58b21; }
        
        .recent-post-item { display: flex; gap: 12px; margin-bottom: 15px; }
        .recent-post-img { width: 70px; height: 70px; border-radius: 8px; overflow: hidden; flex-shrink: 0; }
        .recent-post-img img { width: 100%; height: 100%; object-fit: cover; }
        .recent-post-info h6 { font-size: 14px; font-weight: 500; line-height: 1.4; margin-bottom: 5px; }
        .recent-post-info span { font-size: 12px; color: #999; }
    </style>

    <div class="blog-hero text-center">
        <div class="container">
            <h1 class="text-display fw-medium mb-12">Our Blog</h1>
            <p class="text-body-1 cl-text-2 max-w-600 mx-auto">Discover stories, guides, and insights from the world of KraftX.</p>
        </div>
    </div>

    <div class="container pb-80">
        <div class="row">
            <div class="col-lg-8">
                <div class="row g-4">
                    @forelse($posts as $post)
                    <div class="col-md-6 wow fadeInUp">
                        <div class="blog-card">
                            <div class="blog-image">
                                <a href="{{ route('blog.show', $post->slug) }}">
                                    @if($post->featured_image)
                                        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}">
                                    @else
                                        <img src="{{ asset('assets/images/blog/blog-placeholder.jpg') }}" alt="{{ $post->title }}">
                                    @endif
                                </a>
                            </div>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <span><i class="icon icon-Calendar"></i> {{ $post->published_at->format('M d, Y') }}</span>
                                    <span><i class="icon icon-User"></i> {{ $post->author?->name }}</span>
                                </div>
                                <h4 class="blog-title">
                                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                </h4>
                                <p class="blog-excerpt">{{ Str::limit($post->excerpt, 100) }}</p>
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-underline fw-semibold text-dark text-caption-01">READ MORE</a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">No blog posts found.</p>
                    </div>
                    @endforelse
                </div>

               
            </div>

            <div class="col-lg-4">
                <div class="blog-sidebar ps-lg-4">
                    <!-- Categories -->
                    <div class="blog-sidebar-widget">
                        <h5 class="widget-title">Categories</h5>
                        <ul class="category-list">
                            <li>
                                <a href="{{ route('blog.index') }}" class="{{ !request('category') ? 'fw-bold text-dark' : '' }}">
                                    All Posts
                                </a>
                            </li>
                            @foreach($categories as $category)
                            <li>
                                <a href="{{ route('blog.index', ['category' => $category->slug]) }}" class="{{ request('category') == $category->slug ? 'fw-bold text-dark' : '' }}">
                                    {{ $category->name }}
                                    <span class="text-muted">({{ $category->posts_count }})</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Recent Posts -->
                    <div class="blog-sidebar-widget">
                        <h5 class="widget-title">Recent Posts</h5>
                        <div class="recent-posts-list">
                            @foreach($recentPosts as $recent)
                            <div class="recent-post-item">
                                <div class="recent-post-img">
                                    <a href="{{ route('blog.show', $recent->slug) }}">
                                        @if($recent->featured_image)
                                            <img src="{{ Storage::url($recent->featured_image) }}" alt="">
                                        @else
                                            <img src="{{ asset('assets/images/blog/blog-placeholder.jpg') }}" alt="">
                                        @endif
                                    </a>
                                </div>
                                <div class="recent-post-info">
                                    <h6><a href="{{ route('blog.show', $recent->slug) }}" class="text-dark text-decoration-none">{{ Str::limit($recent->title, 40) }}</a></h6>
                                    <span>{{ $recent->published_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tags -->
                    @if($tags->isNotEmpty())
                    <div class="blog-sidebar-widget">
                        <h5 class="widget-title">Popular Tags</h5>
                        <div class="d-flex flex-wrap gap-8">
                            @foreach($tags as $tag)
                            <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" 
                               class="px-3 py-2 bg-light border rounded-pill text-dark text-decoration-none text-caption-02 {{ request('tag') == $tag->slug ? 'bg-dark text-white' : '' }}">
                                #{{ $tag->name }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>
