$(document).ready(function() {
  
  $(document).on('submit', 'form#register-form', function(e) {
    e.preventDefault();
    
    // clear down error fields from previous attempt
    var usernameErrorEl = $('#username-error');
    var passwordErrorEl = $('#password-error');
    var confirmPasswordErrorEl = $('#confirm-password-error');
    var recoveryQuestionErrorEl = $('#recovery-question-error');
    var recoveryAnswerErrorEl = $('#recovery-answer-error');
    var validationErrorEl = $('#validation-error');
    usernameErrorEl.text('');
    passwordErrorEl.text('');
    confirmPasswordErrorEl.text('');
    recoveryQuestionErrorEl.text('');
    recoveryAnswerErrorEl.text('');
    validationErrorEl.text('');
    
    var formData = serializeForm('#register-form');
    
    $.ajax({
      url: 'register.php',
      type: 'POST',
      data: {
        username: formData['username'],
        password: formData['password'],
        confirmPassword: formData['confirmPassword'],
        recoveryQuestion: formData['recoveryQuestion'],
        recoveryAnswer: formData['recoveryAnswer']
      },
      success: function(data) {
        if (data.error) {
          if (data.usernameError) {
            usernameErrorEl.text(data.usernameError);
          }
          if (data.passwordError) {
            passwordErrorEl.text(data.passwordError);
          }
          if (data.confirmPasswordError) {
            confirmPasswordErrorEl.text(data.confirmPasswordError);
          }
          if (data.recoveryQuestionError) {
            recoveryQuestionErrorEl.text(data.recoveryQuestionError);
          }
          if (data.recoveryAnswerError) {
            recoveryAnswerErrorEl.text(data.recoveryAnswerError);
          }
          if (data.validationFailure) {
            validationErrorEl.text(data.validationFailure);
          }
        } else if (data.redirectTo) {
          // was required because PHP header redirect didn't cooperate at all
          window.location=data.redirectTo;
        }
      }
    });
    
    return false;
  });

});