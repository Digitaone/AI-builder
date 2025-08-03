<?php
// SEO Variables
$page_title = 'About Us';
$meta_description = 'Learn about the mission, team, and passion behind WanderChicVibes, your favorite guide to travel, fashion, and lifestyle.';

include '../templates/header.php';
?>

<div class="bg-white p-8 rounded-lg shadow-lg max-w-4xl mx-auto">
    <header class="text-center mb-12">
        <h1 class="text-5xl font-montserrat font-bold text-gray-800">About WanderChicVibes</h1>
        <p class="mt-4 text-lg text-gray-600">Discover the story and passion behind our community.</p>
    </header>

    <!-- Mission Section -->
    <section id="mission" class="mb-12">
        <h2 class="text-3xl font-montserrat font-bold mb-4">Our Mission</h2>
        <p class="text-gray-700 leading-relaxed">
            At WanderChicVibes, we believe that life is an adventure meant to be lived with style, curiosity, and mindfulness. Our mission is to inspire you to explore the world, embrace your personal style, and cultivate a life of well-being. We provide thoughtful articles, practical tips, and beautiful inspiration for the modern individual who seeks to blend travel, fashion, and a healthy lifestyle seamlessly.
        </p>
    </section>

    <!-- Team Section -->
    <section id="team" class="mb-12">
        <h2 class="text-3xl font-montserrat font-bold mb-4">Our Team</h2>
        <p class="text-gray-700 leading-relaxed">
            WanderChicVibes was founded by a group of passionate writers, photographers, and creatives who share a common love for storytelling. Our team travels the globe, attends fashion weeks, and consults with wellness experts to bring you authentic, high-quality content that you can trust and enjoy. We are dreamers and doers, dedicated to building a vibrant community of like-minded individuals.
        </p>
    </section>

    <!-- Collaboration Section -->
    <section id="collaboration">
        <h2 class="text-3xl font-montserrat font-bold mb-4">Work With Us</h2>
        <p class="text-gray-700 leading-relaxed">
            Are you a brand, a fellow blogger, or a creative professional? We are always open to collaborations, guest posts, and partnerships that align with our values. If you have an idea, we'd love to hear from you!
        </p>
        <a href="/contact" class="inline-block mt-4 bg-gray-800 text-white font-semibold px-6 py-3 rounded-lg hover:bg-gray-700">Get in Touch</a>
    </section>

</div>

<?php include '../templates/footer.php'; ?>
