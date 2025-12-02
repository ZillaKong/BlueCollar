// HTML escape function to prevent XSS attacks
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

$(document).ready(function(){
    fetchCategoryData();
    fetchProductData();
    fetchStoreData();

    // Make category cards clickable
    $(document).on('click', '.category-card', function(){
        const categoryId = $(this).data('category-id');
        const categoryName = $(this).find('.category-title').text();
        window.location.href = `category.php?id=${categoryId}&name=${encodeURIComponent(categoryName)}`;
    });

    // Make store cards clickable
    $(document).on('click', '.store-card', function(){
        const storeId = $(this).data('store-id');
        window.location.href = `storefront.php?id=${storeId}`;
    });
});

// Fetch and display categories
function fetchCategoryData(){
    $.ajax({
        url: '../../actions/get_category.php',
        type: 'GET',
        dataType: 'json',
        success: function(data){
            const categoryContainer = $('#category-container');
            categoryContainer.empty();

            if (data.length > 0){
                $.each(data, function(index, category){
                    const escapedName = escapeHtml(category.category_name);
                    const categoryId = parseInt(category.category_id) || 0;
                    const card = `<div class="category-card" data-category-id="${categoryId}">
                                    <div class="category-image-placeholder"><span>📁</span></div>
                                    <h4 class="category-title">${escapedName}</h4>
                                    <p class="category-count">${parseInt(category.total_products) || 0} products</p>
                                </div>`;
                    categoryContainer.append(card);
                });
            } else {
                categoryContainer.append('<p>No categories found.</p>');
            }
        },
        error: function(xhr, status, error){
            console.error('Error fetching categories:', error);
            $('#category-container').append('<p>Error loading categories.</p>');
        }
    });
}

// Fetch and display products
function fetchProductData(){
    $.ajax({
        url: '../../actions/get_product.php',
        type: 'GET',
        dataType: 'json',
        success: function(data){
            const productContainer = $('#product-container');
            productContainer.empty();

            if (data.length > 0){
                $.each(data, function(index, product){
                    const escapedName = escapeHtml(product.product_name);
                    const productId = parseInt(product.product_id) || 0;
                    const card = `<div class="product-card" data-product-id="${productId}">
                                    <div class="product-image-placeholder"><span>📦</span></div>
                                    <h4 class="product-title">${escapedName}</h4>
                                </div>`;
                    productContainer.append(card);
                });
            } else {
                productContainer.append('<p>No products found.</p>');
            }
        },
        error: function(xhr, status, error){
            console.error('Error fetching products:', error);
            $('#product-container').append('<p>Error loading products.</p>');
        }
    });
}

// Fetch and display stores
function fetchStoreData(){
    $.ajax({
        url: '../../actions/get_storefront.php',
        type: 'GET',
        data: { all: 1 },
        dataType: 'json',
        success: function(data){
            const storeContainer = $('#store-container');
            storeContainer.empty();

            if (data.length > 0){
                $.each(data, function(index, store){
                    const escapedName = escapeHtml(store.store_name) || 'Unnamed Store';
                    const description = escapeHtml(store.store_description 
                        ? (store.store_description.length > 100 
                            ? store.store_description.substring(0, 100) + '...' 
                            : store.store_description)
                        : 'No description available');
                    const companyName = escapeHtml(store.company_name) || '';
                    const productCount = parseInt(store.product_count) || 0;
                    const storeId = parseInt(store.store_id) || 0;
                    
                    const card = `<div class="store-card" data-store-id="${storeId}">
                                    <div class="store-icon">🏪</div>
                                    <h4 class="store-name">${escapedName}</h4>
                                    <p class="store-company">${companyName}</p>
                                    <p class="store-description">${description}</p>
                                    <p class="store-product-count">${productCount} product(s)</p>
                                </div>`;
                    storeContainer.append(card);
                });
            } else {
                storeContainer.append('<p>No stores found.</p>');
            }
        },
        error: function(xhr, status, error){
            console.error('Error fetching stores:', error);
            $('#store-container').append('<p>Error loading stores.</p>');
        }
    });
}
