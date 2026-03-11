$(document).ready(function() {
    // Function to check if all required fields are filled
    function areRequiredFieldsFilled() {
        let filled = true;

        // Check all required fields
        $('input[required]').each(function() {
            const value = $.trim($(this).val()); // Get trimmed value
            const messageField = $('#' + $(this).attr('id') + 'Message');

            if (value === '') {
                filled = false;
                $(this).addClass('input-error'); // Add error class for visual feedback
                messageField.text('This field is required').css('color', 'red'); // Show required message
            } else {
                $(this).removeClass('input-error'); // Remove error class if field is filled
                messageField.text(''); // Clear message
            }
        });

        return filled; // Return whether all fields are filled
    };