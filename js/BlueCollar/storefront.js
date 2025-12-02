$(document).ready(function(){
    // Get store ID and order ID from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const storeId = parseInt(urlParams.get('id')) || 0;
    const orderId = parseInt(urlParams.get('order_id')) || 0;
    const categoryFilter = parseInt(urlParams.get('category')) || 0;

    // Store order context
    window.orderContext = {
        orderId: orderId,
        storeId: storeId,
        invoiceNumber: null,
        items: [],
        total: 0
    };

    if (storeId && storeId > 0) {
        fetchStorefrontData(storeId);
        
        // If we have an order context, load order details
        if (orderId) {
            loadOrderContext(orderId);
        } else {
            // Check if there's an active order with this store
            checkActiveOrder(storeId);
        }
    } else {
        showError('Invalid store selected.');
    }

    // Sort button click handlers
    $(document).on('click', '.sort-btn', function(){
        const sortType = $(this).data('sort');
        $('.sort-btn').removeClass('active');
        $(this).addClass('active');
        sortProducts(sortType);
    });

    // Add to order button click
    $(document).on('click', '.add-to-order-btn', function(){
        const $btn = $(this);
        const productId = $btn.data('product-id');
        const price = $btn.data('price');
        const $card = $btn.closest('.product-card');
        const quantity = parseInt($card.find('.qty-input').val()) || 1;
        
        addToOrder(productId, quantity, price, $btn);
    });

    // Quantity buttons
    $(document).on('click', '.qty-btn.minus', function(){
        const $input = $(this).siblings('.qty-input');
        let val = parseInt($input.val()) || 1;
        if (val > 1) $input.val(val - 1);
    });

    $(document).on('click', '.qty-btn.plus', function(){
        const $input = $(this).siblings('.qty-input');
        let val = parseInt($input.val()) || 1;
        $input.val(val + 1);
    });

    // Checkout button in floating bar - opens payment modal
    $('#bar-checkout-btn').on('click', function(){
        if (window.orderContext.orderId) {
            openPaymentModal();
        }
    });

    // Start new order button
    $('#start-order-btn').on('click', function(){
        startNewOrder();
    });

    // Payment modal controls
    $('#close-payment-modal, #cancel-payment-btn').on('click', function(){
        closePaymentModal();
    });

    $('#payment-modal').on('click', function(e){
        if (e.target === this) closePaymentModal();
    });

    // Proceed to pay button
    $('#proceed-payment-btn').on('click', function(){
        initiatePaystackPayment();
    });
});

// Store products globally for sorting
let allProducts = [];

function fetchStorefrontData(storeId){
    $.ajax({
        url: '../../actions/get_storefront.php',
        type: 'GET',
        data: { store_id: storeId },
        dataType: 'json',
        success: function(response){
            if (response.status === 'success') {
                displayStoreInfo(response.store_info);
                displayCategories(response.categories);
                allProducts = response.products;
                displayProducts(allProducts);
                
                // Update page title
                document.title = response.store_info.store_name + ' - BlueCollar';
            } else {
                showError(response.message || 'Failed to load store data.');
            }
        },
        error: function(xhr, status, error){
            console.error('Error fetching storefront:', error);
            showError('Error loading store data. Please try again.');
        }
    });
}

function displayStoreInfo(info){
    $('#store-name').text(info.store_name || 'Unnamed Store');
    $('#store-description').text(info.store_description || 'No description available.');
    $('#store-company').text(info.company_name ? 'Company: ' + info.company_name : '');
    $('#store-phone').text(info.phone ? 'Contact: ' + info.phone : '');
}

function displayCategories(categories){
    if (categories.length > 0) {
        // Get primary category (first one) - use .text() for safe rendering
        const primaryCategory = categories[0].category_name;
        $('#primary-category').text(primaryCategory);
        
        // If there are more categories, list them (escape each one)
        if (categories.length > 1) {
            const otherCategories = categories.slice(1).map(c => escapeHtml(c.category_name)).join(', ');
            $('#primary-category').append('<br><small>Also in: ' + otherCategories + '</small>');
        }
    } else {
        $('#primary-category').text('Not categorized');
    }
}

