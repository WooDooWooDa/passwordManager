$('input').keydown(function (e) {
   validateNumberOnly(e);
});

function validateNumberOnly(e) {
    let key
    console.log(e.type)
    if (e.type === 'paste') {
        key = event.clipboardData.getData('text/plain');
    } else {
        key = String.fromCharCode(e.keyCode || e.which);
    }
    let regex = /[0-9\b\r\n]|\./;
    if(!regex.test(key)) {
        e.returnValue = false;
        if(e.preventDefault) e.preventDefault();
    }
}