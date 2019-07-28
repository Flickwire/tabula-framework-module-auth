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
        password: {
          identifier : 'password',
          rules: [
            {
              type   : 'regExp[/[!"#$%&\'()*+,\\-./:;<=>?@[\\]^_`{|}~\\\\]/]',
              prompt : 'Your password must contain a special character\n(!"#$%&\'()*+,-./:;<=>?@[]^_`{|}~\\)'
            },
            {
              type   : 'regExp[/[a-z]/]',
              prompt : 'Your password must contain at least one lowercase character'
            },
            {
              type   : 'regExp[/[A-Z]/]',
              prompt : 'Your password must contain at least one uppercase character'
            },
            {
              type   : 'regExp[/[0-9]/]',
              prompt : 'Your password must contain at least one number'
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