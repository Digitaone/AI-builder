<?php
$all_posts = [
    ['id' => 1, 'title' => 'A Chic Guide to a Weekend in Paris', 'category' => 'Travel', 'author_name' => 'Jane Doe', 'created_at' => '2024-07-20', 'content' => '<p>Paris is always a good idea...</p>', 'featured_image_url' => 'https://images.unsplash.com/photo-1502602898657-3e91760c0337?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=1600', 'slug' => 'a-chic-guide-to-a-weekend-in-paris'],
    ['id' => 2, 'title' => '5 Wellness Habits to Transform Your Morning Routine', 'category' => 'Lifestyle', 'author_name' => 'John Smith', 'created_at' => '2024-07-18', 'content' => '<p>Elevate your mornings...</p>', 'featured_image_url' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17025?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=1600', 'slug' => '5-wellness-habits-to-transform-your-morning-routine'],
    // ... other posts
];

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;
foreach ($all_posts as $p) {
    if ($p['id'] === $post_id) {
        $post = $p;
        break;
    }
}

// SEO Variables
if ($post) {
    $page_title = $post['title'];
    $meta_description = substr(strip_tags($post['content']), 0, 160);
} else {
    $page_title = 'Post Not Found';
    $meta_description = 'The requested post could not be found.';
}

include '../templates/header.php';
?>

<article class="bg-white p-8 rounded-lg shadow-lg max-w-4xl mx-auto">
    <?php if ($post): ?>
        <header class="mb-8 text-center">
            <img src="<?php echo htmlspecialchars($post['featured_image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-96 object-cover rounded-lg mb-6">
            <h1 class="text-4xl md:text-5xl font-montserrat font-bold text-gray-800"><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="meta text-gray-500 mt-4 font-montserrat text-sm">
                <span>By <?php echo htmlspecialchars($post['author_name']); ?></span> |
                <span><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span> |
                <a href="/category/<?php echo urlencode(strtolower($post['category'])); ?>" class="hover:text-blush-pink"><?php echo htmlspecialchars($post['category']); ?></a>
            </div>
        </header>

        <div class="prose lg:prose-xl max-w-none font-lora">
            <?php echo $post['content']; ?>
        </div>

        <section id="comments" class="mt-12">
            <h2 class="text-2xl font-montserrat font-bold mb-6">Comments</h2>
            <div class="bg-gray-100 p-6 rounded-lg">
                <p>Comments are coming soon!</p>
            </div>
        </section>

    <?php else: ?>
        <div class="text-center">
            <h1 class="text-3xl font-montserrat font-bold">Post Not Found</h1>
            <p class="mt-4">Sorry, we couldn't find the post you're looking for.</p>
            <a href="/" class="inline-block mt-6 bg-gray-800 text-white font-semibold px-6 py-3 rounded-lg hover:bg-gray-700">Back to Homepage</a>
        </div>
    <?php endif; ?>
</article>

<?php include '../templates/footer.php'; ?>
