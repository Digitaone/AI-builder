<?php include 'partials/header.php'; ?>

<div class="form-container">
    <h2>Contact Us</h2>
    <p>If you have any questions, please fill out the form below and we'll get back to you as soon as possible.</p>

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($success) && !empty($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <form action="/contact" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <div class="form-group">
            <label for="name">Your Name</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($submitted_data['name'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Your Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($submitted_data['email'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" class="form-control" value="<?php echo htmlspecialchars($submitted_data['subject'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="6" class="form-control" required><?php echo htmlspecialchars($submitted_data['message'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="captcha">Anti-Spam: What is <?php echo $num1; ?> + <?php echo $num2; ?>?</label>
            <input type="number" id="captcha" name="captcha" class="form-control" required>
        </div>
        <button type="submit" class="btn">Send Message</button>
    </form>
</div>

<?php include 'partials/footer.php'; ?>
