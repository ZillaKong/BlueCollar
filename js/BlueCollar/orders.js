$(document).ready(function(){
    loadOrders();

    // Filter button clicks
    $(document).on('click', '.filter-btn', function(){
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        const filter = $(this).data('filter');
        filterOrders(filter);
    });

    // View order details
    $(document).on('click', '.order-card', function(){
        const orderId = $(this).data('order-id');
        loadOrderDetails(orderId);
    });

    // Close modal
    $('#close-modal').on('click', closeModal);
    $('#order-modal').on('click', function(e){
        if (e.target === this) closeModal();
    });

    // Modal action buttons (delegated)
    $(document).on('click', '#btn-checkout', function(){
        const orderId = $(this).data('order-id');
        openPaymentModalForOrder(orderId);
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

    $(document).on('click', '#btn-cancel', function(){
        const orderId = $(this).data('order-id');
        if (confirm('Are you sure you want to cancel this order?')) {
            cancelOrder(orderId);
        }
    });

    $(document).on('click', '#btn-continue', function(){
        const storeId = $(this).data('store-id');
        const orderId = $(this).data('order-id');
        window.location.href = `storefront.php?id=${storeId}&order_id=${orderId}`;
    });

    // Update quantity in modal
    $(document).on('change', '.item-quantity', function(){
        const itemId = $(this).data('item-id');
        const quantity = parseInt($(this).val());
        updateItemQuantity(itemId, quantity);
    });

    // Remove item from modal
    $(document).on('click', '.remove-item-btn', function(){
        const itemId = $(this).data('item-id');
        if (confirm('Remove this item from the order?')) {
            removeItem(itemId);
        }
    });
});

let allOrders = [];
let currentOrderId = null;

function loadOrders(){
    $('#orders-loading').show();
    $('#orders-list').empty();
    $('#no-orders').hide();

    $.ajax({
        url: '../../actions/order_actions.php',
        type: 'GET',
        data: { action: 'get_orders' },
        dataType: 'json',
        success: function(data){
            $('#orders-loading').hide();
            allOrders = data;
            
            if (data.length > 0) {
                displayOrders(data);
            } else {
                $('#no-orders').show();
            }
        },
        error: function(xhr, status, error){
            $('#orders-loading').hide();
            console.error('Error loading orders:', error);
            $('#orders-list').html('<p class="error-message">Failed to load orders. Please try again.</p>');
        }
    });
}

function displayOrders(orders){
    const container = $('#orders-list');
    container.empty();

    $.each(orders, function(index, order){
        const statusClass = getStatusClass(order.status);
        const statusLabel = getStatusLabel(order.status);
        const storeName = order.store_name || order.company_name || 'Unknown Store';
        const totalAmount = order.total_amount ? '$' + parseFloat(order.total_amount).toFixed(2) : 'N/A';
        const createdDate = formatDate(order.created_at);
        
        const card = `
            <div class="order-card" data-order-id="${order.order_id}" data-status="${order.status}">
                <div class="order-card-header">
                    <div class="order-invoice">
                        <span class="invoice-label">Invoice #</span>
                        <span class="invoice-number">${escapeHtml(order.invoice_number)}</span>
                    </div>
                    <span class="order-status ${statusClass}">${statusLabel}</span>
                </div>
                <div class="order-card-body">
                    <div class="order-store">
                        <span class="store-icon">🏪</span>
                        <span class="store-name">${escapeHtml(storeName)}</span>
                    </div>
                    <div class="order-meta">
                        <span class="order-items">${order.item_count} item(s)</span>
                        <span class="order-total">${totalAmount}</span>
                    </div>
                </div>
                <div class="order-card-footer">
                    <span class="order-date">${createdDate}</span>
                    <span class="view-details">View Details →</span>
                </div>
            </div>`;
        container.append(card);
    });
}

function filterOrders(status){
    if (status === 'all') {
        displayOrders(allOrders);
    } else {
        const filtered = allOrders.filter(order => order.status === status);
        if (filtered.length > 0) {
            displayOrders(filtered);
        } else {
            $('#orders-list').html(`<p class="no-results">No ${status} orders found.</p>`);
        }
    }
}

function loadOrderDetails(orderId){
    currentOrderId = orderId;
    $('#modal-body').html('<div class="loader"></div><p>Loading order details...</p>');
    $('#modal-footer').empty();
    $('#order-modal').show();

    $.ajax({
        url: '../../actions/order_actions.php',
        type: 'GET',
        data: { action: 'get_order', order_id: orderId },
        dataType: 'json',
        success: function(response){
            if (response.status === 'success') {
                displayOrderDetails(response);
            } else {
                $('#modal-body').html(`<p class="error-message">${response.message}</p>`);
            }
        },
        error: function(xhr, status, error){
            console.error('Error loading order details:', error);
            $('#modal-body').html('<p class="error-message">Failed to load order details.</p>');
        }
    });
}

function displayOrderDetails(data){
    const order = data.order;
    const items = data.items;
    const isPending = order.status === 'pending';
    
    let itemsHtml = '';
    if (items.length > 0) {
        itemsHtml = '<div class="order-items-list">';
        $.each(items, function(index, item){
            const lineTotal = '$' + parseFloat(item.line_total).toFixed(2);
            const unitPrice = '$' + parseFloat(item.price_at_order).toFixed(2);
            
            itemsHtml += `
                <div class="order-item" data-item-id="${item.item_id}">
                    <div class="item-info">
                        <h4 class="item-name">${escapeHtml(item.product_name)}</h4>
                        <p class="item-brand">${escapeHtml(item.brand_name || 'Unknown brand')}</p>
                        <p class="item-code">Code: ${escapeHtml(item.product_code || 'N/A')}</p>
                    </div>
                    <div class="item-pricing">
                        <span class="unit-price">${unitPrice} each</span>
                        ${isPending ? 
                            `<input type="number" class="item-quantity" data-item-id="${item.item_id}" value="${item.quantity}" min="1">` :
                            `<span class="item-qty">Qty: ${item.quantity}</span>`
                        }
                        <span class="line-total">${lineTotal}</span>
                        ${isPending ? `<button class="remove-item-btn" data-item-id="${item.item_id}">&times;</button>` : ''}
                    </div>
                </div>`;
        });
        itemsHtml += '</div>';
    } else {
        itemsHtml = '<p class="no-items">No items in this order yet.</p>';
    }

    const totalAmount = '$' + parseFloat(data.calculated_total).toFixed(2);
    const statusLabel = getStatusLabel(order.status);
    const statusClass = getStatusClass(order.status);

    const detailsHtml = `
        <div class="order-details-header">
            <div class="detail-row">
                <span class="detail-label">Invoice Number:</span>
                <span class="detail-value">${escapeHtml(order.invoice_number)}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value order-status ${statusClass}">${statusLabel}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Store:</span>
                <span class="detail-value">${escapeHtml(order.store_name || order.company_name)}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Contact:</span>
                <span class="detail-value">${escapeHtml(order.supplier_phone || 'N/A')}</span>
            </div>
        </div>
        <h3 class="items-heading">Order Items</h3>
        ${itemsHtml}
        <div class="order-total-section">
            <span class="total-label">Total Amount:</span>
            <span class="total-value">${totalAmount}</span>
        </div>`;

    $('#modal-title').text('Order #' + order.invoice_number);
    $('#modal-body').html(detailsHtml);

    // Add action buttons based on status
    let footerHtml = '';
    if (isPending) {
        footerHtml = `
            <button id="btn-continue" class="btn-secondary" data-store-id="${order.storefront_id}" data-order-id="${order.order_id}">
                Continue Shopping
            </button>
            <button id="btn-cancel" class="btn-danger" data-order-id="${order.order_id}">
                Cancel Order
            </button>
            ${items.length > 0 ? `<button id="btn-checkout" class="btn-primary" data-order-id="${order.order_id}" data-total="${data.calculated_total}" data-invoice="${order.invoice_number}" data-items="${items.length}">Pay & Checkout</button>` : ''}
        `;
    } else if (order.status === 'completed') {
        footerHtml = `<p class="completed-message">This order was completed on ${formatDate(order.invoice_date)}</p>`;
    } else if (order.status === 'canceled') {
        footerHtml = `<p class="canceled-message">This order was canceled</p>`;
    }
    
    $('#modal-footer').html(footerHtml);
}

function updateItemQuantity(itemId, quantity){
    $.ajax({
        url: '../../actions/order_actions.php',
        type: 'POST',
        data: { 
            action: 'update_item',
            item_id: itemId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response){
            if (response.status === 'success') {
                // Reload order details to refresh totals
                loadOrderDetails(currentOrderId);
            } else {
                alert(response.message || 'Failed to update quantity.');
            }
        },
        error: function(){
            alert('Error updating quantity. Please try again.');
        }
    });
}

function removeItem(itemId){
    $.ajax({
        url: '../../actions/order_actions.php',
        type: 'POST',
        data: { 
            action: 'remove_item',
            item_id: itemId
        },
        dataType: 'json',
        success: function(response){
            if (response.status === 'success') {
                loadOrderDetails(currentOrderId);
                loadOrders(); // Refresh order list
            } else {
                alert(response.message || 'Failed to remove item.');
            }
        },
        error: function(){
            alert('Error removing item. Please try again.');
        }
    });
}

// ============================================
// PAYMENT MODAL FUNCTIONS
// ============================================

let currentPaymentOrder = null;

function openPaymentModalForOrder(orderId){
    // Get order data from the checkout button
    const $btn = $(`#btn-checkout[data-order-id="${orderId}"]`);
    const total = parseFloat($btn.data('total')) || 0;
    const invoice = $btn.data('invoice') || '';
    const itemCount = $btn.data('items') || 0;
    
    if (total <= 0) {
        alert('Cannot process payment for empty order.');
        return;
    }
    
    currentPaymentOrder = {
        orderId: orderId,
        invoiceNumber: invoice,
        total: total,
        itemCount: itemCount
    };
    
    // Populate payment modal
    $('#payment-invoice-number').text('#' + invoice);
    $('#payment-item-count').text(itemCount + ' item(s)');
    $('#payment-total').text('$' + total.toFixed(2));
    
    // Close order modal and show payment modal
    closeModal();
    $('#payment-modal').show();
}

function closePaymentModal(){
    $('#payment-modal').hide();
    $('#payment-email').val('');
    resetPaymentButton();
    currentPaymentOrder = null;
}

function resetPaymentButton(){
    const $btn = $('#proceed-payment-btn');
    $btn.prop('disabled', false);
    $btn.find('.btn-text').show();
    $btn.find('.btn-loader').hide();
}

function initiatePaystackPayment(){
    if (!currentPaymentOrder) {
        alert('No order selected for payment.');
        return;
    }
    
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
            order_id: currentPaymentOrder.orderId,
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
        amount: Math.round(currentPaymentOrder.total * 100), // Convert to kobo
        ref: reference,
        currency: 'NGN',
        metadata: {
            order_id: currentPaymentOrder.orderId,
            invoice_number: currentPaymentOrder.invoiceNumber,
            custom_fields: [
                {
                    display_name: "Order Number",
                    variable_name: "order_number",
                    value: currentPaymentOrder.invoiceNumber
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
                loadOrders(); // Refresh orders list
            } else {
                alert('Payment verification failed: ' + (response.message || 'Unknown error'));
                resetPaymentButton();
            }
        },
        error: function(){
            alert('Error verifying payment. Please refresh the page to check your order status.');
            closePaymentModal();
            loadOrders();
        }
    });
}

function showPaymentSuccess(data){
    // Show success alert at top of page
    const successHtml = `
        <div id="payment-success-dynamic" class="alert alert-success">
            <div class="alert-content">
                <span class="alert-icon">✓</span>
                <div class="alert-text">
                    <strong>Payment Successful!</strong>
                    <p>Your order has been completed. Amount: $${parseFloat(data.amount).toFixed(2)}</p>
                </div>
                <button class="alert-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
        </div>`;
    
    // Remove any existing alerts and add new one
    $('.alert').remove();
    $('#body-container').prepend(successHtml);
    
    // Scroll to top to show success message
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function isValidEmail(email){
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Legacy checkout function (kept for reference, now redirects to payment)
function checkoutOrder(orderId){
    openPaymentModalForOrder(orderId);
}

function cancelOrder(orderId){
    $.ajax({
        url: '../../actions/order_actions.php',
        type: 'POST',
        data: { 
            action: 'cancel',
            order_id: orderId
        },
        dataType: 'json',
        success: function(response){
            if (response.status === 'success') {
                alert('Order canceled.');
                closeModal();
                loadOrders();
            } else {
                alert(response.message || 'Failed to cancel order.');
            }
        },
        error: function(){
            alert('Error canceling order. Please try again.');
        }
    });
}

function closeModal(){
    $('#order-modal').hide();
    currentOrderId = null;
}

function getStatusClass(status){
    switch(status) {
        case 'pending': return 'status-pending';
        case 'completed': return 'status-completed';
        case 'canceled': return 'status-canceled';
        default: return '';
    }
}

function getStatusLabel(status){
    switch(status) {
        case 'pending': return 'Pending';
        case 'completed': return 'Completed';
        case 'canceled': return 'Canceled';
        default: return status;
    }
}

function formatDate(dateStr){
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function escapeHtml(text){
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

