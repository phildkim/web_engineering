<?php

define('host', 'localhost');
define('user', 'root');
define('pass', 'root');
define('db', 'Lab2');
define('regexpUsername', '/^[a-zA-Z]+$/');
define('regexpPassword', '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/');
define('existUsername', 'Username exists, please try again.');
define('errorUsername', 'Invalid username, please try again.');
define('errorPassword', 'Invalid password, please try again.');
define('invalidUsername', 'Enter only letters in username field.');
define('invalidPassword', 'Enter at least an upper-case, lower-case and number in password field.');


session_start();
$mysqldb = new mysqli(host, user, pass, db);
if ($mysqldb->connect_error) {
  die('ERROR: '.$mysqldb->connect_error);
}


if (isset($_POST['formstate'])) {
  $formstate = $_POST['formstate'];
  settype($formstate, 'integer');
}


function validate($regex, $post) {
  $field = '';
  if (preg_match($regex, $post) && isset($post)) {
    $field = $post;
    settype($field, 'string');
  }
  return $field;
}


// register
if ($formstate == 1) {
  if ($mysql = $mysqldb->prepare('SELECT password FROM users WHERE username=?')) {
    // check username using regular expression
    $username = validate(regexpUsername, $_POST['username']);
    if ($username == '') {
      echo json_encode(['code' => 403, 'msg' => invalidUsername]);
      $mysql->close();
      $mysqldb->close();
      exit();
    }
    $mysql->bind_param('s', $username);
    $mysql->execute();
    $mysql->store_result();
    if ($mysql->num_rows() == 0) {
      // check password using regular expression
      $password = validate(regexpPassword, $_POST['password']);
      if ($password == '') {
        echo json_encode(['code' => 403, 'msg' => invalidPassword]);
        $mysql->close();
        $mysqldb->close();
        exit();
      }
      // create password hash, then insert username and password hash into database table
      $passwordHash = password_hash($password, PASSWORD_DEFAULT);
      $mysql = $mysqldb->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
      $mysql->bind_param('ss', $username, $passwordHash);
      $mysql->execute();
      session_regenerate_id();
      $_SESSION['auth'] = TRUE;
      $_SESSION['admin'] = $userid == 1 ? TRUE : FALSE;
      $_SESSION['name'] = $username;
      echo json_encode(['code' => 200, 'msg' => 'home.php']);
      $mysql->close();
      $mysqldb->close();
      exit();
    } else {
      echo json_encode(['code' => 404, 'msg' => existUsername]);
    }
  }
}

// login
if ($formstate == 2) {
  if ($mysql = $mysqldb->prepare('SELECT userid, password FROM users WHERE username=?')) {
    // check username using regular expression
    $username = validate(regexpUsername, $_POST['username']);
    if ($username == '') {
      echo json_encode(['code' => 403, 'msg' => invalidUsername]);
      $mysql->close();
      $mysqldb->close();
      exit();
    }
    $mysql->bind_param('s', $username);
    $mysql->execute();
    $mysql->store_result();
    if ($mysql->num_rows() == 0) {
      echo json_encode(['code' => 404, 'msg' => errorUsername]);
    } else {
      // check password using regular expression
      $passwordText = validate(regexpPassword, $_POST['password']);
      if ($passwordText == '') {
        echo json_encode(['code' => 403, 'msg' => invalidPassword]);
        $mysql->close();
        $mysqldb->close();
        exit();
      }
      $mysql->bind_result($userid, $password);
      $mysql->fetch();
      // check password with password hash from database table
      if (password_verify($passwordText, $password)) {
        session_regenerate_id();
        $_SESSION['auth'] = TRUE;
        $_SESSION['admin'] = $userid == 1 ? TRUE : FALSE;
        $_SESSION['name'] = $_POST['username'];
        echo json_encode(['code' => 200, 'msg' => 'home.php']);
        $mysql->close();
        $mysqldb->close();
        exit();
      } else {
        echo json_encode(['code' => 404, 'msg' => errorPassword]);
      }
    }
  }
}

?>