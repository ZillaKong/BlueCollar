// HTML escape function to prevent XSS attacks
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

$(document).ready(function(){
    fetchBrandData();

    // Use event delegation for edit/delete buttons (safer than inline onclick)
    $(document).on('click', '.edit-brand-btn', function(){
        const id = $(this).data('id');
        const name = $(this).data('name');
        editBrand(id, name);
    });

    $(document).on('click', '.delete-brand-btn', function(){
        const id = $(this).data('id');
        deleteBrand(id);
    });
});

function fetchBrandData(){
    $.ajax({
        url: '../actions/get_brands.php',
        type: 'GET',
        dataType: 'json',
        success: function(data){
            const tableBody = $('#brandsTable tbody');
            tableBody.empty();

            if (data.length > 0){
                $.each(data, function(index, brand){
                    const brandId = parseInt(brand.brand_id) || 0;
                    const brandName = escapeHtml(brand.brand_name);
                    const totalProducts = parseInt(brand.total_products) || 0;
                    const row = `<tr>
                            <td>${brandId}</td>
                            <td>${brandName}</td>
                            <td>${totalProducts}</td>
                            <td>
                                <button class="edit-brand-btn" data-id="${brandId}" data-name="${brandName}">Update</button>
                                <button class="delete-brand-btn" data-id="${brandId}">Delete</button>
                            </td>
                        </tr>`;
                    tableBody.append(row);
                });
            }else{
                tableBody.append('<tr><td colspan="4">No brands found.</td></tr>');
            }
        },
        error: function(xhr, status, error){
            console.error('Error fetching brands:', error);
            alert('Error loading brands data.');
        }
    })
}

function editBrand(id, name){
    const newName = prompt("Enter new brand name:", name);
    if (newName && newName.trim() !== '' && newName !== name) {
        $.ajax({
            url: '../actions/update_brand.php',
            type: 'POST',
            data: { brand_id: id, brand_name: newName.trim() },
            dataType: 'json',
            success: function(response){
                if (response.status === 'success') {
                    alert('Brand updated successfully!');
                    fetchBrandData();
                } else {
                    alert('Error updating brand: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error){
                console.error('Error updating brand:', error);
                alert('Error updating brand. Please try again.');
            }
        });
    }
}

function deleteBrand(id){
    if (confirm("Are you sure you want to delete this brand?")) {
        $.ajax({
            url: '../actions/delete_brand.php',
            type: 'POST',
            data: { brand_id: id },
            dataType: 'json',
            success: function(response){
                if (response.status === 'success') {
                    alert('Brand deleted successfully!');
                    fetchBrandData();
                } else {
                    alert('Error deleting brand: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error){
                console.error('Error deleting brand:', error);
                alert('Error deleting brand. Please try again.');
            }
        });
    }
}
