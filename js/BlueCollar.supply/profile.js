$(document).ready(function() {
    // Load storefront preview data
    loadStorefrontPreview();
    loadCategories();

    // Edit profile button opens modal
    $('#edit-profile-btn').on('click', function() {
        openSettingsModal();
    });

    // Close modal buttons
    $('#close-settings-modal, #cancel-settings').on('click', function() {
        closeSettingsModal();
    });

    // Click outside modal to close
    $('.modal-overlay').on('click', function() {
        closeSettingsModal();
    });

    // Prevent modal content clicks from closing
    $('.modal-container').on('click', function(e) {
        e.stopPropagation();
    });

    // Handle form submission
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        saveProfile();
    });

    // ESC key to close modal
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#settings-modal').hasClass('active')) {
            closeSettingsModal();
        }
    });
});

// Store data globally
let storeData = null;
let productsData = [];

function loadStorefrontPreview() {
    $.ajax({
        url: '../../actions/get_storefront.php?current=1',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && response.data) {
                storeData = response.data;
                displayStorePreview(response.data);
                loadStoreProducts(response.data.store_id);
            } else {
                displayEmptyStore();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading storefront:', error);
            displayEmptyStore();
        }
    });
}

function displayStorePreview(data) {
    // Store name
    $('#preview-store-name').text(data.store_name || 'Your Store');
    
    // Category - check both category_name (from JOIN) and primary_category (ID)
    if (data.category_name) {
        $('#preview-category').text(data.category_name);
    } else if (data.primary_category) {
        // Category ID is set but name wasn't joined - fetch it
        $('#preview-category').text('Category #' + data.primary_category);
    } else {
        $('#preview-category').html('<em>Not set</em>');
    }
    
    // Company
    if (data.company_name) {
        $('#preview-company').text(data.company_name);
    } else {
        $('#preview-company').html('<em>Not set</em>');
    }
    
    // Phone
    if (data.phone) {
        $('#preview-phone').text(data.phone);
    } else {
        $('#preview-phone').html('<em>Not set</em>');
    }
    
    // Description
    if (data.store_description) {
        $('#preview-description').text(data.store_description);
    } else {
        $('#preview-description').html('<em>No description added yet. Click "Edit Store Settings" to add one.</em>');
    }
    
    // Pre-fill form fields
    $('#store_name').val(data.store_name || '');
    $('#company_name').val(data.company_name || '');
    $('#store_description').val(data.store_description || '');
    $('#phone').val(data.phone || '');
    
    // Set category after categories are loaded
    if (data.primary_category) {
        setTimeout(function() {
            $('#primary_category').val(data.primary_category);
        }, 500);
    }
}

function displayEmptyStore() {
    $('#preview-store-name').text('Your Store');
    $('#preview-category').html('<em>Not set - Click Edit to configure</em>');
    $('#preview-company').html('<em>Not set</em>');
    $('#preview-phone').html('<em>Not set</em>');
    $('#preview-description').html('<em>Get started by clicking "Edit Store Settings" to set up your storefront.</em>');
    
    // Update stats to show 0
    updateStats(0, 0, 0, 0);
}

function loadStoreProducts(storeId) {
    $.ajax({
        url: '../../actions/get_storefront.php',
        type: 'GET',
        data: { store_id: storeId },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                productsData = response.products || [];
                displayProductsPreview(productsData);
                calculateStats(productsData, response.categories || []);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading products:', error);
            displayProductsPreview([]);
            updateStats(0, 0, 0, 0);
        }
    });
}

function displayProductsPreview(products) {
    const container = $('#products-preview-container');
    container.empty();
    
    $('#products-count').text(products.length + ' product' + (products.length !== 1 ? 's' : ''));
    
    if (products.length === 0) {
        container.html(`
            <div class="no-products-message">
                <p>📦 No products yet</p>
                <p>Visit your <a href="inventory.php">Inventory</a> to add products</p>
            </div>
        `);
        return;
    }
    
    // Show max 8 products in preview
    const previewProducts = products.slice(0, 8);
    
    previewProducts.forEach(function(product) {
        const stockStatus = getStockStatus(product.stock_quantity, product.availability_status);
        const price = product.price ? '$' + parseFloat(product.price).toFixed(2) : 'No price';
        
        const card = `
            <div class="product-preview-card">
                <div class="product-preview-image">
                    <span>📦</span>
                </div>
                <h5>${escapeHtml(product.product_name)}</h5>
                <p class="product-brand">${escapeHtml(product.brand_name) || 'No brand'}</p>
                <div class="product-meta">
                    <span class="product-price">${price}</span>
                    <span class="product-stock ${stockStatus.class}">${stockStatus.text}</span>
                </div>
            </div>
        `;
        container.append(card);
    });
    
    // If there are more products, show a message
    if (products.length > 8) {
        container.append(`
            <div class="product-preview-card" style="display: flex; align-items: center; justify-content: center; background: rgba(69, 75, 27, 0.05);">
                <div style="text-align: center;">
                    <p style="margin: 0; font-size: 1.2rem; color: #454B1B;">+${products.length - 8}</p>
                    <p style="margin: 0; font-size: 0.85rem; color: #454B1B; opacity: 0.7;">more products</p>
                    <a href="inventory.php" style="color: #454B1B; font-weight: 600; font-size: 0.85rem;">View All →</a>
                </div>
            </div>
        `);
    }
}

