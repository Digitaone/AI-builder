<?php
// SEO Variables
$page_title = 'Contact Us';
$meta_description = 'Get in touch with the WanderChicVibes team. We\'d love to hear from you for collaborations, questions, or feedback.';

include '../templates/header.php';
?>

<div class="bg-white p-8 rounded-lg shadow-lg max-w-4xl mx-auto">
    <header class="text-center mb-12">
        <h1 class="text-5xl font-montserrat font-bold text-gray-800">Contact Us</h1>
        <p class="mt-4 text-lg text-gray-600">We'd love to hear from you. Drop us a line!</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <!-- Contact Form -->
        <section id="contact-form">
            <h2 class="text-2xl font-montserrat font-bold mb-4">Send a Message</h2>
            <form action="#" method="POST">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-montserrat mb-2">Name</label>
                    <input type="text" id="name" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-mint-green" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-montserrat mb-2">Email</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-mint-green" required>
                </div>
                <div class="mb-4">
                    <label for="subject" class="block text-gray-700 font-montserrat mb-2">Subject</label>
                    <input type="text" id="subject" name="subject" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-mint-green" required>
                </div>
                <div class="mb-4">
                    <label for="message" class="block text-gray-700 font-montserrat mb-2">Message</label>
                    <textarea id="message" name="message" rows="5" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-mint-green" required></textarea>
                </div>
                <button type="submit" class="w-full bg-gray-800 text-white font-semibold px-6 py-3 rounded-lg hover:bg-gray-700">Submit</button>
            </form>
        </section>

        <!-- Contact Info & Map -->
        <section id="contact-info">
            <h2 class="text-2xl font-montserrat font-bold mb-4">Our Info</h2>
            <div class="text-gray-700 space-y-4">
                <p><strong>Email:</strong> <a href="mailto:hello@wanderchicvibes.com" class="hover:text-blush-pink">hello@wanderchicvibes.com</a></p>
                <p><strong>Address:</strong> 123 Chic Street, Style City, 45678 (Virtual Office)</p>
            </div>

            <h2 class="text-2xl font-montserrat font-bold mt-8 mb-4">Our Location</h2>
            <div class="w-full h-64 bg-gray-200 rounded-lg">
                <!-- Google Maps Embed Placeholder -->
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.2500000000005!2d144.9631!3d-37.8141!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0x5045675218ce7e0!2sMelbourne%20VIC%2C%20Australia!5e0!3m2!1sen!2sus!4v1620000000000!5m2!1sen!2sus"
                    width="100%"
                    height="100%"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
            </div>
        </section>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
