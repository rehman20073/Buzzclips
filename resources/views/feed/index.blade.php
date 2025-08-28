<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuzzClips - Video Feed</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .video-container {
            scroll-snap-align: start;
        }
        
        .video-feed {
            scroll-snap-type: y mandatory;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* Internet Explorer 10+ */
        }
        
        .video-feed::-webkit-scrollbar {
            display: none; /* WebKit */
        }

        .heart-animation {
            animation: heartBeat 0.3s ease-in-out;
        }

        @keyframes heartBeat {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .comments-overlay {
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
        }

        .video-thumbnail {
            background-size: cover;
            background-position: center;
            filter: blur(2px);
            transition: filter 0.3s ease;
        }

        .video-loading {
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .video-loaded {
            opacity: 1;
        }

        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-900 overflow-hidden flex justify-center">
 
    <div class="w-full max-w-[600px] bg-black relative">
        <!-- Navigation Header -->
        <div class="absolute top-0 left-0 right-0 z-40 p-4 bg-gradient-to-b from-black/80 to-transparent">
            <div class="flex justify-between items-center">
                <h1 class="text-white font-bold text-xl">BuzzClips</h1>
                <div class="flex space-x-2">
                    @auth
                        @if(auth()->user()->isCreator())
                            <a href="/creator" class="bg-violet-500 hover:bg-violet-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                Creator Panel
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Sign Up
                        </a>
                        <a href="{{ route('auth.google.redirect') }}" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Google
                        </a>
                    @endauth
                </div>
            </div>
        </div>
        
        <div class="video-feed h-screen overflow-y-scroll snap-y snap-mandatory">
            @foreach($videos as $video)
            <div class="video-container relative h-screen w-full snap-start" data-video-id="{{ $video->id }}">
                <!-- Video Element -->
                <video 
                    class="video-element absolute inset-0 w-full h-full " 
                    src="{{ $video->getVideo() }}"
                    loop 
                    muted
                    autoplay
                    preload="metadata"
                    playsinline
                    webkit-playsinline
                    crossorigin="anonymous"
                    data-video-id="{{ $video->id }}">
                </video>            <!-- Overlay Controls -->
            <div class="absolute inset-0 flex">
                <!-- Left side - tap to play/pause -->
                <div class="flex-1 flex items-center justify-center">
                    <div class="play-pause-indicator hidden">
                        <div class="bg-black bg-opacity-50 rounded-full p-4">
                            <svg class="w-12 h-12 text-white play-icon" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            <svg class="w-12 h-12 text-white pause-icon hidden" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Right side - controls -->
                <div class="w-20 flex flex-col justify-end pb-20 pr-4 space-y-6">
                    <!-- Like Button -->
                    <div class="flex flex-col items-center">
                        <button class="like-btn p-3 rounded-full bg-black bg-opacity-30 hover:bg-opacity-50 transition-all duration-200" 
                                data-video-id="{{ $video->id }}">
                            <svg class="w-8 h-8 heart-icon {{ $video->is_liked_by_user ? 'text-red-500' : 'text-white' }}" 
                                 fill="{{ $video->is_liked_by_user ? 'currentColor' : 'none' }}" 
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        <span class="likes-count text-white text-sm mt-1">{{ $video->likes_count }}</span>
                    </div>
                    
                    <!-- Comments Button -->
                    <div class="flex flex-col items-center">
                        <button class="comments-btn p-3 rounded-full bg-black bg-opacity-30 hover:bg-opacity-50 transition-all duration-200" 
                                data-video-id="{{ $video->id }}">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </button>
                        <span class="comments-count text-white text-sm mt-1">{{ $video->comments_count }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Info -->
            <div class="absolute bottom-0 left-0 right-20 p-4 comments-overlay">
                <div class="text-white">
                    <h3 class="font-bold text-lg mb-2">{{ $video->title }}</h3>
                    <p class="text-sm opacity-90">{{ $video->description }}</p>
                </div>
            </div>
            
            <!-- Comments Overlay -->
            <div class="comments-overlay-panel fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex justify-center" data-video-id="{{ $video->id }}">
                <div class="w-full max-w-[600px] relative">
                    <div class="absolute bottom-0 left-0 right-0 bg-gray-900 rounded-t-3xl max-h-96 overflow-hidden">
                        <div class="p-4 border-b border-gray-700">
                            <div class="flex justify-between items-center">
                                <h4 class="text-white font-semibold">Comments</h4>
                                <button class="close-comments text-gray-400 hover:text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="comments-list max-h-64 overflow-y-auto p-4 space-y-3">
                            <!-- Comments will be loaded here -->
                        </div>
                        
                        <div class="p-4 border-t border-gray-700">
                            <div class="flex space-x-3">
                                <input type="text" class="comment-input flex-1 bg-gray-800 text-white rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Add a comment...">
                                <button class="send-comment bg-blue-500 hover:bg-blue-600 text-white rounded-full px-6 py-2 font-semibold">Post</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let currentVideo = null;
            
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Initialize first video
            playVideoInView();
            
            // Debounce function to limit scroll event frequency
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
            
            // Handle scroll to play/pause videos with debouncing
            const debouncedPlayVideo = debounce(playVideoInView, 100);
            
            $('.video-feed').on('scroll', function() {
                debouncedPlayVideo();
            });
            
            // Also handle when scroll snap settles
            $('.video-feed').on('scrollend', function() {
                playVideoInView();
            });
            
            function playVideoInView() {
                const containers = $('.video-container');
                const feedElement = $('.video-feed')[0];
                const scrollTop = feedElement.scrollTop;
                const containerHeight = $(window).height(); // Each container is full height
                
                console.log('Scroll position:', scrollTop); // Debug log
                
                // Calculate which video should be playing based on scroll position
                const currentIndex = Math.round(scrollTop / containerHeight);
                console.log('Current video index:', currentIndex); // Debug log
                
                containers.each(function(index) {
                    const container = $(this);
                    const video = container.find('.video-element')[0];
                    
                    if (index === currentIndex) {
                        // This video should be playing
                        if (currentVideo && currentVideo !== video) {
                            console.log('Pausing previous video and playing new one'); // Debug log
                            currentVideo.pause();
                            currentVideo.currentTime = 0;
                        }
                        currentVideo = video;
                        video.play().catch(e => console.log('Autoplay prevented:', e));
                        console.log('Playing video', index); // Debug log
                    } else {
                        // Pause all other videos
                        video.pause();
                        console.log('Pausing video', index); // Debug log
                    }
                });
            }
            
            // Handle video tap to play/pause
            $('.video-element').on('click', function() {
                const video = this;
                const container = $(this).closest('.video-container');
                const indicator = container.find('.play-pause-indicator');
                
                if (video.paused) {
                    video.play().catch(e => console.log('Play failed:', e));
                    indicator.find('.play-icon').removeClass('hidden');
                    indicator.find('.pause-icon').addClass('hidden');
                } else {
                    video.pause();
                    indicator.find('.play-icon').addClass('hidden');
                    indicator.find('.pause-icon').removeClass('hidden');
                }
                
                indicator.removeClass('hidden').delay(500).queue(function() {
                    $(this).addClass('hidden').dequeue();
                });
            });
            
            // Handle like button
            $('.like-btn').on('click', function() {
                @auth
                    const videoId = $(this).data('video-id');
                    const heartIcon = $(this).find('.heart-icon');
                    const likesCount = $(this).siblings('.likes-count');
                    
                    console.log('Like button clicked for video:', videoId); // Debug log
                    
                    $.post(`/videos/${videoId}/like`)
                        .done(function(response) {
                            console.log('Like response:', response); // Debug log
                            if (response.liked) {
                                // User just liked the video
                                heartIcon.removeClass('text-white')
                                        .addClass('text-red-500 heart-animation')
                                        .attr('fill', 'currentColor')
                                        .attr('stroke', 'currentColor');
                            } else {
                                // User just unliked the video
                                heartIcon.removeClass('text-red-500 heart-animation')
                                        .addClass('text-white')
                                        .attr('fill', 'none')
                                        .attr('stroke', 'currentColor');
                            }
                            likesCount.text(response.likes_count);
                        })
                        .fail(function(xhr) {
                            console.error('Like failed:', xhr); // Debug log
                            if (xhr.status === 401) {
                                alert('Please login to like videos');
                                window.location.href = '/login';
                            } else {
                                console.error('Failed to toggle like');
                            }
                        });
                @else
                    alert('Please login to like videos');
                    window.location.href = '/login';
                @endauth
            });
            
            // Handle comments button
            $('.comments-btn').on('click', function() {
                const videoId = $(this).data('video-id');
                const overlay = $(`.comments-overlay-panel[data-video-id="${videoId}"]`);
                
                console.log('Comments button clicked for video:', videoId); // Debug log
                
                // Load comments
                loadComments(videoId);
                overlay.removeClass('hidden');
            });
            
            // Close comments
            $('.close-comments').on('click', function() {
                $(this).closest('.comments-overlay-panel').addClass('hidden');
            });
            
            // Send comment
            $('.send-comment').on('click', function() {
                @auth
                    const panel = $(this).closest('.comments-overlay-panel');
                    const videoId = panel.data('video-id');
                    const input = panel.find('.comment-input');
                    const comment = input.val().trim();
                    
                    console.log('Send comment clicked for video:', videoId, 'comment:', comment); // Debug log
                    
                    if (!comment) return;
                    
                    $.post(`/videos/${videoId}/comment`, { comment: comment })
                        .done(function(response) {
                            console.log('Comment response:', response); // Debug log
                            if (response.success) {
                                input.val('');
                                loadComments(videoId); // Reload comments
                                
                                // Update comments count
                                $(`.comments-btn[data-video-id="${videoId}"]`).siblings('.comments-count').text(function(i, text) {
                                    return parseInt(text) + 1;
                                });
                            }
                        })
                        .fail(function(xhr) {
                            console.error('Comment failed:', xhr); // Debug log
                            if (xhr.status === 401) {
                                alert('Please login to comment');
                                window.location.href = '/login';
                            } else {
                                console.error('Failed to post comment');
                            }
                        });
                @else
                    alert('Please login to comment');
                    window.location.href = '/login';
                @endauth
            });
            
            // Handle Enter key in comment input
            $('.comment-input').on('keypress', function(e) {
                if (e.which === 13) {
                    $(this).siblings('.send-comment').click();
                }
            });
            
            function loadComments(videoId) {
                const commentsList = $(`.comments-overlay-panel[data-video-id="${videoId}"] .comments-list`);
                
                console.log('Loading comments for video:', videoId); // Debug log
                
                $.get(`/videos/${videoId}/comments`)
                    .done(function(response) {
                        console.log('Comments loaded:', response); // Debug log
                        commentsList.empty();
                        
                        if (response.comments.length === 0) {
                            commentsList.html('<p class="text-gray-400 text-center">No comments yet. Be the first to comment!</p>');
                        } else {
                            response.comments.forEach(function(comment) {
                                const commentHtml = `
                                    <div class="flex space-x-3">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-semibold">${comment.user_name.charAt(0).toUpperCase()}</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-white font-semibold text-sm">${comment.user_name}</span>
                                                <span class="text-gray-400 text-xs">${comment.created_at}</span>
                                            </div>
                                            <p class="text-gray-300 text-sm">${comment.comment}</p>
                                        </div>
                                    </div>
                                `;
                                commentsList.append(commentHtml);
                            });
                        }
                    })
                    .fail(function(xhr) {
                        console.error('Failed to load comments:', xhr); // Debug log
                        commentsList.html('<p class="text-red-400 text-center">Failed to load comments</p>');
                    });
            }
        });
    </script>
</body>
</html>
</html>
