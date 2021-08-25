<?php
session_start();
if (!isset($_SESSION['auth'])) {
  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" rel="stylesheet" href="./css/style.css">
    <link type="text/css" rel="stylesheet" href="./css/font-awesome.min.css">
    <style type="text/css">
      table, th, td {
        border: 1.5px solid black;
        border-collapse: collapse;
      }
      table.users {
        margin-left: auto; 
        margin-right: auto;
      }
      .users {
        background-color: white;
      }
      .container {
        margin: 0;
        padding: 0;
      }
      #table,
      #user {
        text-align: center;
      }
      #user {
        height: 250px;
        padding-top: 40px;
        padding-bottom: 20px;
      }
      #admin {
        height: fit-content;
        padding-bottom: 40px;
      }
      #toggle {
        font-size: larger;
        font-weight: bold;
        text-transform: uppercase;
      }
    </style>
    <title>Lab 2 - HOME</title>
    <script type="text/javascript" src="./js/jquery.min.js"></script>
    <script type="text/javascript">
      window.addEventListener('load', function(){
        var user = document.getElementById('user');
        var admin = document.getElementById('admin');
      	var toggle = document.getElementById('toggle');
        // random background color when user clicks toggle button
      	toggle.addEventListener('click', function(){
      		var hex = '0123456789ABCDEF';
      		var color = '#';
      		for (let i = 0; i < 6; i++) {
      			color += hex[Math.floor(1 + Math.random() * 15)];
      		}
          user.style.backgroundColor = color;
      	});

        admin.style.backgroundColor = '#eee';
      });
    </script>
  </head>
  <body class="auth">
    <div class="nav">
      <ul>
        <li>
          <a href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
    <div class="container">
      <!-- display username and toggle button for background color -->
      <div id="user">
        <h1>Welcome <?= $_SESSION['name'] ?>!</h1>
        <br/>
        <input id="toggle" type="button" value="Toggle">
      </div>
      <!-- display table of all users if admin is authenticated -->
      <?php
        include('./server.php');
        $sql = 'SELECT * FROM users ORDER BY username ASC';
        $query = $mysqldb->prepare($sql);
        $query->execute();
        $data = $query->get_result();
        if ($_SESSION['admin']) {
        	print("<div id='admin'><hr><br><table class='users'><h2 id='table'>Users Table</h2><tr><th>userid</th><th>username</th><th>password</th></tr>");
          foreach ($data as $key => $value) {
            $id = $value['userid'];
            $user = $value['username'];
            $pass = $value['password'];
            print("<tr><td>$id</td>"."<td>$user</td>"."<td>$pass</td><tr>");
          }
          print("</table></div><hr>");
        }
        $mysqldb->close();
      ?>
    </div>
  </body>
</html>