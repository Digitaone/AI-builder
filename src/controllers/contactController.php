<?php

/**
 * Displays the contact form.
 */
function show_contact_form() {
    // Simple math CAPTCHA
    $num1 = rand(1, 9);
    $num2 = rand(1, 9);
    $_SESSION['captcha_answer'] = $num1 + $num2;

    $data = [
        'title' => 'Contact Us',
        'meta_description' => 'Have a question? Get in touch with us through our contact form.',
        'num1' => $num1,
        'num2' => $num2
    ];
    render_view('contact', $data);
}

/**
 * Handles the contact form submission.
 */
function handle_contact_form() {
    verify_csrf_token();
    $errors = [];
    $success_message = '';

    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $captcha = $_POST['captcha'] ?? '';

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $errors[] = 'All fields are required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (empty($captcha) || intval($captcha) !== $_SESSION['captcha_answer']) {
        $errors[] = 'The anti-spam answer is incorrect.';
    }

    if (empty($errors)) {
        // In a real application, you would send an email here.
        // mail('admin@your-website.com', 'Contact Form: ' . $subject, $message, 'From: ' . $email);
        $success_message = 'Thank you for your message! We will get back to you shortly.';
    }

    // Regenerate a new CAPTCHA question
    $num1 = rand(1, 9);
    $num2 = rand(1, 9);
    $_SESSION['captcha_answer'] = $num1 + $num2;

    $data = [
        'title' => 'Contact Us',
        'meta_description' => 'Have a question? Get in touch with us through our contact form.',
        'num1' => $num1,
        'num2' => $num2,
        'errors' => $errors,
        'success' => $success_message,
        'submitted_data' => $_POST // To repopulate the form
    ];
    render_view('contact', $data);
}
