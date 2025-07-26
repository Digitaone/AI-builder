document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Simple animation for featured posts on scroll
    const featuredPosts = document.querySelector('.featured-posts');
    if (featuredPosts) {
        window.addEventListener('scroll', () => {
            const rect = featuredPosts.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom >= 0) {
                featuredPosts.style.opacity = 1;
                featuredPosts.style.transform = 'translateY(0)';
            }
        });
    }
});
