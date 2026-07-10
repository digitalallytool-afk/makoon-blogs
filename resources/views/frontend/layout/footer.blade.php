    <footer class="site-footer" id="contact">
        <div class="container-xl footer-grid">
            <div>
                <img src="{{ asset('frontend/images') }}/makoons-logo.png" alt="Makoons logo" class="footer-logo">
                <p>Pre-school · Daycare · Activity Centre</p>
            </div>

            <div>
                <h3>Quick Links</h3>
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('about') }}">About us</a>
                <a href="{{ route('blogs') }}">Blogs</a>
            </div>

            <div>
                <h3>Explore</h3>
                <a href="{{ route('stories') }}">Stories</a>
                <a href="{{ route('printables') }}">Printables</a>
                <a href="{{ route('sessions') }}">Sessions</a>
            </div>

            <div class="newsletter-block">
                <h3>Newsletter</h3>
                <p>A short note when we publish a useful blog post.</p>
                <form class="newsletter-form" action="#" method="POST">
                    <input type="email" name="email" placeholder="Your email address" aria-label="Your email address" required>
                    <button type="submit">Join</button>
                </form>
            </div>
        </div>
        <div class="container-xl footer-bottom">
            <span>© 2026 Makoons. All rights reserved.</span>
            <span>Privacy · Terms</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="{{ asset('frontend/js/script.js') }}?v=1.0.9"></script>
    </body>

    </html>
