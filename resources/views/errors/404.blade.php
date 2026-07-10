@extends('frontend.layout.main')
@section('title', 'Page Not Found | Makoons')
@section('meta_description', 'The page you are looking for does not exist or has been moved. Return to the Makoons home page.')
@section('body_class', 'error-page-body')

@section('content')
<main class="error-page-container">
    <div class="container-xl error-content-wrapper">
        <div class="error-graphic-container">
            <!-- Playful 404 SVG Illustration with Balloons and Clouds -->
            <svg class="error-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 350" width="100%" height="100%">
                <defs>
                    <linearGradient id="cloudGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#ffffff" />
                        <stop offset="100%" stop-color="#f0f5ff" />
                    </linearGradient>
                    <linearGradient id="balloon1" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#ff6b6b" />
                        <stop offset="100%" stop-color="#ee5253" />
                    </linearGradient>
                    <linearGradient id="balloon2" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#48dbfb" />
                        <stop offset="100%" stop-color="#0abde3" />
                    </linearGradient>
                    <linearGradient id="balloon3" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#ff9f43" />
                        <stop offset="100%" stop-color="#f39c12" />
                    </linearGradient>
                </defs>

                <!-- Clouds Background -->
                <path d="M 50,250 A 40,40 0 0,1 110,220 A 50,50 0 0,1 190,200 A 45,45 0 0,1 250,230 A 40,40 0 0,1 310,225 A 50,50 0 0,1 390,210 A 40,40 0 0,1 450,250 Z" fill="url(#cloudGrad)" opacity="0.6"/>
                <path d="M 20,280 A 30,30 0 0,1 70,250 A 40,40 0 0,1 140,240 A 35,35 0 0,1 200,260 A 30,30 0 0,1 250,255 A 40,40 0 0,1 320,245 A 30,30 0 0,1 380,280 Z" fill="url(#cloudGrad)" opacity="0.4"/>

                <!-- Balloon Strings -->
                <path d="M 170,170 Q 210,210 240,250" fill="none" stroke="#bdc5d8" stroke-width="2.5" stroke-dasharray="4,4" />
                <path d="M 250,150 Q 250,210 250,250" fill="none" stroke="#bdc5d8" stroke-width="2.5" stroke-dasharray="4,4" />
                <path d="M 330,180 Q 290,220 260,250" fill="none" stroke="#bdc5d8" stroke-width="2.5" stroke-dasharray="4,4" />

                <!-- Balloons -->
                <!-- Balloon 1 (Left - Red) -->
                <g class="floating-balloon-left">
                    <ellipse cx="170" cy="110" rx="35" ry="45" fill="url(#balloon1)" />
                    <polygon points="170,155 165,162 175,162" fill="#ee5253" />
                </g>

                <!-- Balloon 2 (Middle - Blue) -->
                <g class="floating-balloon-middle">
                    <ellipse cx="250" cy="90" rx="40" ry="50" fill="url(#balloon2)" />
                    <polygon points="250,140 245,147 255,147" fill="#0abde3" />
                </g>

                <!-- Balloon 3 (Right - Orange) -->
                <g class="floating-balloon-right">
                    <ellipse cx="330" cy="120" rx="32" ry="42" fill="url(#balloon3)" />
                    <polygon points="330,162 325,169 335,169" fill="#f39c12" />
                </g>

                <!-- Large "404" text integrated with illustration -->
                <text x="250" y="270" font-family="'Fredoka One', 'Nunito', sans-serif" font-size="120" font-weight="bold" fill="var(--ink)" text-anchor="middle" letter-spacing="-5">404</text>

                <!-- Little ground grass tufts -->
                <path d="M 210,310 L 215,300 L 220,310 M 215,310 L 218,297 L 222,310" stroke="#72b843" stroke-width="2" stroke-linecap="round"/>
                <path d="M 270,312 L 274,303 L 278,312" stroke="#72b843" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>

        <div class="error-text-container">
            <span class="eyebrow-badge">Whoops!</span>
            <h2 class="error-title">This page went on a field trip!</h2>
            <p class="error-description">We couldn't find the page you were looking for. It might have been moved, renamed, or perhaps it never existed in the first place.</p>
            <div class="error-actions">
                <a href="{{ route('home') }}" class="btn-error-primary">Back to Home</a>
                <a href="{{ route('blogs') }}" class="btn-error-secondary">Explore Blogs</a>
            </div>
        </div>
    </div>
</main>
@endsection
