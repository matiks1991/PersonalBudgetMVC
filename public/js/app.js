// Add jQuery Validation plugin method for a valid password
// Valid passwords contain at least ano letter and one number

$.validator.addMethod('validPassword', 
    function(value, element, param) {
        if (value != ''){
            if (value.match(/.*[a-z]+.*/i) == null){
                return false;
            }
            if (value.match(/.*\d+.*/i) == null){
                return false;
            }
        }
        return true;
    },
    'Hasło musi zawierać minimum jedną literę oraz jedną cyfrę'
);

$.validator.addMethod('validName',
    function(value, element, param) {
        if (value != ''){
            if (value.match(/.*[A-Za-zżźćńółęąśŻŹŚŁ]+.*/i) == null){
                return false;
            }
        }
        return true;
    }
);

$(document).ready(function() {
    $("#show_hide_password").click(function() {
        $("#show_hide_password_icon").toggleClass('icon-eye-off');

        var input =$("#inputPassword");

        if(input.attr("type") === "password"){
            input.attr('type', 'text');
        }else{
            input.attr('type', 'password');
        }
    });
});