@extends('frontend.layout.main')
@section('title', '500 - Internal Server Error | Makoons')
@section('meta_description', 'An internal server error occurred on Makoons. We are working to fix this as soon as possible.')
@section('body_class', 'error-page-body')

@section('content')
<main class="error-page-container">
    <div class="container-xl error-content-wrapper">
        <div class="error-graphic-container">
            <!-- Playful 500 SVG Cartoon/Illustration (Blocks tumbled over/sleeping character) -->
            <svg class="error-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 350" width="100%" height="100%">
                <defs>
                    <linearGradient id="block1" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#48dbfb" />
                        <stop offset="100%" stop-color="#0abde3" />
                    </linearGradient>
                    <linearGradient id="block2" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#ff6b6b" />
                        <stop offset="100%" stop-color="#ee5253" />
                    </linearGradient>
                    <linearGradient id="block3" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#1dd1a1" />
                        <stop offset="100%" stop-color="#10ac84" />
                    </linearGradient>
                    <linearGradient id="block4" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#ff9f43" />
                        <stop offset="100%" stop-color="#f39c12" />
                    </linearGradient>
                </defs>

                <!-- Tumbled Toy Blocks representing "500" -->
                <!-- Blue block 1 -->
                <rect x="80" y="140" width="70" height="70" rx="10" transform="rotate(-15 115 175)" fill="url(#block1)" />
                <text x="115" y="195" font-family="'Fredoka One', 'Nunito', sans-serif" font-size="50" fill="#fff" text-anchor="middle" transform="rotate(-15 115 175)">5</text>

                <!-- Red block 2 -->
                <rect x="190" y="120" width="70" height="70" rx="10" transform="rotate(10 225 155)" fill="url(#block2)" />
                <text x="225" y="175" font-family="'Fredoka One', 'Nunito', sans-serif" font-size="50" fill="#fff" text-anchor="middle" transform="rotate(10 225 155)">0</text>

                <!-- Orange block 3 -->
                <rect x="300" y="150" width="70" height="70" rx="10" transform="rotate(-5 335 185)" fill="url(#block4)" />
                <text x="335" y="205" font-family="'Fredoka One', 'Nunito', sans-serif" font-size="50" fill="#fff" text-anchor="middle" transform="rotate(-5 335 185)">0</text>

                <!-- Green Floor/Platform -->
                <path d="M 50,280 Q 250,290 450,280" fill="none" stroke="var(--makoons-green)" stroke-width="6" stroke-linecap="round" />
                
                <!-- Little stars/sparks showing the "crash" -->
                <path d="M 180,90 L 185,80 L 190,90 L 200,95 L 190,100 L 185,110 L 180,100 L 170,95 Z" fill="#ff9f43" opacity="0.8"/>
                <path d="M 290,95 L 293,87 L 301,90 L 295,96 L 297,104 L 290,99 L 283,104 L 285,96 L 279,90 L 287,87 Z" fill="#1dd1a1" opacity="0.8"/>
            </svg>
        </div>

        <div class="error-text-container">
            <span class="eyebrow-badge alert-badge">Error 500</span>
            <h2 class="error-title">Our toy tower tipped over!</h2>
            <p class="error-description">Something went wrong on our server (Internal Server Error). Don't worry, our team of teachers is already tidying up the playroom to get everything back in place.</p>
            <div class="error-actions">
                <a href="{{ route('home') }}" class="btn-error-primary">Back to Home</a>
                <a href="javascript:window.location.reload();" class="btn-error-secondary">Try Refreshing</a>
            </div>
        </div>
    </div>
</main>
@endsection
