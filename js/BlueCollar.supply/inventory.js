$(document).ready(function(){
    fetchInventoryData();
    loadCategories();

    $('#addProductBtn').click(function(){
        $('#addProductForm').toggle();
    });

    $('#addProductForm').submit(function(e){
        e.preventDefault();
        addProduct();
    });

    $('#cancelAddProduct').click(function(){
        $('#addProductForm').hide();
        $('#addProductForm')[0].reset();
    });
});

function fetchInventoryData(){
    $.ajax({
        url: '../../actions/get_store_products.php',
        type: 'GET',
        dataType: 'json',
        success: function(data){
            const tableBody = $('#inventoryTable tbody');
            tableBody.empty();

            if (data.length > 0){
                $.each(data, function(index, product){
                    const row = `<tr>
                            <td>${product.product_id}</td>
                            <td>${product.product_name}</td>
                            <td>${product.category_name}</td>
                            <td>${product.brand_name}</td>
                            <td>${product.description}</td>
                            <td>${product.stock_quantity}</td>
                            <td>${product.price}</td>
                            <td>
                                <button onclick="editProduct(${product.product_id}, '${product.product_name.replace(/'/g, "\\'").replace(/"/g, '\\"')}', '${product.category_name}', '${product.brand_name}', '${product.description.replace(/'/g, "\\'").replace(/"/g, '\\"')}', ${product.price})">Update</button>
                                <button onclick="deleteProduct(${product.product_id})">Delete</button>
                            </td>
                        </tr>`;
                    tableBody.append(row);
                });
            }else{
                tableBody.append('<tr><td colspan="8">No products found in your inventory.</td></tr>');
            }
        },
        error: function(xhr, status, error){
            console.error('Error fetching inventory:', error);
            alert('Error loading inventory data.');
        }
    })
}

function loadCategories(){
    $.ajax({
        url: '../../actions/get_category.php',
        type: 'GET',
        dataType: 'json',
        success: function(data){
            const categorySelect = $('#productCategory');
            categorySelect.empty();
            categorySelect.append('<option value="">Select Category</option>');

            if (data.length > 0){
                $.each(data, function(index, category){
                    categorySelect.append(`<option value="${category.category_id}">${category.category_name}</option>`);
                });
            }
        },
        error: function(xhr, status, error){
            console.error('Error loading categories:', error);
        }
    })
}

function loadBrands(){
    $.ajax({
        url: '../../actions/get_brands.php',
        type: 'GET',
        dataType: 'json',
        success: function(data){
            const brandSelect = $('#productBrand');
            brandSelect.empty();
            brandSelect.append('<option value="">Select Brand</option>');

            if (data.length > 0){
                $.each(data, function(index, brand){
                    brandSelect.append(`<option value="${brand.brand_id}">${brand.brand_name}</option>`);
                });
            }
        },
        error: function(xhr, status, error){
            console.error('Error loading brands:', error);
        }
    })
}

function addProduct(){
    const productName = $('#productName').val().trim();
    const categoryId = $('#productCategory').val();
    const brandName = $('#productBrand').val().trim();
    const description = $('#productDescription').val().trim();
    const stockQuantity = $('#productStock').val();
    const price = $('#productPrice').val();

    if (!productName || !categoryId || !brandName || !stockQuantity || !price) {
        alert('Please fill in all required fields.');
        return;
    }

    $.ajax({
        url: '../../actions/add_product.php',
        type: 'POST',
        data: {
            product_name: productName,
            category_id: categoryId,
            brand_name: brandName,
            description: description,
            stock_quantity: stockQuantity,
            price: price
        },
        dataType: 'json',
        success: function(response){
            if (response.status === 'success') {
                alert('Product added successfully!');
                $('#addProductForm')[0].reset();
                $('#addProductForm').hide();
                fetchInventoryData();
            } else {
                alert('Error adding product: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error){
            console.error('Error adding product:', error);
            alert('Error adding product. Please try again.');
        }
    });
}

function editProduct(id, name, category, brand, description){
    // Simple form generation using prompts for now
    const newName = prompt("Enter new product name:", name);
    if (newName && newName.trim() !== '' && newName !== name) {
        $.ajax({
            url: '../../actions/update_product.php',
            type: 'POST',
            data: { product_id: id, product_name: newName.trim() },
            dataType: 'json',
            success: function(response){
                if (response.status === 'success') {
                    fetchInventoryData();
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
            url: '../../actions/delete_product.php',
            type: 'POST',
            data: { product_id: id },
            dataType: 'json',
            success: function(response){
                if (response.status === 'success') {
                    fetchInventoryData();
                } else {
                    alert('Error deleting product: ' + response.message);
                }
            }
        });
    }
}
