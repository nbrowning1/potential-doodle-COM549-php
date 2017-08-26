$(document).ready(function() {
  
  $(document).on('submit', 'form#account-form', function(e) {
    e.preventDefault();
    
    // clear down error fields from previous attempt
    var usernameErrorEl = $('#username-error');
    usernameErrorEl.text('');
    
    var formData = serializeForm('#account-form');
    
    $.ajax({
      url: 'validate_account_recovery.php',
      type: 'POST',
      data: {
        username: formData['username']
      },
      success: function(data) {
        if (data.error) {
          if (data.usernameError) {
            usernameErrorEl.text(data.usernameError);
          }
        } else {
          $.ajax({
            url: 'recover.php',
            type: 'POST',
            data: {
              username: formData['username']
            },
            success: function(html) {
              // replace current page content
              document.open();
              document.write(html);
              document.close();
            }
          });
        }
      }
    });
    
    return false;
  });
  
  $(document).on('submit', 'form#recovery-form', function(e) {
    e.preventDefault();
    
    // clear down error fields from previous attempt
    var answerErrorEl = $('#answer-error');
    answerErrorEl.text('');
    
    var formData = serializeForm('#recovery-form');
    
    $.ajax({
      url: 'validate_recovery_question.php',
      type: 'POST',
      data: {
        username: formData['username'],
        answer: formData['answer']
      },
      success: function(data) {
        if (data.error) {
          if (data.answerError) {
            answerErrorEl.text(data.answerError);
          }
        } else {
          $.ajax({
            url: 'recover.php',
            type: 'POST',
            data: {
              username: formData['username'],
              answer: formData['answer']
            },
            success: function(html) {
              // replace current page content
              document.open();
              document.write(html);
              document.close();
            }
          });
        }
      }
    });
  });
  
  $(document).on('submit', 'form#reset-password-form', function(e) {
    e.preventDefault();
    
    // clear down error fields from previous attempt
    var passwordErrorEl = $('#password-error');
    var confirmPasswordErrorEl = $('#confirm-password-error');
    passwordErrorEl.text('');
    confirmPasswordErrorEl.text('');
    
    var formData = serializeForm('#reset-password-form');
    
    $.ajax({
      url: 'validate_new_password.php',
      type: 'POST',
      data: {
        username: formData['username'],
        answer: formData['answer'],
        password: formData['password'],
        confirmPassword: formData['confirmPassword']
      },
      success: function(data) {
        if (data.error) {
          if (data.passwordError) {
            passwordErrorEl.text(data.passwordError);
          }
          if (data.confirmPasswordError) {
            confirmPasswordErrorEl.text(data.confirmPasswordError);
          }
        } else {
          window.location = '../login.html';
          // $('.snackbar').fadeIn(400).delay(3000).fadeOut(400);
        }
      }
    });
  });

});