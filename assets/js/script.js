// Real-time form validation
$(document).ready(function(){
    // Product form validation
    $('#productForm').on('input', function(){
        let isValid = true;
        $('.required-field').each(function(){
            if($(this).val().trim() === ''){
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        $('#submitBtn').prop('disabled', !isValid);
    });
// ප්‍රමාණය යාවත්කාලීන කිරීම
function updateQuantity(productId, action, amount) {
    $.ajax({
        url: 'admin/products/update_quantity.php',
        type: 'POST',
        dataType: 'json',
        data: {
            product_id: productId,
            action: action,
            amount: amount
        },
        success: function(response) {
            if(response.status === 'success') {
                // අදාළ පේළියේ ප්‍රමාණය යාවත්කාලීන කරන්න
                $(`#quantity-${response.product_id}`).text(response.new_quantity);
                
                // සාර්ථක පණිවුණය පෙන්වන්න
                showAlert('success', 'ප්‍රමාණය සාර්ථකව යාවත්කාලීන කරන ලදී');
            } else {
                showAlert('danger', response.message || 'දෝෂයක් ඇතිවිය');
            }
        },
        error: function() {
            showAlert('danger', 'සේවාදායකයා සමඟ සම්බන්ධතාවය අසාර්ථක විය');
        }
    });
}

// ප්‍රමාණය වැඩි කිරීම
$(document).on('click', '.btn-increase', function() {
    const productId = $(this).data('id');
    const amount = parseInt($(this).data('amount') || 1);
    updateQuantity(productId, 'increase', amount);
});

// ප්‍රමාණය අඩු කිරීම
$(document).on('click', '.btn-decrease', function() {
    const productId = $(this).data('id');
    const amount = parseInt($(this).data('amount') || 1);
    updateQuantity(productId, 'decrease', amount);
});
    // Image preview functionality
    $('#productImage').change(function(e){
        const file = e.target.files[0];
        if(file){
            const reader = new FileReader();
            reader.onload = function(e){
                $('#imagePreview').html(
                    `<img src="${e.target.result}" class="img-thumbnail" style="max-height:200px">`
                );
            }
            reader.readAsDataURL(file);
        }
    });
});
