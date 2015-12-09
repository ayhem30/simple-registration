/**
 * @author: Arem G. Aguinaldo
 * @date: 11/16/15
 * @email: ayhem30@gmail.com
 */
$(document).ready(function() {
    $.backstretch( base_url + "/img/backgrounds/1.jpg");
    $('#top-navbar-1').on('shown.bs.collapse', function(){
    	$.backstretch("resize");
    });
    $('#top-navbar-1').on('hidden.bs.collapse', function(){
    	$.backstretch("resize");
    });

    // form validation
    $("#registration-form").validate({
        rules: {
            "form-email": {
                required: true,
                email: true,
                maxlength: 255
            },
            "form-first-name": {
                required  : true,
                maxlength : 30
            },
            "form-last-name": {
                required  : true,
                maxlength : 30
            },
            "form-middle-name": {
                maxlength : 30
            },
            "form-phone-number": {
                maxlength : 11
            },
            "form-password": {
                required: true,
                minlength: 8,
                maxlength: 24
            },
            "form-confirm-password": {
                required: true,
                equalTo: "#form-password"
            }
        },
        messages: {
            "form-email": {
                required  : "Please enter a valid email address",
                email     : "Please enter a valid email address",
                maxlength : "Your email has reached its 255 character limit"
            },
            "form-first-name": {
                required: "Please enter your first name",
                maxlength: "Your first name has reached its 30 character limit"
            },
            "form-last-name": {
                required: "Please enter your last name",
                maxlength: "Your last name has reached its 30 character limit"
            },
            "form-middle-name": {
                maxlength: "Your middle name has reached its 30 character limit"
            },
            "form-phone-number": {
                maxlength: "Your phone number has reached its 11 character limit"
            },
            "form-password": {
                required: "Please provide a password",
                minlength: "Your password must be at least 8 characters long"
            },
            "form-confirm-password": {
                required: "Please provide a password",
                equalTo: "Please enter the same password as above"
            }
        },
        submitHandler: function() {
            var birthday = new Date($('.birthDay').val());
            var today = new Date();
            var pass = true;
            var age = today.getFullYear().toFixed() - birthday.getFullYear().toFixed();

            // button actions
            $('#success-message,#error-message').addClass('hide');
            $('.btnActions').attr('disabled',true).addClass('hide');
            $('#loader').removeClass('hide');

            if (age > 18) {
                var input = document.getElementById("form-profile-picture"),
                    formdata = false ,i = 0, len = input.files.length, file;

                if (window.FormData) {
                    formdata = new FormData();
                }

                for ( ; i < len; i++ ) {
                    file = input.files[i];
                    if (file.type.match(/image\/jpeg/) ||
                        file.type.match(/image\/png/) ||
                        file.type.match(/image\/jpg/) ||
                        file.type.match(/image\/gif/)) {
                        if (formdata) {
                            formdata.append("profile_picture", file);
                        }
                    } else {
                        // button actions
                        $('#loader').addClass('hide');
                        $('.btnActions').attr('disabled',false).removeClass('hide');

                        $('#error-message').html('Invalid Image. Allowable types are jpeg,png,gif.').removeClass('hide');
                        pass = false;
                    }
                }

                formdata.append("data",($("#registration-form").serialize()
                                        + '&form-age=' + encodeURIComponent(age)));

                if (formdata && pass) {
                    $.ajax({
                        url         : base_url + "/registration/save/",
                        type        : "POST",
                        data        : formdata,
                        dataType    : 'json',
                        processData : false,
                        contentType : false,
                        cache       : false,
                        success     : function (response) {
                            // button actions
                            $('#loader').addClass('hide');
                            $('.btnActions').attr('disabled',false).removeClass('hide');

                            if (response.hasError) {
                                $('#success-message').addClass('hide');
                                $('#error-message').html(response.errorMessage).removeClass('hide');
                            } else {
                                document.getElementById("registration-form").reset();
                                $('#error-message').addClass('hide');
                                $('#success-message').html(response.errorMessage).removeClass('hide');
                            }
                        }
                    });
                }

            } else {
                // button actions
                $('#loader').addClass('hide');
                $('.btnActions').attr('disabled',false).removeClass('hide');

                $('#success-message').addClass('hide');
                $('#error-message').html('You must be 18 years above.').removeClass('hide');
            }
        }
    });

    $('.required').on('focus', function() {
        $('#success-message,#error-message').addClass('hide');
    	$(this).removeClass('input-error');
    });

    $('#birthday').on('click',function(){
        $('#success-message,#error-message').addClass('hide');
        $(this).find('select').each(function(){
            if ($(this).val() != 0) {
               $(this).removeClass('input-error');
            }
        });
    });
    
    $('.registration-form').on('submit', function(e) {
    	$(this).find('.required').each(function(){
    		if( $(this).val() == "" && $(this).attr('id') != 'birthday') {
    			e.preventDefault();
    			$(this).addClass('input-error');
    		} else if ($(this).attr('id') == 'birthday'){
                $(this).find('select').each(function(){
                    if ($(this).val() == 0) {
                        $(this).addClass('input-error');
                    } else {
                        $(this).removeClass('input-error');
                    }
                });
            } else {
    			$(this).removeClass('input-error');
    		}
    	});
    });

    $('#add-phone-number').on('click', function(){
        $('#success-message,#error-message').addClass('hide');
          $('#additional').append(
              '<div class="form-group" id="phone-number-'+$('.form-phone-number').length+'">' +
                '<div class="input-group">' +
                '<label class="sr-only">Phone Number</label>' +
                '<input type="text" maxlength="11" name="form-phone-number[]" placeholder="Phone Number..." class="form-phone-number form-control">' +
                '<span class="input-group-btn"><button type="button" onclick="removeElement(this);" data-phone-id="'+$('.form-phone-number').length+'" class="btn btn-danger"><i class="fa fa-remove"></i></button></span>' +
                '</div>' +
              '</div>'
          );
    });

    var date = new Date();
    $("#birthday").birthdayPicker({
        "maxYear": date.getFullYear(),
        "monthFormat":"short"
    });
});

function removeElement(e){
    var id = $(e).data('phone-id');
    $('#phone-number-' + id).remove();
    return false;
}
