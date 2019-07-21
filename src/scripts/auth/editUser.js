$(function() {
    $('.ui.form')
    .form({
      fields: {
        name: {
          identifier  : 'name',
          rules: [
            {
              type   : 'empty',
              prompt : 'Please enter your name as you would like it displayed'
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
        },
        password2: {
          identifier : 'password2',
          rules: [
            {
              type   : 'match[password]',
              prompt : 'Your passwords must match'
            }
          ]
        }
      }
    });
});