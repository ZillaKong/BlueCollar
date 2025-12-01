$(document).ready(function() {
    $('#login-button').click(function(event) {
        // Prevent the default form submission
        event.preventDefault();

        // Get the form element
        let form = document.getElementsByName('login-form')[0];
        let formData = $(form).serialize();
        console.log("Serialized Data:", formData);

        // Perform client-side validation
        let email = $('input[name="email"]').val();
        let password = $('input[name="password"]').val();
        if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            alert('Please enter a valid email address.');
            return;
        }
        if (password.length < 8) {
            alert('Password must be at least 8 characters long.');
            return;
        }
        // If validation passes, submit the form
        $.ajax({
            url: '/../BlueCollar/actions/login_user_actions.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                // Handle success response
                if (response.status === 'success') {
                    alert('Login successful!');
                    if (response.role === 'supplier') {
                        window.location.href = '/../BlueCollar/view/BlueCoallr.supply/home.php'; // Redirect to dashboard page
                    }else if (response.role === 'buyer') {
                        window.location.href = '/../BlueCollar/view/BlueCollar/home.php'; // Redirect to dashboard page
                    }else if (response.role === 'admin') {
                        window.location.href = '/../BlueCollar/view/admin.php'; // Redirect to admin page
                    }
                } else {
                    alert('Login failed: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                // Handle error response
                alert('Login failed: ' + xhr.responseText, error, status);
            }
        });
    });
});