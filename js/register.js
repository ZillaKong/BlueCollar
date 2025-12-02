$(document).ready(function() {
    $('#register-button').click(function(event) {
        // Prevent the default form submission
        event.preventDefault();
        
        // Get the form element
        form = document.getElementsByName('supplier-registration-form')[0] || document.getElementsByName('buyer-registration-form')[0];
        let formData = $(form).serialize();
        
        // Perform client-side validation
        let password = $('input[name="password"]').val();
        let confirmPassword = $('input[name="confirm_password"]').val();
        if (password.length < 8 || !password.match (/[A-z]/) || !password.match(/[0-9]/) ){
            alert('Password must be at least 8 characters long and include both letters and numbers.');
            return;
        }
        else if
            (password !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }   
        // If validation passes, submit the form
        $.ajax({
            url: '../../actions/register_user_actions.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                // Handle success response
                if (response.status === 'success') {
                alert('Registration successful!');
                window.location.href = '../view/login.php'; // Redirect to login page
                } else {
                alert('Registration failed: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                // Handle error response
                alert('Registration failed: ' + xhr.responseText, error, status);
            }
        });
        
    });
});