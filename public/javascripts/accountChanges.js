$('#apply').hide()

$(function(){
    $('form :input').change(function(e){
        $('#apply').show()
    });
});
