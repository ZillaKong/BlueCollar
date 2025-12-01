$(document).ready(function(){
    fetchProductData();
});

function fetchProductData(){
    $.ajax({
        url: '../actions/get_product.php',
        type: 'GET',
        dataType: 'json',
        success: function(data){
            const productContainer = $('#product-container');
            productContainer.empty();

            if (data.length > 0){
                $.each(data, function(index, product){
                    const escapedName = product.product_name.replace(/'/g, "\\'").replace(/"/g, '\\"');
                    const card = `<div class="category-card" id="category">
                                    <img src="../assets/mechanic.jpg" alt="${escapedName}" class="category-image">
                                    <h4 class="category-title">${escapedName}</h4>
                                </div>`;
                    productContainer.append(card);
                });
            }else{
                productContainer.append('<p>No products found.</p>');
            }
        },
        error: function(xhr, status, error){
            console.error('Error fetching products:', error);
            alert('Error loading products data.');
        }
    })
}