function displayProducts(products){
    const container = $('#storefront-products-container');
    container.empty();

    const hasOrderContext = window.orderContext.orderId > 0;

    if (products.length > 0) {
        $('#products-count').text(products.length + ' product(s) available');
        
        $.each(products, function(index, product){
            // Escape all user-generated content to prevent XSS
            const escapedName = escapeHtml(product.product_name);
            const description = escapeHtml(product.description) || 'No description available';
            const price = product.price ? '$' + parseFloat(product.price).toFixed(2) : 'Price not set';
            const priceValue = parseFloat(product.price) || 0;
            const brand = escapeHtml(product.brand_name) || 'Unknown brand';
            const category = escapeHtml(product.category_name) || 'Uncategorized';
            const stockStatus = getStockStatus(product.stock_quantity, product.availability_status);
            const isInStock = product.availability_status === 'in stock' && product.stock_quantity > 0;
            const productId = parseInt(product.product_id) || 0;
            
            // Check if product is already in order
            const inOrder = window.orderContext.items.find(item => item.product_id == productId);
            const addedClass = inOrder ? 'added' : '';
            const btnText = inOrder ? '✓ In Order' : 'Add to Order';
            
            let orderControls = '';
            if (hasOrderContext) {
                orderControls = `
                    <div class="quantity-selector">
                        <button class="qty-btn minus">-</button>
                        <input type="number" class="qty-input" value="${inOrder ? inOrder.quantity : 1}" min="1">
                        <button class="qty-btn plus">+</button>
                    </div>
                    <button class="add-to-order-btn ${addedClass}" 
                            data-product-id="${productId}" 
                            data-price="${priceValue}"
                            ${!isInStock ? 'disabled' : ''}>
                        ${!isInStock ? 'Out of Stock' : btnText}
                    </button>`;
            }
            
            const card = `
                <div class="product-card" data-product-id="${productId}" 
                     data-price="${priceValue}" 
                     data-brand="${brand.toLowerCase()}"
                     data-name="${escapedName.toLowerCase()}">
                    <div class="product-image-placeholder">
                        <span>📦</span>
                    </div>
                    <div class="product-info">
                        <h4 class="product-title">${escapedName}</h4>
                        <p class="product-brand">${brand}</p>
                        <p class="product-category-tag">${category}</p>
                        <p class="product-description">${description}</p>
                        <div class="product-details">
                            <span class="product-price">${price}</span>
                            <span class="product-stock ${stockStatus.class}">${stockStatus.text}</span>
                        </div>
                        ${orderControls}
                    </div>
                </div>`;
            container.append(card);
        });
    } else {
        container.html('<p class="no-products">No products available in this store.</p>');
        $('#products-count').text('0 products');
    }
}

function checkActiveOrder(storeId){
    $.ajax({
        url: '../../actions/order_actions.php',
        type: 'GET',
        data: { 
            action: 'check_active',
            storefront_id: storeId
        },
        dataType: 'json',
        success: function(response){
            if (response && response.status === 'success' && response.has_active_order) {
                window.orderContext.orderId = response.order_id;
                window.orderContext.invoiceNumber = response.invoice_number;
                loadOrderContext(response.order_id);
                updateStartOrderButton(true);
            } else {
                updateStartOrderButton(false);
            }
        },
        error: function(xhr, status, error){
            // Silently fail - order functionality may not be set up yet
            console.log('Order system not available or no active order.');
            updateStartOrderButton(false);
        }
    });
}

function startNewOrder(){
    const $btn = $('#start-order-btn');
    $btn.prop('disabled', true).text('Creating order...');
    
    $.ajax({
        url: '../../actions/order_actions.php',
        type: 'POST',
        data: {
            action: 'create',
            storefront_id: window.orderContext.storeId
        },
        dataType: 'json',
        success: function(response){
            if (response.status === 'success') {
                window.orderContext.orderId = response.order_id;
                window.orderContext.invoiceNumber = response.invoice_number;
                window.orderContext.items = [];
                window.orderContext.total = 0;
                
                updateStartOrderButton(true);
                updateOrderBar();
                
                // Refresh products to show add-to-order buttons
                displayProducts(allProducts);
                
                $('#order-status-text').text('Order #' + response.invoice_number + ' created! Add products below.');
            } else {
                $btn.prop('disabled', false).html('🛒 Start a New Order');
                alert(response.message || 'Failed to create order.');
            }
        },
        error: function(){
            $btn.prop('disabled', false).html('🛒 Start a New Order');
            alert('Error creating order. Please try again.');
        }
    });
}

function updateStartOrderButton(hasOrder){
    const $btn = $('#start-order-btn');
    const $statusText = $('#order-status-text');
    
    if (hasOrder && window.orderContext.orderId) {
        $btn.html('📝 Continue Order').removeClass('btn-start-order').addClass('btn-continue-order');
        $statusText.html('Active order: <strong>#' + window.orderContext.invoiceNumber + '</strong> - Add products below');
    } else {
        $btn.html('🛒 Start a New Order').removeClass('btn-continue-order').addClass('btn-start-order');
        $statusText.text('');
    }
    $btn.prop('disabled', false);
}

