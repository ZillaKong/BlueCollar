$(document).ready(function(){
    fetchCategoryData();

    $('#addCategoryBtn').click(function(){
        $('#addCategoryForm').toggle();
    });

    $('#addCategoryForm').submit(function(e){
        e.preventDefault();
        const categoryName = $('#categoryName').val().trim();
        if (categoryName) {
            addCategory(categoryName);
        } else {
            alert('Please enter a category name.');
        }
    });
});

function fetchCategoryData(){
    $.ajax({
        url: '../actions/get_category.php',
        type: 'GET',
        dataType: 'json',
        success: function(data){
            const tableBody = $('#categoryTable tbody');
            tableBody.empty();

            if (data.length > 0){
                $.each(data, function(index, category){
                    const row = `<tr>
                            <td>${category.category_id}</td>
                            <td>${category.category_name}</td>
                            <td>${category.total_products}</td>
                            <td>
                                <button onclick="editCategory(${category.category_id}, '${category.category_name}')">Update</button>
                                <button onclick="deleteCategory(${category.category_id})">Delete</button>
                            </td>
                        </tr>`;
                    tableBody.append(row);
                });
            }else{
                tableBody.append('<tr><td colspan="4">No categories found.</td></tr>')
            }
        }
    })
}

function editCategory(id, name){
    const newName = prompt("Enter new category name:", name);
    if (newName && newName !== name) {
        $.ajax({
            url: '../actions/update_category.php',
            type: 'POST',
            data: { category_id: id, category_name: newName },
            dataType: 'json',
            success: function(response){
                if (response.status === 'success') {
                    fetchCategoryData();
                } else {
                    alert('Error updating category: ' + response.message);
                }
            }
        });
    }
}

function deleteCategory(id){
    if (confirm("Are you sure you want to delete this category?")) {
        $.ajax({
            url: '../actions/delete_category.php',
            type: 'POST',
            data: { category_id: id },
            dataType: 'json',
            success: function(response){
                if (response.status === 'success') {
                    fetchCategoryData();
                } else {
                    alert('Error deleting category: ' + response.message);
                }
            }
        });
    }
}

function addCategory(categoryName){
    $.ajax({
        url: '../actions/add_category.php',
        type: 'POST',
        data: { category_name: categoryName },
        dataType: 'json',
        success: function(response){
            if (response.status === 'success') {
                fetchCategoryData();
                $('#addCategoryModal').hide();
                $('#categoryName').val('');
            } else {
                alert('Error adding category: ' + response.message);
            }
        }
    });
}
