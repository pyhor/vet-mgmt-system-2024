$(document).ready(function() {
    function showCheckExistingMessage(field) {
        var value = $('#' + field).val().trim(); // Trim whitespace

        console.log("Checking field: " + field + " with value: '" + value + "'"); // Debugging log

        // Only proceed with the AJAX request if the value is not empty
        if (value !== '') {
            $.ajax({
                url: 'check_existing.php',
                type: 'POST',
                data: { field: field, value: value },
                dataType: 'json',
                success: function(response) {
                    console.log("Response: ", response); // Debugging log

                    var messageField = $('#' + field + 'Message');
                    if (response.status === 'exists') {
                        messageField.text(capitalizeFirstLetter(field) + ' already exists').css('color', 'red');
                    } else if (response.status === 'available') {
                        messageField.text(capitalizeFirstLetter(field) + ' is available').css('color', 'green');
                    } else if (response.status === 'empty') {
                        messageField.text('Please enter a value').css('color', 'orange');
                    }
                }
            });
        } else {
            $('#' + field + 'Message').text('Please enter a value').css('color', 'orange');
        }
    }

    // Attach the blur event to specific fields
    $('#vetId').on('blur', function() {
        showCheckExistingMessage($(this).attr('id'));
    });

    $('#vetUsername, #vetEmail, #vetPhoneNum').on('blur', function() {
        var value = $(this).val().trim(); // Trim whitespace
        console.log("Blur event triggered for field: " + $(this).attr('id') + " with value: '" + value + "'"); // Debugging log
        if (value !== '') {
            showCheckExistingMessage($(this).attr('id'));
        } else {
            $('#' + $(this).attr('id') + 'Message').text('Please enter a value').css('color', 'orange');
        }
    });
});