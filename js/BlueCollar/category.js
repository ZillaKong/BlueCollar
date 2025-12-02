$(document).ready(function(){
    // Get URL parameters using JavaScript
    const urlParams = new URLSearchParams(window.location.search);
    const categoryId = parseInt(urlParams.get('id')) || 0;
    const categoryName = urlParams.get('name') || 'Category';

    // Update page title and header
    document.title = categoryName + ' - BlueCollar';
    $('#category-title').text(categoryName);

    if (categoryId && categoryId > 0) {
        fetchProductsByCategory(categoryId);
    } else {
        $('#category-products-container').html('<p>Invalid category selected.</p>');
        $('#product-count').text('No products to display');
    }
});

function fetchProductsByCategory(catId){
    $.ajax({
        url: '../../actions/get_products_by_category.php',
        type: 'GET',
        data: { category_id: catId },
        dataType: 'json',
        success: function(data){
            const productContainer = $('#category-products-container');
            productContainer.empty();

            if (Array.isArray(data) && data.length > 0){
                $('#product-count').text(data.length + ' product(s) found');
                
                $.each(data, function(index, product){
                    const escapedName = product.product_name.replace(/'/g, "\\'").replace(/"/g, '\\"');
                    const description = product.description ? product.description : 'No description available';
                    const price = product.price ? '$' + parseFloat(product.price).toFixed(2) : 'Price not set';
                    const stock = product.stock_quantity ? product.stock_quantity + ' in stock' : 'Out of stock';
                    const brand = product.brand_name ? product.brand_name : 'Unknown brand';
                    const store = product.store_name ? product.store_name : 'Unknown store';
                    const storeId = product.store_id || 0;
                    
                    const storeLink = storeId > 0 
                        ? `<a href="storefront.php?id=${storeId}" class="store-link">${store}</a>` 
                        : store;
                    
                    const card = `
                        <div class="product-card" data-product-id="${product.product_id}">
                            <div class="product-image-placeholder"><span>📦</span></div>
                            <div class="product-info">
                                <h4 class="product-title">${escapedName}</h4>
                                <p class="product-brand">${brand}</p>
                                <p class="product-description">${description}</p>
                                <div class="product-details">
                                    <span class="product-price">${price}</span>
                                    <span class="product-stock">${stock}</span>
                                </div>
                                <p class="product-store">Sold by: ${storeLink}</p>
                            </div>
                        </div>`;
                    productContainer.append(card);
                });
            } else if (data.status === 'error') {
                productContainer.html('<p>' + data.message + '</p>');
                $('#product-count').text('Error loading products');
            } else {
                productContainer.html('<p>No products found in this category.</p>');
                $('#product-count').text('0 products found');
            }
        },
        error: function(xhr, status, error){
            console.error('Error fetching products:', error);
            $('#category-products-container').html('<p>Error loading products. Please try again.</p>');
            $('#product-count').text('Error loading products');
        }
    });
}