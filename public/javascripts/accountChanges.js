$('#apply').prop("disabled", true)

$(function(){
    $('form :input').change(function(e){
        $('#apply').prop("disabled", false)
    });
});

$(function () {
    $('.check').change(function (e) {
        if (e.target.id == 'none' && e.target.checked == true) {
            console.log($('#sms').prop('checked', false))
            console.log($('#email').prop('checked', false))
            console.log($('#google').prop('checked', false))
        } else {
            console.log($('#none').prop('checked', false))
        }
    });
})
