window.addEventListener("load",function(event) {
    $('.ui.form')
    .form({
      fields: {
        name: {
            identifier  : 'password',
            rules: [
            {
              type   : 'empty',
              prompt : 'Please enter your password'
            }
            ]
        },
        email: {
            identifier  : 'email',
            rules: [
            {
                type   : 'email',
                prompt : 'Please enter a valid email address'
            }
            ]
        }
      }
    });
  });