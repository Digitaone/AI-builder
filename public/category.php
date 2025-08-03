<?php
$all_posts = [
    ['id' => 1, 'title' => 'A Chic Guide to a Weekend in Paris', 'category' => 'Travel', 'content' => '...', 'featured_image_url' => 'https://images.unsplash.com/photo-1502602898657-3e91760c0337?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=1600', 'slug' => 'a-chic-guide-to-a-weekend-in-paris'],
    ['id' => 2, 'title' => '5 Wellness Habits to Transform Your Morning Routine', 'category' => 'Lifestyle', 'content' => '...', 'featured_image_url' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17025?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=1600', 'slug' => '5-wellness-habits-to-transform-your-morning-routine'],
    ['id' => 3, 'title' => 'Autumn Fashion Trends You Need to Know', 'category' => 'Fashion', 'content' => '...', 'featured_image_url' => 'https://images.unsplash.com/photo-1574271146353-8317a53f8538?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=1600', 'slug' => 'autumn-fashion-trends-you-need-to-know'],
    ['id' => 4, 'title' => 'Exploring the Amalfi Coast: A 7-Day Itinerary', 'category' => 'Travel', 'content' => '...', 'featured_image_url' => 'https://images.unsplash.com/photo-1533105079780-52b9be4ac20c?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=1600', 'slug' => 'exploring-the-amalfi-coast-a-7-day-itinerary'],
    ['id' => 5, 'title' => 'Mental Health: The Importance of Setting Boundaries', 'category' => 'Healthcare', 'content' => '...', 'featured_image_url' => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=1600', 'slug' => 'mental-health-the-importance-of-setting-boundaries']
];

$category_name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'All';

$filtered_posts = [];
if ($category_name !== 'All') {
    foreach ($all_posts as $post) {
        if (strcasecmp($post['category'], $category_name) == 0) {
            $filtered_posts[] = $post;
        }
    }
} else {
    $filtered_posts = $all_posts;
}

// SEO Variables
$page_title = 'Category: ' . ucfirst($category_name);
$meta_description = 'Browse all posts in the ' . htmlspecialchars(ucfirst($category_name)) . ' category on WanderChicVibes.';

include '../templates/header.php';
?>

<div class="category-header text-center my-8">
    <h1 class="text-4xl font-montserrat font-bold">Category: <?php echo ucfirst($category_name); ?></h1>
</div>

<section class="posts-grid">
    <?php if (!empty($filtered_posts)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($filtered_posts as $post): ?>
                <div class="post-card bg-white rounded-lg shadow-lg overflow-hidden">
                    <a href="/post/<?php echo $post['id']; ?>/<?php echo $post['slug']; ?>">
                        <img src="<?php echo htmlspecialchars($post['featured_image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-48 object-cover">
                    </a>
                    <div class="p-6">
                        <span class="text-sm font-montserrat text-gray-500 uppercase"><?php echo htmlspecialchars($post['category']); ?></span>
                        <h3 class="text-xl font-bold font-montserrat mt-2">
                            <a href="/post/<?php echo $post['id']; ?>/<?php echo $post['slug']; ?>" class="hover:text-blush-pink"><?php echo htmlspecialchars($post['title']); ?></a>
                        </h3>
                        <a href="/post/<?php echo $post['id']; ?>/<?php echo $post['slug']; ?>" class="inline-block mt-4 font-montserrat text-sm font-semibold text-gray-800 hover:text-blush-pink">Read More &rarr;</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-600">No posts found in this category.</p>
    <?php endif; ?>
</section>

<?php include '../templates/footer.php'; ?>
