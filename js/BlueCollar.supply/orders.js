$(document).ready(function(){
    loadSupplierOrders();

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
});

let allOrders = [];
let currentOrderId = null;

function loadSupplierOrders(){
    $('#orders-loading').show();
    $('#orders-list').empty();
    $('#no-orders').hide();

    $.ajax({
        url: '../../actions/supplier_order_actions.php',
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
        const buyerName = order.buyer_name || 'Unknown Buyer';
        const companyName = order.buyer_company || '';
        const totalAmount = order.total_amount ? 'GH₵' + parseFloat(order.total_amount).toFixed(2) : 'N/A';
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
                        <span class="store-icon">👤</span>
                        <div>
                            <span class="store-name">${escapeHtml(buyerName)}</span>
                            ${companyName ? `<br><small class="company-name">${escapeHtml(companyName)}</small>` : ''}
                        </div>
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
        url: '../../actions/supplier_order_actions.php',
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
    
    let itemsHtml = '';
    if (items.length > 0) {
        itemsHtml = '<div class="order-items-list">';
        $.each(items, function(index, item){
            const lineTotal = 'GH₵' + parseFloat(item.line_total).toFixed(2);
            const unitPrice = 'GH₵' + parseFloat(item.price_at_order).toFixed(2);
            
            itemsHtml += `
                <div class="order-item">
                    <div class="item-info">
                        <h4 class="item-name">${escapeHtml(item.product_name)}</h4>
                        <p class="item-brand">${escapeHtml(item.brand_name || 'Unknown brand')}</p>
                        <p class="item-code">Code: ${escapeHtml(item.product_code || 'N/A')}</p>
                    </div>
                    <div class="item-pricing">
                        <span class="unit-price">${unitPrice} each</span>
                        <span class="item-qty">Qty: ${item.quantity}</span>
                        <span class="line-total">${lineTotal}</span>
                    </div>
                </div>`;
        });
        itemsHtml += '</div>';
    } else {
        itemsHtml = '<p class="no-items">No items in this order.</p>';
    }

    const totalAmount = 'GH₵' + parseFloat(data.calculated_total).toFixed(2);
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
                <span class="detail-label">Buyer:</span>
                <span class="detail-value">${escapeHtml(order.buyer_name)}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Company:</span>
                <span class="detail-value">${escapeHtml(order.buyer_company || 'N/A')}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Contact:</span>
                <span class="detail-value">${escapeHtml(order.buyer_phone || 'N/A')}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">${escapeHtml(order.buyer_email || 'N/A')}</span>
            </div>
        </div>
        <h3 class="items-heading">Order Items</h3>
        ${itemsHtml}
        <div class="order-total-section" style="background: linear-gradient(135deg, #454B1B 0%, #5a6323 100%);">
            <span class="total-label">Total Amount:</span>
            <span class="total-value">${totalAmount}</span>
        </div>`;

    $('#modal-title').text('Order #' + order.invoice_number);
    $('#modal-body').html(detailsHtml);

    // Add order date info
    if (order.status === 'completed') {
        $('#modal-footer').html(`<p class="completed-message">Completed on ${formatDate(order.invoice_date)}</p>`);
    } else if (order.status === 'canceled') {
        $('#modal-footer').html(`<p class="canceled-message">This order was canceled</p>`);
    } else {
        $('#modal-footer').html(`<p>Order placed on ${formatDate(order.created_at)}</p>`);
    }
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

