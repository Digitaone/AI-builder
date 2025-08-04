document.addEventListener('DOMContentLoaded', () => {
    // App state
    let allPosts = [];

    // Element selectors
    const themeToggleButton = document.querySelector('.theme-toggle');
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const newsletterForm = document.getElementById('newsletter-form');
    const newsletterFeedback = document.getElementById('newsletter-feedback');

    /**
     * Initializes the application.
     */
    const init = async () => {
        setupEventListeners();
        try {
            allPosts = await fetchBlogPosts();

            // Check for homepage sections
            if (document.getElementById('featured-posts')) {
                renderFeaturedPosts(allPosts);
                injectSchema_WebSite();
            }
            if (document.getElementById('category-highlights')) {
                renderCategoryHighlights(allPosts);
            }

            // Check for single post page section
            if (document.getElementById('post-main')) {
                renderPostPage(allPosts);
            }

            // Check for category page
            const category = document.body.dataset.category;
            if (category) {
                renderCategoryPage(allPosts, category);
            }


        } catch (error) {
            console.error('Initialization failed:', error);
        }
    };

    /**
     * Fetches blog posts from the JSON file.
     * @returns {Promise<Array>} A promise that resolves to an array of post objects.
     */
    const fetchBlogPosts = async () => {
        try {
            const response = await fetch('data/blogs.json');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Failed to fetch blog posts:', error);
            // In a real app, you might want to display a user-friendly error message.
            return []; // Return empty array on failure
        }
    };

    /**
     * Sets up all event listeners for the application.
     */
    const setupEventListeners = () => {
        // Theme toggle
        if (themeToggleButton) {
            themeToggleButton.addEventListener('click', toggleTheme);
        }

        // Mobile navigation toggle
        if (navToggle && navMenu) {
            navToggle.addEventListener('click', toggleMobileNav);
        }

        // Newsletter form submission
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', handleNewsletterSignup);
        }

        // Contact form submission
        const contactForm = document.getElementById('contact-form');
        if (contactForm) {
            contactForm.addEventListener('submit', handleContactFormSubmit);
        }
    };

    /**
     * Toggles the color theme between light and dark mode.
     */
    const toggleTheme = () => {
        document.documentElement.classList.toggle('dark-mode');
        const isDarkMode = document.documentElement.classList.contains('dark-mode');
        localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
    };

    /**
     * Toggles the mobile navigation menu.
     */
    const toggleMobileNav = () => {
        navMenu.classList.toggle('active');
        const isExpanded = navMenu.classList.contains('active');
        navToggle.setAttribute('aria-expanded', isExpanded);
    };

    /**
     * Handles the newsletter signup form submission.
     * @param {Event} e - The form submission event.
     */
    const handleNewsletterSignup = (e) => {
        e.preventDefault();
        const emailInput = document.getElementById('newsletter-email');
        const email = emailInput.value.trim();

        if (email) {
            // In a real application, this would be sent to a server.
            // For this project, we'll just save it to localStorage.
            localStorage.setItem('newsletter_signup', email);

            if(newsletterFeedback) {
                newsletterFeedback.textContent = 'Thank you for subscribing!';
                newsletterFeedback.style.color = 'var(--secondary-accent-color)';
            }
            emailInput.value = '';

            setTimeout(() => {
                if(newsletterFeedback) newsletterFeedback.textContent = '';
            }, 5000);
        }
    };

    /**
     * Renders a grid of post cards into a specified container.
     * @param {Array} posts - An array of post objects to render.
     * @param {HTMLElement} container - The container element to render into.
     */
    const renderPostGrid = (posts, container) => {
        if (posts.length === 0) {
            container.innerHTML = '<p>No posts found in this category yet.</p>';
            return;
        }

        container.innerHTML = posts.map(post => `
            <article class="post-card">
                <a href="post.html?id=${post.id}" class="card-link" aria-label="Read more about ${post.title}">
                    <img src="${post.image}" alt="" class="post-card-image" loading="lazy">
                    <div class="post-card-content">
                        <span class="post-card-category">${post.category}</span>
                        <h3 class="post-card-title">${post.title}</h3>
                        <p>${post.content.substring(0, 100).trim()}...</p>
                        <span class="read-more">Read More &rarr;</span>
                    </div>
                </a>
            </article>
        `).join('');
    };

    /**
     * Renders featured posts on the homepage.
     * @param {Array} posts - The array of all blog posts.
     */
    const renderFeaturedPosts = (posts) => {
        const container = document.querySelector('#featured-posts .post-grid');
        if (!container) return;

        const featured = posts.sort((a, b) => b.id - a.id).slice(0, 3);
        renderPostGrid(featured, container);
    };

    /**
     * Renders all posts for a specific category page.
     * @param {Array} posts - The array of all blog posts.
     * @param {string} category - The category to filter by.
     */
    const renderCategoryPage = (posts, category) => {
        const container = document.querySelector('#category-main .post-grid');
        if (!container) return;

        const categoryPosts = posts.filter(post => post.category === category);
        renderPostGrid(categoryPosts, container);
        injectSchema_CollectionPage(category);
    };

    /**
     * Renders unique category links based on the available posts.
     * @param {Array} posts - An array of post objects to extract categories from.
     */
    const renderCategoryHighlights = (posts) => {
        const container = document.querySelector('#category-highlights .category-list');
        if (!container) return;

        const categories = [...new Set(posts.map(post => post.category))];

        // Ensure "Food" is included if not in the main set, as per requirements
        if (!categories.includes('Food')) {
            categories.push('Food');
        }

        container.innerHTML = categories.map(category => `
            <a href="${category.toLowerCase()}.html" class="category-card">
                ${category}
            </a>
        `).join('');
    };


    /**
     * Orchestrates the rendering of the single post page.
     * @param {Array} posts - The array of all blog posts.
     */
    const renderPostPage = (posts) => {
        const params = new URLSearchParams(window.location.search);
        const postId = parseInt(params.get('id'), 10);

        if (isNaN(postId)) {
            displayPostError('Invalid post ID provided.');
            return;
        }

        const post = posts.find(p => p.id === postId);

        if (!post) {
            displayPostError('Post not found. It may have been moved or deleted.');
            document.title = "Post Not Found - WanderChicVibes";
            return;
        }

        updateMetaTags(post);
        renderSinglePost(post);
        injectSchema_BlogPosting(post);
        renderComments(post);

        const commentForm = document.getElementById('comment-form');
        if (commentForm) {
            commentForm.addEventListener('submit', (e) => {
                e.preventDefault();
                handleCommentSubmission(post);
            });
        }
    };

    /**
     * Renders the content of a single post into the page.
     * @param {object} post - The post object to render.
     */
    const renderSinglePost = (post) => {
        const container = document.querySelector('.post-full');
        if (!container) return;

        const postDate = new Date(post.date);
        postDate.setDate(postDate.getDate() + 1); // Adjust for timezone issues
        const formattedDate = postDate.toLocaleDateString('en-US', {
            year: 'numeric', month: 'long', day: 'numeric'
        });

        const postHtml = `
            <header class="post-full-header">
                <h1 class="post-full-title">${post.title}</h1>
                <div class="post-full-meta">
                    <time datetime="${post.date}">${formattedDate}</time>
                    <span class="bull">&bull;</span>
                    <span>By ${post.author}</span>
                    <span class="bull">&bull;</span>
                    <a href="${post.category.toLowerCase()}.html">${post.category}</a>
                </div>
            </header>
            <figure class="post-full-image">
                <img src="${post.image}" alt="${post.title}">
            </figure>
            <section class="post-full-content">
                <p>${post.content.replace(/\n\n/g, '</p><p>').replace(/\n/g, '<br>')}</p>
            </section>
        `;
        container.innerHTML = postHtml;
    };

    /**
     * Displays an error message in the main post container.
     * @param {string} message - The error message to display.
     */
    const displayPostError = (message) => {
        const container = document.querySelector('.post-full');
        if (!container) return;
        container.innerHTML = `<p class="error" style="text-align: center; padding: 4rem 0;">${message}</p>`;
    };

    /**
     * Updates the page's meta tags for SEO based on the post content.
     * @param {object} post - The post object.
     */
    const updateMetaTags = (post) => {
        const pageTitle = `${post.title} - WanderChicVibes`;
        document.title = pageTitle;

        const description = post.content.substring(0, 160).trim() + '...';

        // Standard description
        document.querySelector('meta[name="description"]')?.setAttribute('content', description);

        // Open Graph
        document.querySelector('meta[property="og:title"]')?.setAttribute('content', pageTitle);
        document.querySelector('meta[property="og:description"]')?.setAttribute('content', description);
        document.querySelector('meta[property="og:image"]')?.setAttribute('content', post.image);
        document.querySelector('meta[property="og:url"]')?.setAttribute('content', window.location.href);

        // Twitter Card
        document.querySelector('meta[property="twitter:title"]')?.setAttribute('content', pageTitle);
        document.querySelector('meta[property="twitter:description"]')?.setAttribute('content', description);
        document.querySelector('meta[property="twitter:image"]')?.setAttribute('content', post.image);
    };

    /**
     * Renders the comments for a given post.
     * @param {object} post - The post object.
     */
    const renderComments = (post) => {
        const container = document.getElementById('comments-list');
        if (!container) return;

        const staticComments = post.comments || [];

        const storedComments = JSON.parse(localStorage.getItem(`comments_post_${post.id}`)) || [];

        const allComments = [...staticComments, ...storedComments].sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));

        if (allComments.length === 0) {
            container.innerHTML = '<p>No comments yet. Be the first to comment!</p>';
            return;
        }

        container.innerHTML = allComments.map(comment => `
            <div class="comment">
                <p class="comment-meta">
                    <strong>${comment.username}</strong> on
                    <time datetime="${comment.timestamp}">${new Date(comment.timestamp).toLocaleDateString()}</time>
                </p>
                <p>${comment.text}</p>
            </div>
        `).join('');
    };

    /**
     * Handles the submission of a new comment.
     * @param {object} post - The post to which the comment is being added.
     */
    const handleCommentSubmission = (post) => {
        const nameInput = document.getElementById('comment-name');
        const textInput = document.getElementById('comment-text');

        if (nameInput.value.trim() === '' || textInput.value.trim() === '') {
            // Simple validation
            return;
        }

        const newComment = {
            username: nameInput.value.trim(),
            timestamp: new Date().toISOString(),
            text: textInput.value.trim()
        };

        const key = `comments_post_${post.id}`;
        const existingComments = JSON.parse(localStorage.getItem(key)) || [];
        existingComments.push(newComment);
        localStorage.setItem(key, JSON.stringify(existingComments));

        renderComments(post); // Re-render the comments list
        nameInput.value = '';
        textInput.value = '';
    };

    /**
     * Handles the contact form submission.
     * @param {Event} e - The form submission event.
     */
    const handleContactFormSubmit = (e) => {
        e.preventDefault();
        const form = e.target;
        const feedbackEl = document.getElementById('contact-feedback');

        // Basic validation
        if (form.name.value.trim() === '' || form.email.value.trim() === '' || form.message.value.trim() === '') {
            feedbackEl.textContent = 'Please fill out all fields.';
            feedbackEl.style.color = 'red';
            return;
        }

        // On success:
        feedbackEl.textContent = 'Thank you for your message! We will get back to you shortly.';
        feedbackEl.style.color = 'var(--secondary-accent-color)';
        form.reset();

        setTimeout(() => {
            feedbackEl.textContent = '';
        }, 5000);
    };

    /**
     * Injects BlogPosting JSON-LD schema into the head of the document.
     * @param {object} post - The post object for which to generate schema.
     */
    const injectSchema_BlogPosting = (post) => {
        // Remove any existing blog posting schema
        const existingSchema = document.querySelector('script[data-schema="blog-posting"]');
        if (existingSchema) {
            existingSchema.remove();
        }

        const schema = {
            "@context": "https://schema.org",
            "@type": "BlogPosting",
            "headline": post.title,
            "description": post.content.substring(0, 200).trim() + '...',
            "image": post.image, // In a real site, this should be an absolute URL
            "author": {
                "@type": "Person",
                "name": post.author
            },
            "publisher": {
                "@type": "Organization",
                "name": "WanderChicVibes",
                "logo": {
                    "@type": "ImageObject",
                    "url": "assets/logo.png" // Placeholder logo path
                }
            },
            "datePublished": post.date,
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": window.location.href
            }
        };

        const script = document.createElement('script');
        script.type = 'application/ld+json';
        script.setAttribute('data-schema', 'blog-posting');
        script.textContent = JSON.stringify(schema, null, 2);
        document.head.appendChild(script);
    };

    /**
     * Injects WebSite JSON-LD schema into the head of the document.
     */
    const injectSchema_WebSite = () => {
        const schema = {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "url": "https://wanderchicvibes.com/",
            "name": "WanderChicVibes",
            "potentialAction": {
                "@type": "SearchAction",
                "target": {
                    "@type": "EntryPoint",
                    "urlTemplate": "https://wanderchicvibes.com/search.html?q={search_term_string}"
                },
                "query-input": "required name=search_term_string"
            }
        };

        const script = document.createElement('script');
        script.type = 'application/ld+json';
        script.textContent = JSON.stringify(schema, null, 2);
        document.head.appendChild(script);
    };

    /**
     * Injects CollectionPage JSON-LD schema into the head of the document.
     * @param {string} categoryName - The name of the category for the page.
     */
    const injectSchema_CollectionPage = (categoryName) => {
        const schema = {
            "@context": "https://schema.org",
            "@type": "CollectionPage",
            "name": `Category: ${categoryName}`,
            "description": `Find all posts in the ${categoryName} category on WanderChicVibes.`,
            "url": window.location.href
        };

        const script = document.createElement('script');
        script.type = 'application/ld+json';
        script.textContent = JSON.stringify(schema, null, 2);
        document.head.appendChild(script);
    };

    // Start the application
    init();
});
