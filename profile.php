<?php
// session_start(); // main.js will handle auth via check_session.php
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php'); // Redirect if not logged in (server-side check)
//     exit;
// }
include 'templates/header.php';
?>
<main>
    <h2>User Profile</h2>
    <div id="profileDetails">
        <p>Loading profile...</p>
    </div>
    <h3>Update Profile</h3>
    <form id="updateProfileForm">
        <div>
            <label for="prof_first_name">First Name:</label>
            <input type="text" id="prof_first_name" name="first_name">
        </div>
        <div>
            <label for="prof_last_name">Last Name:</label>
            <input type="text" id="prof_last_name" name="last_name">
        </div>
        <button type="submit">Update Profile</button>
    </form>
    <div id="profileMessages"></div>
</main>
<?php include 'templates/footer.php'; ?>
