document.addEventListener('DOMContentLoaded', function() {
    const registrationForm = document.getElementById('registrationForm');
    const loginForm = document.getElementById('loginForm');
    const registrationMessages = document.getElementById('registrationMessages');
    const loginMessages = document.getElementById('loginMessages');

    if (registrationForm) {
        registrationForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = new FormData(registrationForm);
            const data = Object.fromEntries(formData.entries());

            registrationMessages.innerHTML = ''; // Clear previous messages

            // Basic client-side validation
            if (!data.username || !data.email || !data.password || !data.confirm_password) {
                registrationMessages.innerHTML = '<p style="color:red;">Please fill in all required fields.</p>';
                return;
            }
            if (data.password !== data.confirm_password) {
                registrationMessages.innerHTML = '<p style="color:red;">Passwords do not match.</p>';
                return;
            }
            if (data.password.length < 8) {
                registrationMessages.innerHTML = '<p style="color:red;">Password must be at least 8 characters long.</p>';
                return;
            }
            // Validate email format (basic)
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(data.email)) {
                registrationMessages.innerHTML = '<p style="color:red;">Invalid email format.</p>';
                return;
            }


            try {
                const response = await fetch('php/api/register.php', {
                    method: 'POST',
                    body: formData // FormData directly for multipart/form-data or x-www-form-urlencoded
                });
                const result = await response.json();
                if (result.status === 'success') { // Check based on actual API response
                    registrationMessages.innerHTML = `<p style="color:green;">${result.message || 'Registration successful! You can now log in.'}</p>`;
                    registrationForm.reset();
                    // Optionally redirect to login page: window.location.href = 'login.php';
                } else {
                    let errorMsg = result.message || 'Registration failed.';
                    if (result.errors) { // Display specific validation errors from backend
                        errorMsg += '<ul>';
                        result.errors.forEach(err => { errorMsg += `<li>${err}</li>`; });
                        errorMsg += '</ul>';
                    }
                    registrationMessages.innerHTML = `<p style="color:red;">${errorMsg}</p>`;
                }
            } catch (error) {
                console.error('Registration fetch error:', error);
                registrationMessages.innerHTML = `<p style="color:red;">An error occurred during registration: ${error.message}</p>`;
            }
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = new FormData(loginForm);
            loginMessages.innerHTML = ''; // Clear previous messages

            const email = formData.get('email');
            const password = formData.get('password');

            if (!email || !password) {
                loginMessages.innerHTML = '<p style="color:red;">Email and password are required.</p>';
                return;
            }
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                loginMessages.innerHTML = '<p style="color:red;">Invalid email format.</p>';
                return;
            }

            try {
                const response = await fetch('php/api/login.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.status === 'success') { // Check based on actual API response
                    loginMessages.innerHTML = `<p style="color:green;">${result.message || 'Login successful!'}</p>`;
                    if (result.user) {
                        console.log('Logged in user:', result.user);
                        updateNavOnLogin(result.user);
                        // Redirect to profile page or dashboard after successful login
                        window.location.href = 'profile.php'; // Example redirect
                    } else {
                         // Should not happen if status is success and API is consistent
                        loginMessages.innerHTML = `<p style="color:orange;">Login successful, but no user data returned.</p>`;
                    }
                } else {
                    loginMessages.innerHTML = `<p style="color:red;">${result.message || 'Login failed.'}</p>`;
                }
            } catch (error) {
                console.error('Login fetch error:', error);
                loginMessages.innerHTML = `<p style="color:red;">An error occurred during login: ${error.message}</p>`;
            }
        });
    }

    const profileForm = document.getElementById('updateProfileForm');
    const profileDetailsDiv = document.getElementById('profileDetails');
    const profileMessages = document.getElementById('profileMessages');
    const profFirstNameField = document.getElementById('prof_first_name');
    const profLastNameField = document.getElementById('prof_last_name');

    async function loadProfileData() {
        if (!profileDetailsDiv && !profileForm) return; // Only run if elements exist

        try {
            const response = await fetch('php/api/check_session.php'); // Assuming GET request
            const result = await response.json();

            if (result.loggedIn && result.user) {
                if (profileDetailsDiv) {
                     profileDetailsDiv.innerHTML = `
                        <p><strong>Username:</strong> ${result.user.username || 'N/A'}</p>
                        <p><strong>Email:</strong> ${result.user.email || 'N/A'}</p>
                        <p><strong>First Name:</strong> ${result.user.first_name || 'Not set'}</p>
                        <p><strong>Last Name:</strong> ${result.user.last_name || 'Not set'}</p>
                    `;
                }
                if (profFirstNameField) profFirstNameField.value = result.user.first_name || '';
                if (profLastNameField) profLastNameField.value = result.user.last_name || '';
                if (profileForm) profileForm.style.display = 'block';
            } else {
                // Not logged in, redirect or show message
                if (profileDetailsDiv) profileDetailsDiv.innerHTML = "<p>You are not logged in. Please <a href='login.php'>login</a> to view your profile.</p>";
                if (profileForm) profileForm.style.display = 'none'; // Hide form if not logged in
                // If on profile page and not logged in, redirect to login
                if(window.location.pathname.includes('profile.php')) {
                    window.location.href = 'login.php?redirect=profile';
                }
            }
        } catch (error) {
            console.error("Error loading profile data:", error);
            if (profileDetailsDiv) profileDetailsDiv.innerHTML = "<p style='color:red;'>Error loading profile data.</p>";
        }
    }

    if (profileForm) {
        profileForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = new FormData(profileForm);
            profileMessages.innerHTML = ''; // Clear previous messages

            try {
                const response = await fetch('php/api/update_profile.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.status === 'success') { // Check based on actual API response
                    profileMessages.innerHTML = `<p style="color:green;">${result.message || 'Profile updated successfully!'}</p>`;
                    loadProfileData(); // Refresh displayed data
                    checkLoginStatusAndUpdateNav(); // Update nav if name changed and shown there
                } else {
                    let errorMsg = result.message || 'Profile update failed.';
                    if (result.errors) {
                        errorMsg += '<ul>';
                        result.errors.forEach(err => { errorMsg += `<li>${err}</li>`; });
                        errorMsg += '</ul>';
                    }
                    profileMessages.innerHTML = `<p style="color:red;">${errorMsg}</p>`;
                }
            } catch (error) {
                console.error('Profile update fetch error:', error);
                profileMessages.innerHTML = `<p style="color:red;">An error occurred: ${error.message}</p>`;
            }
        });
    }

    function updateNavOnLogin(user) {
        const nav = document.querySelector('header nav');
        if (!nav) return;

        // Remove existing Login/Register links
        const loginLink = nav.querySelector('a[href="login.php"]');
        const registerLink = nav.querySelector('a[href="register.php"]');
        if (loginLink) loginLink.remove();
        if (registerLink) registerLink.remove();

        // Remove old user-specific elements if they exist (e.g., from a previous state)
        const oldWelcomeMsg = nav.querySelector('#navWelcomeMsg');
        const oldLogoutButton = nav.querySelector('#logoutButton');
        const oldProfileLink = nav.querySelector('a[href="profile.php"]');
        const oldAdminLink = nav.querySelector('a[href="admin_dashboard.php"]'); // Example
        if(oldWelcomeMsg) oldWelcomeMsg.remove();
        if(oldLogoutButton) oldLogoutButton.remove();
        if(oldProfileLink) oldProfileLink.remove();
        if(oldAdminLink) oldAdminLink.remove();

        let userHTML = `<span id="navWelcomeMsg" style="margin-right: 1em;">Welcome, ${user.username}!</span>`;
        userHTML += `<a href="profile.php" style="margin-right: 1em;">Profile</a>`;
        if (user.role === 'admin') {
            userHTML += `<a href="admin_dashboard.php" style="margin-right: 1em;">Admin Panel</a>`; // Example admin link
        }
        userHTML += `<a href="#" id="logoutButton">Logout</a>`;

        nav.insertAdjacentHTML('beforeend', userHTML);

        const logoutButton = document.getElementById('logoutButton');
        if (logoutButton) {
            logoutButton.addEventListener('click', handleLogout);
        }
    }

    function updateNavOnLogout() {
        const nav = document.querySelector('header nav');
        if (!nav) return;

        // Remove user-specific elements
        const welcomeMsg = nav.querySelector('#navWelcomeMsg');
        const logoutButton = document.getElementById('logoutButton');
        const profileLink = nav.querySelector('a[href="profile.php"]');
        const adminLink = nav.querySelector('a[href="admin_dashboard.php"]'); // Example

        if (welcomeMsg) welcomeMsg.remove();
        if (logoutButton) logoutButton.remove();
        if (profileLink) profileLink.remove();
        if (adminLink) adminLink.remove();

        // Add Login/Register links if they don't exist
        if (!nav.querySelector('a[href="login.php"]')) {
            nav.insertAdjacentHTML('beforeend', ' <a href="login.php" style="margin-right: 1em;">Login</a>');
        }
        if (!nav.querySelector('a[href="register.php"]')) {
            nav.insertAdjacentHTML('beforeend', ' <a href="register.php">Register</a>');
        }
    }

    async function handleLogout(event) {
        if(event) event.preventDefault();
        try {
            const response = await fetch('php/api/logout.php', { method: 'POST' }); // Assuming POST for logout
            const result = await response.json();
            if (result.status === 'success') { // Check based on actual API response
                console.log('Logout successful:', result.message);
                updateNavOnLogout();
                // Redirect to homepage or login page
                if(window.location.pathname.includes('profile.php') || window.location.pathname.includes('admin_dashboard.php')) {
                    window.location.href = 'index.php';
                } else if (window.location.pathname.includes('login.php') || window.location.pathname.includes('register.php')) {
                    // If on login/register page, do nothing or redirect to index.php
                } else {
                     // For other pages, may not need to redirect, just update nav.
                     // Or optionally, redirect to index to ensure clean state.
                     // window.location.href = 'index.php';
                }
            } else {
                console.error('Logout failed:', result.message);
                // Display error to user if appropriate
            }
        } catch (error) {
            console.error('Error during logout:', error);
             // Display error to user if appropriate
        }
    }

    async function checkLoginStatusAndUpdateNav() {
        try {
            const response = await fetch('php/api/check_session.php'); // Assuming GET request
            const result = await response.json();
            if (result.loggedIn && result.user) {
                updateNavOnLogin(result.user);
            } else {
                updateNavOnLogout();
            }
        } catch (error) {
            console.error("Error checking session status:", error);
            updateNavOnLogout(); // Assume logged out or error
        }
    }

    // Initial actions on page load:
    checkLoginStatusAndUpdateNav(); // Check login status and update nav bar

    // Load profile data if on profile page
    if(window.location.pathname.includes('profile.php')) {
        loadProfileData();
    }
});
