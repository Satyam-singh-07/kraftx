<x-layout :seo="$seo">

    <style>
        .blog-single-hero { padding: 80px 0; background: #fbf8f4; margin-bottom: 60px; }
        .blog-single-title { font-size: 42px; font-weight: 600; line-height: 1.2; margin-bottom: 24px; }
        .blog-post-content { font-size: 17px; line-height: 1.8; color: #333; }
        .blog-post-content h2 { margin: 40px 0 20px; font-weight: 600; }
        .blog-post-content p { margin-bottom: 24px; }
        .blog-post-content img { border-radius: 15px; margin-bottom: 30px; max-width: 100%; height: auto; }
        
        .related-posts { background: #fbf8f4; padding: 80px 0; margin-top: 80px; }
        .comment-item { border-bottom: 1px solid #eee; padding-bottom: 30px; margin-bottom: 30px; }
        .comment-avatar { width: 60px; height: 60px; border-radius: 50%; background: #eee; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-weight: 600; color: #888; }
        
        @media (max-width: 767px) {
            .blog-single-title { font-size: 32px; }
        }
    </style>

    <article>
        <div class="blog-single-hero text-center">
            <div class="container">
                <div class="max-w-800 mx-auto">
                    <div class="blog-meta mb-16 d-flex justify-content-center gap-20 text-caption-01 cl-text-2">
                        <span><i class="icon icon-Calendar"></i> {{ $post->published_at->format('M d, Y') }}</span>
                        <span><i class="icon icon-User"></i> {{ $post->author?->name ?? 'KraftX' }}</span>
                        <span><i class="icon icon-Folder"></i> {{ $post->category->name }}</span>
                    </div>
                    <h1 class="blog-single-title">{{ $post->title }}</h1>
                    @if($post->excerpt)
                        <p class="text-body-1 cl-text-2 mb-0">{{ $post->excerpt }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="container" style="
    margin-bottom: 63px;
">
            <div class="max-w-800 mx-auto">
                @if($post->featured_image)
                    <div class="mb-40">
                        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-100 rounded-20 shadow-sm">
                    </div>
                @endif

                <div class="blog-post-content">
                    {!! $post->parsed_content !!}
                </div>

                <div class="mt-60 pt-40 border-top d-flex flex-wrap justify-content-between align-items-center gap-20">
                    @if($post->tags->isNotEmpty())
                        <div class="d-flex flex-wrap gap-8">
                            <span class="fw-semibold me-8">Tags:</span>
                            @foreach($post->tags as $tag)
                                <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="px-3 py-1 bg-light border rounded-pill text-dark text-decoration-none text-caption-02">#{{ $tag->name }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Comments Section -->
                <div class="mt-80 pt-60 border-top" id="comments">
                    <h3 class="mb-40 fw-medium">Comments ({{ $post->approvedComments->count() }})</h3>
                    
                    <div class="comments-list">
                        @foreach($post->approvedComments as $comment)
                        <div class="comment-item d-flex gap-20">
                            <div class="comment-avatar">
                                {{ strtoupper(substr($comment->name, 0, 1)) }}
                            </div>
                            <div class="comment-content">
                                <div class="d-flex align-items-center gap-10 mb-8">
                                    <h6 class="mb-0">{{ $comment->name }}</h6>
                                    <span class="text-caption-02 cl-text-3">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mb-0 text-body-2">{{ $comment->comment }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="comment-form-wrap mt-60 p-40 bg-light rounded-20">
                        <h4 class="mb-24 fw-medium">Leave a Comment</h4>
                        @if(session('success'))
                            <div class="alert alert-success mb-24">{{ session('success') }}</div>
                        @endif
                        <form action="{{ route('blog.comment.store', $post->id) }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <fieldset class="tf-field">
                                        <input type="text" name="name" placeholder="Your Name *" required value="{{ old('name', auth()->user()->name ?? '') }}">
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="tf-field">
                                        <input type="email" name="email" placeholder="Your Email *" required value="{{ old('email', auth()->user()->email ?? '') }}">
                                    </fieldset>
                                </div>
                                <div class="col-12">
                                    <fieldset class="tf-field">
                                        <textarea name="comment" placeholder="Your Comment *" rows="5" required>{{ old('comment') }}</textarea>
                                    </fieldset>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="tf-btn animate-btn">Post Comment</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <!-- Related Posts -->
    @if($relatedPosts->isNotEmpty())
    <section class="related-posts">
        <div class="container">
            <h3 class="text-center mb-40 fw-medium">Related Stories</h3>
            <div class="row g-4">
                @foreach($relatedPosts as $rel)
                <div class="col-md-4">
                    <div class="blog-card" style="background: #fff; border: 1px solid #eee; border-radius: 15px; overflow: hidden;">
                        <div class="blog-image">
                            <a href="{{ route('blog.show', $rel->slug) }}">
                                @if($rel->featured_image)
                                    <img src="{{ Storage::url($rel->featured_image) }}" alt="{{ $rel->title }}" style="width: 100%; aspect-ratio: 16/10; object-fit: cover;">
                                @endif
                            </a>
                        </div>
                        <div class="p-24">
                            <h5 class="mb-12"><a href="{{ route('blog.show', $rel->slug) }}" class="text-dark text-decoration-none hover-primary">{{ $rel->title }}</a></h5>
                            <a href="{{ route('blog.show', $rel->slug) }}" class="text-caption-01 fw-semibold text-dark text-decoration-underline">READ MORE</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</x-layout>
