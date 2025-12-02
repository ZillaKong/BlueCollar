$(document).ready(function(){
    loadStores();

    // Store search
    $('#store-search').on('input', function(){
        const searchTerm = $(this).val().toLowerCase();
        filterStores(searchTerm);
    });

    // Store selection
    $(document).on('click', '.store-select-card', function(){
        const storeId = $(this).data('store-id');
        const storeName = $(this).data('store-name');
        selectStore(storeId, storeName);
    });

    // Category selection
    $(document).on('click', '.category-select-card', function(){
        const categoryId = $(this).data('category-id');
        selectCategory(categoryId);
    });

    // Change store button
    $('#change-store-btn').on('click', function(){
        goToStep(1);
    });
});

let allStores = [];
let selectedStore = null;

function loadStores(){
    $('#stores-loading').show();
    $('#store-selection-container').empty();

    $.ajax({
        url: '../../actions/get_storefront.php',
        type: 'GET',
        data: { all: 1 },
        dataType: 'json',
        success: function(data){
            $('#stores-loading').hide();
            allStores = data;
            
            if (data.length > 0) {
                displayStores(data);
            } else {
                $('#store-selection-container').html('<p class="no-results">No stores available.</p>');
            }
        },
        error: function(xhr, status, error){
            $('#stores-loading').hide();
            console.error('Error loading stores:', error);
            $('#store-selection-container').html('<p class="error-message">Failed to load stores.</p>');
        }
    });
}

function displayStores(stores){
    const container = $('#store-selection-container');
    container.empty();

    $.each(stores, function(index, store){
        const escapedName = escapeHtml(store.store_name || 'Unnamed Store');
        const description = store.store_description 
            ? (store.store_description.length > 80 
                ? store.store_description.substring(0, 80) + '...' 
                : store.store_description)
            : 'No description available';
        const productCount = store.product_count || 0;
        
        const card = `
            <div class="store-select-card" data-store-id="${store.store_id}" data-store-name="${escapedName}">
                <div class="store-icon">🏪</div>
                <h4 class="store-name">${escapedName}</h4>
                <p class="store-company">${escapeHtml(store.company_name || '')}</p>
                <p class="store-description">${escapeHtml(description)}</p>
                <p class="store-product-count">${productCount} product(s) available</p>
                <div class="select-overlay">
                    <span>Select Store</span>
                </div>
            </div>`;
        container.append(card);
    });
}

function filterStores(searchTerm){
    if (!searchTerm) {
        displayStores(allStores);
        return;
    }

    const filtered = allStores.filter(store => {
        const name = (store.store_name || '').toLowerCase();
        const company = (store.company_name || '').toLowerCase();
        const desc = (store.store_description || '').toLowerCase();
        return name.includes(searchTerm) || company.includes(searchTerm) || desc.includes(searchTerm);
    });

    if (filtered.length > 0) {
        displayStores(filtered);
    } else {
        $('#store-selection-container').html('<p class="no-results">No stores match your search.</p>');
    }
}

function selectStore(storeId, storeName){
    selectedStore = { id: storeId, name: storeName };
    
    $('#selected-store-name').text(storeName);
    loadStoreCategories(storeId);
    goToStep(2);
}

function loadStoreCategories(storeId){
    $('#categories-loading').show();
    $('#category-selection-container').empty();

    // Get categories that this store has products in
    $.ajax({
        url: '../../actions/get_storefront.php',
        type: 'GET',
        data: { store_id: storeId },
        dataType: 'json',
        success: function(response){
            $('#categories-loading').hide();
            
            if (response.status === 'success' && response.categories && response.categories.length > 0) {
                displayCategories(response.categories);
            } else {
                // If no specific categories, show option to view all products
                $('#category-selection-container').html(`
                    <div class="category-select-card all-products" data-category-id="0">
                        <div class="category-icon">📦</div>
                        <h4 class="category-name">All Products</h4>
                        <p class="category-desc">Browse all available products</p>
                        <div class="select-overlay">
                            <span>View Products</span>
                        </div>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error){
            $('#categories-loading').hide();
            console.error('Error loading categories:', error);
            $('#category-selection-container').html('<p class="error-message">Failed to load categories.</p>');
        }
    });
}

function displayCategories(categories){
    const container = $('#category-selection-container');
    container.empty();

    // Add "All Products" option first
    container.append(`
        <div class="category-select-card all-products" data-category-id="0">
            <div class="category-icon">📦</div>
            <h4 class="category-name">All Products</h4>
            <p class="category-desc">Browse all available products</p>
            <div class="select-overlay">
                <span>View Products</span>
            </div>
        </div>
    `);

    $.each(categories, function(index, category){
        const escapedName = escapeHtml(category.category_name);
        
        const card = `
            <div class="category-select-card" data-category-id="${category.category_id}">
                <div class="category-icon">🏷️</div>
                <h4 class="category-name">${escapedName}</h4>
                <div class="select-overlay">
                    <span>Select Category</span>
                </div>
            </div>`;
        container.append(card);
    });
}

function selectCategory(categoryId){
    if (!selectedStore) {
        alert('Please select a store first.');
        goToStep(1);
        return;
    }

    goToStep(3);
    createOrderAndRedirect(categoryId);
}

function createOrderAndRedirect(categoryId){
    // Create the order
    $.ajax({
        url: '../../actions/order_actions.php',
        type: 'POST',
        data: { 
            action: 'create',
            storefront_id: selectedStore.id
        },
        dataType: 'json',
        success: function(response){
            if (response.status === 'success') {
                // Redirect to storefront with order context
                let redirectUrl = `storefront.php?id=${selectedStore.id}&order_id=${response.order_id}`;
                if (categoryId && categoryId !== 0) {
                    redirectUrl += `&category=${categoryId}`;
                }
                window.location.href = redirectUrl;
            } else {
                alert(response.message || 'Failed to create order.');
                goToStep(2);
            }
        },
        error: function(){
            alert('Error creating order. Please try again.');
            goToStep(2);
        }
    });
}

function goToStep(stepNum){
    // Hide all steps
    $('.order-step').hide();
    
    // Show selected step
    $(`#step-${stepNum}`).show();
    
    // Update step indicator
    $('.step').removeClass('active completed');
    for (let i = 1; i < stepNum; i++) {
        $(`.step[data-step="${i}"]`).addClass('completed');
    }
    $(`.step[data-step="${stepNum}"]`).addClass('active');
}

function escapeHtml(text){
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

