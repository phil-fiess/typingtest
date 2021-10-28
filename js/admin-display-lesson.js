//displays message content in admin section

jQuery(document).ready(function($) {

    let competency = $('select[name="competency"]').val();
    let level = $('select[name="level"]').val();

    $.ajax({
        type: 'POST',
        url: ajax_object.ajax_url,
        data: {
            action: "typingtest_display_admin_lesson",
            competency: competency,
            level: level
        },
        success: function(response) {
            $('#typingtest-admin-lesson-content').text(response);
        },
        error: function(error) {
            alert(error);
        }
    });

    $('#typingtest-admin-wrapper select').on('change', function() {
        let competency = $('select[name="competency"]').val();
        let level = $('select[name="level"]').val();
    
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: "typingtest_display_admin_lesson",
                competency: competency,
                level: level
            },
            success: function(response) {
                $('#typingtest-admin-lesson-content').text(response);
            },
            error: function(error) {
                alert(error);
            }
        });
    });
});