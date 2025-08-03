<?php
// Set default SEO values
$page_title = isset($page_title) ? $page_title . ' - WanderChicVibes' : 'WanderChicVibes - Travel, Fashion & Lifestyle Blog';
$meta_description = isset($meta_description) ? $meta_description : 'Explore travel tips, fashion trends, and lifestyle advice on WanderChicVibes.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <meta name="keywords" content="travel, fashion, lifestyle, wellness, blog">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <!-- <meta property="og:image" content="URL_TO_FEATURED_IMAGE"> -->

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <!-- <meta property="twitter:image" content="URL_TO_FEATURED_IMAGE"> -->

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind CSS Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'montserrat': ['Montserrat', 'sans-serif'],
                        'lora': ['Lora', 'serif']
                    },
                    colors: {
                        'blush-pink': '#FADADD',
                        'mint-green': '#E2F0D9',
                        'neutral-white': '#FFFFFF',
                        'neutral-beige': '#F5F5DC',
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-neutral-beige font-lora text-gray-800">
    <header class="bg-neutral-white shadow-md">
        <div class="container mx-auto px-4 py-6 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold font-montserrat text-gray-800">WanderChicVibes</a>
            <nav>
                <ul class="flex space-x-6 font-montserrat">
                    <li><a href="/" class="hover:text-blush-pink">Home</a></li>
                    <li><a href="/category/travel" class="hover:text-blush-pink">Travel</a></li>
                    <li><a href="/category/lifestyle" class="hover:text-blush-pink">Lifestyle</a></li>
                    <li><a href="/category/healthcare" class="hover:text-blush-pink">Healthcare</a></li>
                    <li><a href="/category/fashion" class="hover:text-blush-pink">Fashion</a></li>
                    <li><a href="/about" class="hover:text-blush-pink">About</a></li>
                    <li><a href="/contact" class="hover:text-blush-pink">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Start of main content area -->
    <main class="container mx-auto p-4">
