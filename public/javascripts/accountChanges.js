$('#apply').prop("disabled", true)

$(function(){
    $('form :input').change(function(e){
        $('#apply').prop("disabled", false)
    });
});
