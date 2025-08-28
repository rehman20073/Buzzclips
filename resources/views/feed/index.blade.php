<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuzzClips — Feed</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .video-feed { scroll-snap-type: y mandatory; scrollbar-width: none; -ms-overflow-style: none; }
        .video-feed::-webkit-scrollbar { display:none; }
        .video-container { scroll-snap-align: start; }
        .rail-btn { transition: transform .1s ease; }
        .rail-btn:active { transform: scale(.95); }
        .heart-pop { animation: heartBeat .28s ease-in-out; }
        @keyframes heartBeat { 0%{transform:scale(1)} 50%{transform:scale(1.2)} 100%{transform:scale(1)} }
        .glass { background: rgba(17,24,39,.6); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); }
    </style>
    @php $brandGradient = 'from-fuchsia-500 via-violet-500 to-sky-500'; @endphp
</head>
<body class="bg-gray-950 overflow-hidden">
<div class="min-h-screen w-full flex justify-center">
    <div class="relative w-full max-w-[600px] bg-black">
        <!-- Top Bar -->
        <div class="pointer-events-none absolute top-0 left-0 right-0 z-40">
            <div class="pointer-events-auto flex items-center justify-between px-4 py-3 bg-gradient-to-b from-black/70 to-transparent">
                <div class="flex items-center space-x-2">
                    <span class="inline-flex h-7 w-7 rounded-md bg-gradient-to-br {{ $brandGradient }}"></span>
                    <span class="text-white font-semibold tracking-wide">BuzzClips</span>
                </div>
                <div class="flex items-center space-x-2">
                    @auth
                        @if(method_exists(auth()->user(),'isCreator') && auth()->user()->isCreator())
                            <a href="/creator" class="text-white/90 hover:text-white text-sm px-3 py-1 rounded-full border border-white/20">Creator</a>
                        @endif
                        <a href="/profile" class="text-white/90 hover:text-white text-sm px-3 py-1 rounded-full border border-white/20">Profile</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-white/90 hover:text-white text-sm px-3 py-1 rounded-full border border-white/20">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-white/90 hover:text-white text-sm px-3 py-1 rounded-full border border-white/20">Login</a>
                        <a href="{{ route('register') }}" class="text-black text-sm px-3 py-1 rounded-full bg-gradient-to-br {{ $brandGradient }}">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Feed -->
        <div class="video-feed h-screen overflow-y-scroll snap-y snap-mandatory">
            @foreach($videos as $video)
                @php $liked = $video->is_liked_by_user ?? false; @endphp
                <div class="video-container relative h-screen w-full snap-start" data-video-id="{{ $video->id }}" id="v{{ $video->id }}">
                    <!-- Video -->
                    <video
                        class="video-element absolute inset-0 w-full h-full object-cover"
                        src="{{ $video->getVideo() }}"
                        @if($video->getThumbnail()) poster="{{ $video->getThumbnail() }}" @endif
                        loop muted autoplay preload="metadata" playsinline webkit-playsinline
                        crossorigin="anonymous"
                        data-video-id="{{ $video->id }}">
                    </video>

                    <!-- Gradient overlays -->
                    <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-black/60 to-transparent pointer-events-none"></div>
                    <div class="absolute inset-x-0 bottom-0 h-56 bg-gradient-to-t from-black/70 to-transparent pointer-events-none"></div>

                    <!-- Right Action Rail -->
                    <div class="absolute right-0 bottom-24 w-20 z-30 pr-4">
                        <div class="flex flex-col items-center space-y-5">
                            <!-- Like -->
                            <button class="rail-btn like-btn flex flex-col items-center" data-video-id="{{ $video->id }}">
                                <svg class="heart-icon w-9 h-9 {{ $liked ? 'text-rose-500' : 'text-white' }}" viewBox="0 0 24 24"
                                     fill="{{ $liked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5">
                                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span class="likes-count text-white text-xs mt-1">{{ $video->likes_count }}</span>
                            </button>

                            <!-- Comments -->
                            <button class="rail-btn comments-btn flex flex-col items-center" data-video-id="{{ $video->id }}">
                                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span class="comments-count text-white text-xs mt-1">{{ $video->comments_count }}</span>
                            </button>

                            <!-- Share (copy link) -->
                            <button class="rail-btn share-btn flex flex-col items-center" data-url="{{ url('/feed') }}#v{{ $video->id }}">
                                <svg class="w-9 h-9 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 12h.01M7 17h.01M11 7h6M11 12h6M11 17h6"/>
                                </svg>
                                <span class="text-white text-xs mt-1">Share</span>
                            </button>
                        </div>
                    </div>

                    <!-- Bottom Meta -->
                    <div class="absolute left-0 right-20 bottom-0 z-30 p-4">
                        <div class="glass rounded-xl p-4">
                            <div class="flex items-start space-x-3">
                                @php $author = $video->user ?? null; $initial = $author?->name ? strtoupper(mb_substr($author->name,0,1)) : 'B'; @endphp
                                <div class="h-9 w-9 rounded-full bg-gradient-to-br {{ $brandGradient }} flex items-center justify-center text-black font-bold">{{ $initial }}</div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-white font-semibold truncate max-w-[60%]">{{ $author->name ?? 'BuzzClips' }}</span>
                                        <span class="text-white/60 text-xs">•</span>
                                        <span class="text-white/80 text-xs truncate">Featured</span>
                                    </div>
                                    <h3 class="text-white mt-1 text-sm font-medium">{{ $video->title }}</h3>
                                    <p class="text-white/80 text-xs mt-0.5 line-clamp-2">{{ $video->description }}</p>
                                </div>
                                @auth
                                    <button class="ml-2 hidden md:inline-flex items-center text-xs px-3 py-1 rounded-full border border-white/20 text-white/90 hover:text-white">Follow</button>
                                @endauth
                            </div>
                        </div>
                    </div>

                    <!-- Center tap indicator -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="play-pause-indicator hidden">
                            <div class="bg-black/50 rounded-full p-4">
                                <svg class="w-12 h-12 text-white play-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                <svg class="w-12 h-12 text-white pause-icon hidden" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Comments Drawer -->
                    <div class="comments-overlay-panel fixed inset-0 bg-black/70 hidden z-50 flex justify-center" data-video-id="{{ $video->id }}">
                        <div class="w-full max-w-[600px] relative">
                            <div class="absolute bottom-0 left-0 right-0 bg-gray-900 rounded-t-3xl max-h-[70vh] overflow-hidden border border-white/5">
                                <div class="p-4 border-b border-white/10 flex items-center justify-between">
                                    <h4 class="text-white font-semibold">Comments</h4>
                                    <button class="close-comments text-gray-400 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                </div>
                                <div class="comments-list max-h-[50vh] overflow-y-auto p-4 space-y-3"></div>
                                <div class="p-4 border-t border-white/10">
                                    <div class="flex space-x-3">
                                        <input type="text" class="comment-input flex-1 bg-gray-800 text-white rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500" placeholder="Add a comment...">
                                        <button class="send-comment bg-gradient-to-br {{ $brandGradient }} text-black rounded-full px-5 py-2 font-semibold">Post</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    $(function() {
        let currentVideo = null;

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // Smoothly determine which video is in view
        function playVideoInView() {
            const containers = $('.video-container');
            let bestIdx = 0, bestScore = -Infinity;
            const viewportH = $(window).height();
            containers.each(function(i){
                const rect = this.getBoundingClientRect();
                const visible = Math.max(0, Math.min(rect.bottom, viewportH) - Math.max(rect.top, 0));
                if (visible > bestScore) { bestScore = visible; bestIdx = i; }
            });
            containers.each(function(i){
                const video = $(this).find('.video-element')[0];
                if (i === bestIdx) { if (currentVideo && currentVideo!==video) { currentVideo.pause(); } currentVideo = video; video.play().catch(()=>{}); }
                else { video.pause(); }
            });
        }

        const debounced = (fn, wait)=>{ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), wait); } };
        $('.video-feed').on('scroll', debounced(playVideoInView, 80));
        $(window).on('resize', debounced(playVideoInView, 100));
        playVideoInView();

        // Tap to play/pause with indicator
        $(document).on('click', '.video-element', function(){
            const video = this;
            const indicator = $(this).closest('.video-container').find('.play-pause-indicator');
            if (video.paused) {
                video.play().catch(()=>{});
                indicator.find('.play-icon').removeClass('hidden');
                indicator.find('.pause-icon').addClass('hidden');
            } else {
                video.pause();
                indicator.find('.play-icon').addClass('hidden');
                indicator.find('.pause-icon').removeClass('hidden');
            }
            indicator.removeClass('hidden').delay(500).queue(function(){ $(this).addClass('hidden').dequeue(); });
        });

        // Like
        $(document).on('click', '.like-btn', function(){
            const $btn = $(this);
            const videoId = $btn.data('video-id');
            const heartIcon = $btn.find('.heart-icon');
            const likesCount = $btn.find('.likes-count');
            $.post(`/videos/${videoId}/like`)
                .done(function(r){
                    if (r.liked) {
                        heartIcon.removeClass('text-white').addClass('text-rose-500 heart-pop').attr('fill','currentColor');
                    } else {
                        heartIcon.removeClass('text-rose-500 heart-pop').addClass('text-white').attr('fill','none');
                    }
                    likesCount.text(r.likes_count);
                })
                .fail(function(xhr){ if (xhr.status===401){ window.location.href='/login'; }});
        });

        // Comments
        $(document).on('click', '.comments-btn', function(){
            const videoId = $(this).data('video-id');
            const overlay = $(`.comments-overlay-panel[data-video-id='${videoId}']`);
            loadComments(videoId);
            overlay.removeClass('hidden');
        });
        $(document).on('click', '.close-comments', function(){ $(this).closest('.comments-overlay-panel').addClass('hidden'); });
        $(document).on('click', '.send-comment', function(){
            const panel = $(this).closest('.comments-overlay-panel');
            const videoId = panel.data('video-id');
            const input = panel.find('.comment-input');
            const comment = input.val().trim(); if (!comment) return;
            $.post(`/videos/${videoId}/comment`, { comment })
                .done(function(resp){ if(resp.success){ input.val(''); loadComments(videoId); const btn=$(`.comments-btn[data-video-id='${videoId}']`); const cnt=btn.find('+ .comments-count'); cnt.text(parseInt(cnt.text()||'0')+1); } })
                .fail(function(xhr){ if (xhr.status===401){ window.location.href='/login'; }});
        });
        $(document).on('keypress', '.comment-input', function(e){ if (e.which===13) $(this).closest('.comments-overlay-panel').find('.send-comment').click(); });

        function loadComments(videoId){
            const list = $(`.comments-overlay-panel[data-video-id='${videoId}'] .comments-list`);
            list.html('<p class="text-white/40 text-center">Loading...</p>');
            $.get(`/videos/${videoId}/comments`).done(function(res){
                list.empty();
                if(!res.comments.length){ list.html('<p class="text-white/40 text-center">No comments yet. Be the first!</p>'); return; }
                res.comments.forEach(function(c){
                    const initial = (c.user_name||'?').charAt(0).toUpperCase();
                    list.append(`
                      <div class='flex space-x-3'>
                        <div class='w-8 h-8 bg-gradient-to-br from-violet-500 to-sky-500 rounded-full flex items-center justify-center text-black text-xs font-bold'>${initial}</div>
                        <div class='flex-1'>
                          <div class='flex items-center space-x-2'>
                            <span class='text-white font-semibold text-sm'>${c.user_name}</span>
                            <span class='text-white/40 text-xs'>${c.created_at}</span>
                          </div>
                          <p class='text-white/80 text-sm'>${$('<div>').text(c.comment).html()}</p>
                        </div>
                      </div>`);
                });
            }).fail(function(){ list.html('<p class="text-red-400 text-center">Failed to load comments</p>'); });
        }

        // Keyboard navigation
        $(document).on('keydown', function(e){
            const feed = $('.video-feed')[0];
            const vh = window.innerHeight;
            if (e.key === 'ArrowDown') { feed.scrollBy({ top: vh, behavior: 'smooth' }); }
            if (e.key === 'ArrowUp') { feed.scrollBy({ top: -vh, behavior: 'smooth' }); }
            if (e.key.toLowerCase() === 'l') { $('.video-container').filter(function(){ return this.getBoundingClientRect().top<=10 && this.getBoundingClientRect().bottom>=window.innerHeight-10; }).find('.like-btn').click(); }
            if (e.key.toLowerCase() === 'c') { $('.video-container').filter(function(){ return this.getBoundingClientRect().top<=10 && this.getBoundingClientRect().bottom>=window.innerHeight-10; }).find('.comments-btn').click(); }
        });

        // Share copy
        $(document).on('click', '.share-btn', function(){
            const url = $(this).data('url');
            navigator.clipboard?.writeText(url).then(()=>{
                const el = $('<div class="fixed top-4 left-1/2 -translate-x-1/2 bg-white/10 text-white px-3 py-1 rounded-full text-sm">Link copied</div>');
                $('body').append(el); setTimeout(()=>el.remove(), 1000);
            });
        });
    });
</script>
</body>
</html>
