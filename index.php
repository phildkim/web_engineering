<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link type="text/css" rel="stylesheet" href="./css/style.css">
  <link type="text/css" rel="stylesheet" href="./css/font-awesome.min.css">
  <style type="text/css">

  </style>
  <title>COMP 484</title>
  <script type="text/javascript" src="./js/jquery.min.js"></script>
  <script type="text/javascript">
    const messages = [
      "username:Enter only letters in this field.",
      "password:Enter at least an upper-case, lower-case and number in this field.",
    ];
    const isEmpty = (field) => (field === "" ? true : false);
    const isValidFields = (id, field) => {
      switch (id) {
        case 0:
          return /^[A-Za-z]+$/.test(field);
        case 1:
          return /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/.test(field);
        default:
          break;
      }
    };
    const validateInputFields = (id, field, input) => {
      let valid = false;
      if (isEmpty(field)) {
        showInvalid(input, `${messages[id].split(":")[0]} is required`);
      } else if (!isValidFields(id, field)) {
        showInvalid(input, `${messages[id].split(":")[1]}`);
      } else {
        showValid(input, "success");
        valid = true;
      }
      return valid;
    };
    const showInvalid = (input, message) => {
      const inputFormText = input.parentElement;
      inputFormText.classList.remove("success");
      inputFormText.classList.add("error");
      const error = inputFormText.querySelector("span");
      error.textContent = message;
    };
    const showValid = (input, type) => {
      const inputFormText = input.parentElement;
      inputFormText.classList.remove("error");
      inputFormText.classList.add(type);
      const error = inputFormText.querySelector("span");
      error.textContent = "";
    };
    const debounce = (fn, delay) => {
      let timeout;
      return (...args) => {
        if (timeout) {
          clearTimeout(timeout);
        }
        timeout = setTimeout(() => {
          fn.apply(null, args);
        }, delay);
      };
    };
    window.addEventListener("load", function() {
      var message = document.getElementById("message");
      var username = document.getElementById("username");
      var password = document.getElementById("password");
      var validation = document.getElementById("validation");

      validation.addEventListener("input", debounce(function(e) {
        switch (e.target.id) {
          case "username":
            validateInputFields(0, username.value.trim(), username);
            break;
          case "password":
            validateInputFields(1, password.value.trim(), password);
            break;
          default:
            break;
        }
      }, 1000));
    });

    $(document).ready(function() {
    	var formstate;

      // open login model when nav login input clicked
      $('#login').click(function() {
        formstate = $('#formstate2').val();
        $('#title').html('LOGIN');
        $('#formContainer').css('display', 'block');
      });

      // open register model when nav register input clicked
      $('#register').click(function() {
        formstate = $('#formstate1').val();
        $('#title').html('REGISTER');
        $('#formContainer').css('display', 'block');
      });

      // clear error message while user enters username
      $('#username').focus(function() {
        $('#message').css('display', 'none');
      });

      // clear error message while user enters password
      $('#password').focus(function() {
        $('#message').css('display', 'none');
      });

      // close model when x icon clicked
      $('#closeIcon').click(function() {
        $('#formContainer').css('display', 'none');
      });

      // close model when cancel input clicked
      $('#cancel').click(function() {
        $('#formContainer').css('display', 'none');
      });

      // login or register user when form is submitted
      $('#validation').submit(function(e) {
        e.preventDefault();
        var username = $('#username').val().trim();
        var password = $('#password').val().trim();
        if (username != '' && password != '') {
          $.ajax({
            url: '/server.php',
            type: 'post',
            dataType: 'json',
            data: {
              username: username,
              password: password,
              formstate: formstate
            },
            success: function(res) {
              if (res.code == 200) {
                window.location = res.msg;
              } else {
                $('#message').html(res.msg);
                $('#message').css('display', 'block');
              }
            }
          });
        } else {
          $('#message').html('Username and Password required.');
          $('#message').css('display', 'block');
        }
      });
    });

  </script>
</head>
<body>
  <!-- navigation bar -->
  <div class="nav">
    <ul>
      <li>
        <a id="register" href="#">
          <b>REGISTER</b>
          <input id="formstate1" type="hidden" name="formstate" value="1">
        </a>
      </li>
      <li>
        <a id="login" href="#">
          <b>LOGIN</b>
          <input id="formstate2" type="hidden" name="formstate" value="2">
        </a>
      </li>
    </ul>
  </div>

  <!-- login and register form -->
  <div class="modal" id="formContainer">
    <form class="modal-content animate" id="validation" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

      <div class="titleContainer">
        <span class="close" id="closeIcon" title="Close Modal">&times;</span>
        <h1 id="title"></h1>
      </div>

      <div class="container">

        <div class="inputForm">
          <label for="username"><b>Username:</b></label>
          <span></span>
          <input id="username" name="username" type="text" autocomplete="off">
        </div>

        <div class="inputForm">
          <label for="password"><b>Password:</b></label>
          <span></span>
          <input id="password" name="password" type="password" autocomplete="off">
        </div>

        <div class="submitForm">
          <input class="submitBtn" name="submit" type="submit" value="submit">
          <span id="message"></span>
        </div>

      </div>

      <div class="container" id="cancelContainer">
        <input class="cancelBtn" id="cancel" type="button" value="cancel">
        <span class="password">Forgot <a href="#">password?</a></span>
      </div>

    </form>
  </div>

</body>
</html>