function loadOrderContext(orderId){
    $.ajax({
        url: '../../actions/order_actions.php',
        type: 'GET',
        data: { 
            action: 'get_order',
            order_id: orderId
        },
        dataType: 'json',
        success: function(response){
            if (response.status === 'success') {
                window.orderContext.orderId = response.order.order_id;
                window.orderContext.invoiceNumber = response.order.invoice_number;
                window.orderContext.items = response.items.map(item => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    price: item.price_at_order
                }));
                window.orderContext.total = response.calculated_total;
                
                updateOrderBar();
                
                // Refresh product display to show order controls
                displayProducts(allProducts);
            }
        },
        error: function(xhr, status, error){
            console.error('Error loading order context:', error);
        }
    });
}

function addToOrder(productId, quantity, price, $btn){
    if (!window.orderContext.orderId) {
        // Create a new order first
        $.ajax({
            url: '../../actions/order_actions.php',
            type: 'POST',
            data: {
                action: 'create',
                storefront_id: window.orderContext.storeId
            },
            dataType: 'json',
            success: function(response){
                if (response.status === 'success') {
                    window.orderContext.orderId = response.order_id;
                    window.orderContext.invoiceNumber = response.invoice_number;
                    // Now add the item
                    addItemToOrder(productId, quantity, price, $btn);
                } else {
                    alert(response.message || 'Failed to create order.');
                }
            },
            error: function(){
                alert('Error creating order. Please try again.');
            }
        });
    } else {
        addItemToOrder(productId, quantity, price, $btn);
    }
}

function addItemToOrder(productId, quantity, price, $btn){
    $btn.prop('disabled', true).text('Adding...');
    
    $.ajax({
        url: '../../actions/order_actions.php',
        type: 'POST',
        data: {
            action: 'add_item',
            order_id: window.orderContext.orderId,
            product_id: productId,
            quantity: quantity,
            price: price
        },
        dataType: 'json',
        success: function(response){
            if (response.status === 'success') {
                $btn.addClass('added').text('✓ In Order');
                
                // Update local order context
                const existingItem = window.orderContext.items.find(item => item.product_id == productId);
                if (existingItem) {
                    existingItem.quantity += quantity;
                } else {
                    window.orderContext.items.push({
                        product_id: productId,
                        quantity: quantity,
                        price: price
                    });
                }
                
                // Recalculate total
                window.orderContext.total = window.orderContext.items.reduce((sum, item) => 
                    sum + (item.quantity * item.price), 0
                );
                
                updateOrderBar();
            } else {
                $btn.prop('disabled', false).text('Add to Order');
                alert(response.message || 'Failed to add item.');
            }
        },
        error: function(){
            $btn.prop('disabled', false).text('Add to Order');
            alert('Error adding item. Please try again.');
        }
    });
}

function updateOrderBar(){
    if (window.orderContext.orderId) {
        $('#bar-invoice-number').text(window.orderContext.invoiceNumber);
        $('#bar-item-count').text(window.orderContext.items.length);
        $('#bar-total').text(parseFloat(window.orderContext.total).toFixed(2));
        $('#order-floating-bar').addClass('visible');
    } else {
        $('#order-floating-bar').removeClass('visible');
    }
}

// ============================================
// PAYMENT MODAL FUNCTIONS
// ============================================

function openPaymentModal(){
    if (!window.orderContext.orderId || window.orderContext.items.length === 0) {
        alert('Please add items to your order before checkout.');
        return;
    }
    
    // Populate payment modal with order info
    $('#payment-invoice-number').text('#' + window.orderContext.invoiceNumber);
    $('#payment-item-count').text(window.orderContext.items.length + ' item(s)');
    $('#payment-total').text('$' + parseFloat(window.orderContext.total).toFixed(2));
    
    // Show modal
    $('#payment-modal').show();
}

function closePaymentModal(){
    $('#payment-modal').hide();
    $('#payment-email').val('');
    resetPaymentButton();
}

function resetPaymentButton(){
    const $btn = $('#proceed-payment-btn');
    $btn.prop('disabled', false);
    $btn.find('.btn-text').show();
    $btn.find('.btn-loader').hide();
}

