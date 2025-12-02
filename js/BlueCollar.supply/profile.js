$(document).ready(function() {
    loadProfileData();
    loadCategories();

    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        saveProfile();
    });
});

function loadProfileData() {
    $.ajax({
        url: '../../actions/get_storefront.php?current=1',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && response.data) {
                const data = response.data;
                $('#store_name').val(data.store_name || '');
                $('#company_name').val(data.company_name || '');
                $('#store_description').val(data.store_description || '');
                $('#primary_category').val(data.primary_category || '');
                $('#phone').val(data.phone || '');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading profile:', error);
            showMessage('Error loading profile data.', 'error');
        }
    });
}

function loadCategories() {
    $.ajax({
        url: '../../actions/get_category.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const select = $('#primary_category');
            select.find('option:not(:first)').remove();
            
            if (Array.isArray(data)) {
                data.forEach(function(category) {
                    select.append(`<option value="${category.cat_id}">${escapeHtml(category.name)}</option>`);
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading categories:', error);
        }
    });
}

function saveProfile() {
    const formData = {
        store_name: $('#store_name').val(),
        company_name: $('#company_name').val(),
        store_description: $('#store_description').val(),
        primary_category: $('#primary_category').val(),
        phone: $('#phone').val()
    };

    $.ajax({
        url: '../../actions/update_storefront.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                showMessage('Profile updated successfully!', 'success');
            } else {
                showMessage(response.message || 'Error updating profile.', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error saving profile:', error);
            showMessage('Error saving profile. Please try again.', 'error');
        }
    });
}

function showMessage(message, type) {
    const msgDiv = $('#profile-message');
    msgDiv.text(message);
    msgDiv.removeClass('success error').addClass(type);
    msgDiv.show();
    
    setTimeout(function() {
        msgDiv.fadeOut();
    }, 3000);
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

