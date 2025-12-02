$(document).ready(function(){
    fetchStorefrontData();
});

function fetchStorefrontData(){
    $.ajax({
        url: '../actions/get_storefront.php',
        type: 'GET',
        data: { admin: 1 },
        dataType: 'json',
        success: function(data){
            const tableBody = $('#storefrontTable tbody');
            tableBody.empty();

            if (data.length > 0){
                $.each(data, function(index, store){
                    const storeName = store.store_name || 'Not Set';
                    const description = store.store_description 
                        ? (store.store_description.length > 50 
                            ? store.store_description.substring(0, 50) + '...' 
                            : store.store_description)
                        : 'No description';
                    
                    const row = `<tr>
                            <td>${store.store_id}</td>
                            <td>${escapeHtml(storeName)}</td>
                            <td>${store.seller_id}</td>
                            <td>${store.product_count}</td>
                            <td title="${escapeHtml(store.store_description || '')}">${escapeHtml(description)}</td>
                            <td>
                                <button onclick="viewStorefront(${store.store_id})">View</button>
                            </td>
                        </tr>`;
                    tableBody.append(row);
                });
            } else {
                tableBody.append('<tr><td colspan="6">No storefronts found.</td></tr>');
            }
        },
        error: function(xhr, status, error){
            console.error('Error fetching storefronts:', error);
            $('#storefrontTable tbody').html('<tr><td colspan="6">Error loading storefronts.</td></tr>');
        }
    });
}

function viewStorefront(storeId){
    window.open('BlueCollar/storefront.php?id=' + storeId, '_blank');
}

function escapeHtml(text){
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