function initiatePaystackPayment(){
    const email = $('#payment-email').val().trim();
    
    if (!email || !isValidEmail(email)) {
        alert('Please enter a valid email address.');
        $('#payment-email').focus();
        return;
    }
    
    const $btn = $('#proceed-payment-btn');
    $btn.prop('disabled', true);
    $btn.find('.btn-text').hide();
    $btn.find('.btn-loader').show();
    
    // Initialize payment with backend
    $.ajax({
        url: '../../actions/order_actions.php',
        type: 'POST',
        data: {
            action: 'init_payment',
            order_id: window.orderContext.orderId,
            email: email
        },
        dataType: 'json',
        success: function(response){
            if (response.status === 'success') {
                // Use Paystack inline popup
                payWithPaystack(email, response.access_code, response.reference);
            } else {
                alert(response.message || 'Failed to initialize payment.');
                resetPaymentButton();
            }
        },
        error: function(){
            alert('Error initiating payment. Please try again.');
            resetPaymentButton();
        }
    });
}

function payWithPaystack(email, accessCode, reference){
    // Paystack inline payment
    const handler = PaystackPop.setup({
        key: PAYSTACK_PUBLIC_KEY,
        email: email,
        amount: Math.round(window.orderContext.total * 100), // Convert to kobo
        ref: reference,
        currency: 'NGN',
        metadata: {
            order_id: window.orderContext.orderId,
            invoice_number: window.orderContext.invoiceNumber,
            custom_fields: [
                {
                    display_name: "Order Number",
                    variable_name: "order_number",
                    value: window.orderContext.invoiceNumber
                }
            ]
        },
        callback: function(response){
            // Payment successful
            verifyPayment(response.reference);
        },
        onClose: function(){
            // User closed payment window
            resetPaymentButton();
        }
    });
    
    handler.openIframe();
}

function verifyPayment(reference){
    $.ajax({
        url: '../../actions/order_actions.php',
        type: 'GET',
        data: {
            action: 'verify_payment',
            reference: reference
        },
        dataType: 'json',
        success: function(response){
            if (response.status === 'success') {
                closePaymentModal();
                showPaymentSuccess(response);
            } else {
                alert('Payment verification failed: ' + (response.message || 'Unknown error'));
                resetPaymentButton();
            }
        },
        error: function(){
            alert('Error verifying payment. Please check your orders page.');
            window.location.href = 'orders.php';
        }
    });
}

function showPaymentSuccess(data){
    // Create success overlay - escape user data
    const successHtml = `
        <div id="payment-success-overlay" class="payment-success-overlay">
            <div class="success-content">
                <div class="success-icon">✓</div>
                <h2>Payment Successful!</h2>
                <p>Your order has been completed.</p>
                <div class="success-details">
                    <p><strong>Amount Paid:</strong> $${parseFloat(data.amount).toFixed(2)}</p>
                    <p><strong>Reference:</strong> ${escapeHtml(data.reference)}</p>
                </div>
                <button onclick="window.location.href='orders.php'" class="btn-primary">View My Orders</button>
            </div>
        </div>`;
    
    $('body').append(successHtml);
}

function isValidEmail(email){
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Legacy checkout function (for non-payment checkout if needed)
function checkoutOrder(orderId){
    openPaymentModal();
}

function sortProducts(sortType){
    let sortedProducts = [...allProducts];
    
    switch(sortType) {
        case 'name':
            sortedProducts.sort((a, b) => 
                (a.product_name || '').localeCompare(b.product_name || '')
            );
            break;
        case 'brand':
            sortedProducts.sort((a, b) => 
                (a.brand_name || '').localeCompare(b.brand_name || '')
            );
            break;
        case 'price-low':
            sortedProducts.sort((a, b) => 
                (parseFloat(a.price) || 0) - (parseFloat(b.price) || 0)
            );
            break;
        case 'price-high':
            sortedProducts.sort((a, b) => 
                (parseFloat(b.price) || 0) - (parseFloat(a.price) || 0)
            );
            break;
    }
    
    displayProducts(sortedProducts);
}

function getStockStatus(quantity, status){
    if (status === 'out_of_stock' || quantity <= 0) {
        return { text: 'Out of Stock', class: 'stock-out' };
    } else if (status === 'incoming_batch') {
        return { text: 'Incoming', class: 'stock-incoming' };
    } else if (quantity < 10) {
        return { text: 'Low Stock (' + quantity + ')', class: 'stock-low' };
    } else {
        return { text: 'In Stock', class: 'stock-in' };
    }
}

function escapeHtml(text){
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showError(message){
    $('#store-name').text('Store Not Found');
    $('#store-description').text(message);
    $('#primary-category').text('-');
    $('#storefront-products-container').html('<p class="error-message">' + message + '</p>');
    $('#products-count').text('');
}