function calculateStats(products, categories) {
    const totalProducts = products.length;
    const uniqueCategories = [...new Set(products.map(p => p.category_id).filter(c => c))].length;
    
    let inStock = 0;
    let lowStock = 0;
    
    products.forEach(function(product) {
        const qty = parseInt(product.stock_quantity) || 0;
        const status = product.availability_status;
        
        if (status === 'in stock' || status === 'in_stock') {
            if (qty > 10) {
                inStock++;
            } else if (qty > 0) {
                lowStock++;
            }
        }
    });
    
    updateStats(totalProducts, uniqueCategories || categories.length, inStock, lowStock);
}

function updateStats(products, categories, inStock, lowStock) {
    animateNumber('#stat-products', products);
    animateNumber('#stat-categories', categories);
    animateNumber('#stat-instock', inStock);
    animateNumber('#stat-lowstock', lowStock);
}

function animateNumber(selector, target) {
    const $el = $(selector);
    const current = parseInt($el.text()) || 0;
    
    if (current === target) return;
    
    $({ count: current }).animate({ count: target }, {
        duration: 500,
        easing: 'swing',
        step: function() {
            $el.text(Math.floor(this.count));
        },
        complete: function() {
            $el.text(target);
        }
    });
}

function getStockStatus(quantity, status) {
    const qty = parseInt(quantity) || 0;
    
    if (status === 'out_of_stock' || qty <= 0) {
        return { text: 'Out', class: 'out-stock' };
    } else if (qty < 10) {
        return { text: 'Low', class: 'low-stock' };
    } else {
        return { text: 'In Stock', class: 'in-stock' };
    }
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
                    // API returns category_id and category_name (not cat_id and name)
                    const catId = category.category_id || category.cat_id;
                    const catName = category.category_name || category.name;
                    select.append(`<option value="${catId}">${escapeHtml(catName)}</option>`);
                });
                
                // Set value if store data is already loaded
                if (storeData && storeData.primary_category) {
                    select.val(storeData.primary_category);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading categories:', error);
        }
    });
}

function openSettingsModal() {
    $('#settings-modal').addClass('active');
    $('body').css('overflow', 'hidden'); // Prevent background scrolling
    $('#store_name').focus();
}

function closeSettingsModal() {
    $('#settings-modal').removeClass('active');
    $('body').css('overflow', '');
    hideMessage();
}

function saveProfile() {
    const formData = {
        store_name: $('#store_name').val().trim(),
        company_name: $('#company_name').val().trim(),
        store_description: $('#store_description').val().trim(),
        primary_category: $('#primary_category').val(),
        phone: $('#phone').val().trim()
    };

    // Validate store name
    if (!formData.store_name) {
        showMessage('Store name is required.', 'error');
        $('#store_name').focus();
        return;
    }

    // Show loading state
    const $saveBtn = $('.btn-save');
    const originalText = $saveBtn.html();
    $saveBtn.prop('disabled', true).html('Saving...');

    $.ajax({
        url: '../../actions/update_storefront.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            $saveBtn.prop('disabled', false).html(originalText);
            
            if (response.status === 'success') {
                showMessage('✓ Store settings saved successfully!', 'success');
                
                // Update the preview with new data
                updatePreviewFromForm(formData);
                
                // Close modal after short delay
                setTimeout(function() {
                    closeSettingsModal();
                    // Reload to get fresh data including category name
                    loadStorefrontPreview();
                }, 1500);
            } else {
                showMessage(response.message || 'Error saving settings.', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error saving profile:', error);
            $saveBtn.prop('disabled', false).html(originalText);
            showMessage('Error saving settings. Please try again.', 'error');
        }
    });
}

function updatePreviewFromForm(data) {
    if (data.store_name) {
        $('#preview-store-name').text(data.store_name);
    }
    if (data.company_name) {
        $('#preview-company').text(data.company_name);
    } else {
        $('#preview-company').html('<em>Not set</em>');
    }
    if (data.phone) {
        $('#preview-phone').text(data.phone);
    } else {
        $('#preview-phone').html('<em>Not set</em>');
    }
    if (data.store_description) {
        $('#preview-description').text(data.store_description);
    } else {
        $('#preview-description').html('<em>No description added</em>');
    }
}

function showMessage(message, type) {
    const msgDiv = $('#profile-message');
    msgDiv.text(message);
    msgDiv.removeClass('success error').addClass(type);
    msgDiv.show();
}

function hideMessage() {
    $('#profile-message').hide();
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
