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
