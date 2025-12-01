$(document).ready(function(){
    fetchProductData();
});

function fetchProductData(){
    $.ajax({
        url: '../actions/get_product.php',
        type: 'GET',
        dataType: 'json',
        success: function(data){
            const tableBody = $('#productTable tbody');
            tableBody.empty();

            if (data.length > 0){
                $.each(data, function(index, product){
                    const escapedName = product.product_name.replace(/'/g, "\\'").replace(/"/g, '\\"');
                    const row = `<tr>
                            <td>${product.product_id}</td>
                            <td>${escapedName}</td>
                            <td>${product.category_id}</td>
                            <td>${product.brand_id}</td>
                            <td>${product.store_id}</td>
                            <td>
                                <button onclick="editProduct(${product.product_id}, '${escapedName}', ${product.category_id}, ${product.brand_id}, ${product.store_id})">Update</button>
                                <button onclick="deleteProduct(${product.product_id})">Delete</button>
                            </td>
                        </tr>`;
                    tableBody.append(row);
                });
            }else{
                tableBody.append('<tr><td colspan="6">No products found.</td></tr>');
            }
        },
        error: function(xhr, status, error){
            console.error('Error fetching products:', error);
            alert('Error loading products data.');
        }
    })
}

function editProduct(id, name, catId, brandId, storeId){
    // Simple form generation using prompts for now
    const newName = prompt("Enter new product name:", name);
    const newCatId = prompt("Enter new category ID:", catId);
    const newBrandId = prompt("Enter new brand ID:", brandId);
    const newStoreId = prompt("Enter new store ID:", storeId);
    if (newName && newCatId && newBrandId && newStoreId) {
        $.ajax({
            url: '../actions/update_product.php',
            type: 'POST',
            data: { product_id: id, product_name: newName, category_id: newCatId, brand_id: newBrandId, store_id: newStoreId },
            dataType: 'json',
            success: function(response){
                if (response.status === 'success') {
                    fetchProductData();
                } else {
                    alert('Error updating product: ' + response.message);
                }
            }
        });
    }
}

function deleteProduct(id){
    if (confirm("Are you sure you want to delete this product?")) {
        $.ajax({
            url: '../actions/delete_product.php',
            type: 'POST',
            data: { product_id: id },
            dataType: 'json',
            success: function(response){
                if (response.status === 'success') {
                    fetchProductData();
                } else {
                    alert('Error deleting product: ' + response.message);
                }
            }
        });
    }
}
