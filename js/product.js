// HTML escape function to prevent XSS attacks
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

$(document).ready(function(){
    fetchProductData();

    // Use event delegation for edit/delete buttons (safer than inline onclick)
    $(document).on('click', '.edit-product-btn', function(){
        const id = $(this).data('id');
        const name = $(this).data('name');
        const catId = $(this).data('cat-id');
        const brandId = $(this).data('brand-id');
        const storeId = $(this).data('store-id');
        editProduct(id, name, catId, brandId, storeId);
    });

    $(document).on('click', '.delete-product-btn', function(){
        const id = $(this).data('id');
        deleteProduct(id);
    });
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
                    const productId = parseInt(product.product_id) || 0;
                    const productName = escapeHtml(product.product_name);
                    const categoryId = parseInt(product.category_id) || 0;
                    const brandId = parseInt(product.brand_id) || 0;
                    const storeId = parseInt(product.store_id) || 0;
                    const row = `<tr>
                            <td>${productId}</td>
                            <td>${productName}</td>
                            <td>${categoryId}</td>
                            <td>${brandId}</td>
                            <td>${storeId}</td>
                            <td>
                                <button class="edit-product-btn" 
                                        data-id="${productId}" 
                                        data-name="${productName}" 
                                        data-cat-id="${categoryId}" 
                                        data-brand-id="${brandId}" 
                                        data-store-id="${storeId}">Update</button>
                                <button class="delete-product-btn" data-id="${productId}">Delete</button>
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
