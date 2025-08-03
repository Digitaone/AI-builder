<?php
// SEO Variables
$page_title = 'Home';
$meta_description = 'Your ultimate guide to travel, fashion, and mindful living. Explore featured posts and get inspired.';

// For development purposes, we'll use a hardcoded array of posts.
$featured_posts = [
    ['id' => 1, 'title' => 'A Chic Guide to a Weekend in Paris', 'category' => 'Travel', 'author_name' => 'Jane Doe', 'content' => 'Paris is always a good idea...', 'featured_image_url' => 'https://images.unsplash.com/photo-1502602898657-3e91760c0337?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=1600', 'slug' => 'a-chic-guide-to-a-weekend-in-paris'],
    ['id' => 2, 'title' => '5 Wellness Habits to Transform Your Morning Routine', 'category' => 'Lifestyle', 'author_name' => 'John Smith', 'content' => 'Elevate your mornings with these five simple wellness habits...', 'featured_image_url' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17025?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=1600', 'slug' => '5-wellness-habits-to-transform-your-morning-routine'],
    ['id' => 3, 'title' => 'Autumn Fashion Trends You Need to Know', 'category' => 'Fashion', 'author_name' => 'Emily White', 'content' => 'As the leaves change, so does our wardrobe...', 'featured_image_url' => 'https://images.unsplash.com/photo-1574271146353-8317a53f8538?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=1600', 'slug' => 'autumn-fashion-trends-you-need-to-know']
];

include '../templates/header.php';
?>

<!-- 1. Hero Section -->
<section class="hero bg-cover bg-center h-96 text-white flex items-center justify-center text-center" style="background-image: url('https://images.unsplash.com/photo-1470770841072-f978cf4d019e?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=1600');">
    <div class="bg-black bg-opacity-40 p-8 rounded-lg">
        <h1 class="text-5xl font-montserrat font-bold">Live a Life of Style & Adventure</h1>
        <p class="mt-4 text-lg font-lora">Your ultimate guide to travel, fashion, and mindful living.</p>
    </div>
</section>

<!-- 2. Featured Blog Posts -->
<section class="featured-posts mt-12">
    <h2 class="text-3xl font-bold text-center font-montserrat mb-8">Latest Posts</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($featured_posts as $post): ?>
            <div class="post-card bg-white rounded-lg shadow-lg overflow-hidden">
                <a href="/post/<?php echo $post['id']; ?>/<?php echo $post['slug']; ?>">
                    <img src="<?php echo htmlspecialchars($post['featured_image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-48 object-cover">
                </a>
                <div class="p-6">
                    <span class="text-sm font-montserrat text-gray-500 uppercase">
                        <a href="/category/<?php echo strtolower(htmlspecialchars($post['category'])); ?>" class="hover:text-blush-pink"><?php echo htmlspecialchars($post['category']); ?></a>
                    </span>
                    <h3 class="text-xl font-bold font-montserrat mt-2">
                        <a href="/post/<?php echo $post['id']; ?>/<?php echo $post['slug']; ?>" class="hover:text-blush-pink"><?php echo htmlspecialchars($post['title']); ?></a>
                    </h3>
                    <p class="mt-2 text-gray-700"><?php echo substr(htmlspecialchars($post['content']), 0, 100); ?>...</p>
                    <a href="/post/<?php echo $post['id']; ?>/<?php echo $post['slug']; ?>" class="inline-block mt-4 font-montserrat text-sm font-semibold text-gray-800 hover:text-blush-pink">Read More &rarr;</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- 3. Newsletter Signup Form -->
<section class="newsletter-signup bg-mint-green mt-12 py-12">
    <div class="container mx-auto text-center">
        <h2 class="text-3xl font-bold font-montserrat">Join the Community</h2>
        <p class="mt-2 text-gray-700">Get the latest travel tips, fashion trends, and wellness advice delivered to your inbox.</p>
        <form action="#" method="POST" class="mt-6 max-w-md mx-auto">
            <div class="flex">
                <input type="email" name="email" placeholder="Enter your email address" class="w-full px-4 py-3 rounded-l-lg focus:outline-none" required>
                <button type="submit" class="bg-gray-800 text-white font-semibold px-6 py-3 rounded-r-lg hover:bg-gray-700">Subscribe</button>
            </div>
        </form>
    </div>
</section>

<?php include '../templates/footer.php'; ?>
