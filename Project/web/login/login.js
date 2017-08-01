$(document).ready(function() {
  
  $(document).on('submit', 'form#login-form', function(e) {
    e.preventDefault();
    
    // clear down error fields from previous attempt
    var usernameErrorEl = $('#username-error');
    var passwordErrorEl = $('#password-error');
    var validationErrorEl = $('#validation-error');
    usernameErrorEl.text('');
    passwordErrorEl.text('');
    validationErrorEl.text('');
    
    var formData = serializeForm('#login-form');
    
    $.ajax({
      url: 'login.php',
      type: 'POST',
      data: {
        username: formData['username'],
        password: formData['password']
      },
      success: function(data) {
        if (data.error) {
          if (data.usernameError) {
            usernameErrorEl.text(data.usernameError);
          }
          if (data.passwordError) {
            passwordErrorEl.text(data.passwordError);
          }
          if (data.validationFailure) {
            validationErrorEl.text(data.validationFailure);
          }
        }
      }
    });
    
    return false;
  });

});