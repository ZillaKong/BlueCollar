$(document).ready(function(){
    fetchBrandData();
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
                    const escapedName = brand.brand_name.replace(/'/g, "\\'").replace(/"/g, '\\"');
                    const row = `<tr>
                            <td>${brand.brand_id}</td>
                            <td>${brand.brand_name}</td>
                            <td>${brand.total_products}</td>
                            <td>
                                <button onclick="editBrand(${brand.brand_id}, '${escapedName}')">Update</button>
                                <button onclick="deleteBrand(${brand.brand_id})">Delete</button>
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
