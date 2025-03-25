<?php
// Initialize the session
session_start();
require 'db_conn.php';
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>To-Do List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    
</head>
<body>
    <!-- Image and text -->
<nav class="navbar navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <img
        src="https://images.ctfassets.net/lzny33ho1g45/best-android-to-do-list-apps-p-img/501a7d8823758b5f40362191fe938dfe/file.png?w=1520&fm=jpg&q=30&fit=thumb&h=760"
        class="me-2"
        height="50"
        alt="To Do List"
        loading="lazy"
      />
      <b> Hello,<?php echo htmlspecialchars($_SESSION["username"]); ?></b> 
    </a>
    <span class="navbar-nav ">
            <a class="nav-link" href="#">
            </a>
    </span>
    <span class="navbar-nav ">
        <a class="nav-link" href="#">
        </a>
    </span> 
    <span class="navbar-nav ">
            <a class="nav-link" href="#">
            </a>
    </span>
    <span class="navbar-nav ">
        <a class="nav-link" href="#">
        </a>
    </span> 
    <span class="navbar-nav ">
            <a class="nav-link" href="#">
            </a>
    </span>
    <span class="navbar-nav ">
        <a class="nav-link" href="#">
        </a>
    </span> 
    <span class="navbar-nav ">
            <a class="nav-link" href="#">
            </a>
    </span>
    <span class="navbar-nav ">
        <a class="nav-link" href="#">
        </a>
    </span> 
    <span class="navbar-nav ">
            <a class="nav-link" href="#">
            </a>
    </span>
    <span class="navbar-nav ">
        <a class="nav-link" href="#">
        </a>
    </span> 
    <span class="navbar-nav ">
            <a class="nav-link" href="#">
            </a>
    </span>
    <span class="navbar-nav ">
        <a class="nav-link" href="#">
        </a>
    </span> 
    <span class="navbar-nav ">
            <a class="nav-link" href="#">
            </a>
    </span>
    <span class="navbar-nav ">
        <a class="nav-link" href="#">
        </a>
    </span> 
    <span class="navbar-nav ">
            <a class="nav-link" href="#">
            </a>
    </span>
    <span class="navbar-nav ">
        <a class="nav-link" href="#">
        </a>
    </span> 
    

    <span class="navbar-nav ">
            <a class="nav-link" href="../reset-password.php">
                  Reset password
            </a>
    </span>
    <span class="navbar-nav ">
        <a class="nav-link" href="../logout.php">
        Logout
        </a>
        </span>
    
    </nav>
    
  </div>
</nav>

    <div class="main-section">
       <div class="add-section">
          <form action="app/add.php" method="POST" autocomplete="off">
             <?php if(isset($_GET['mess']) && $_GET['mess'] == 'error'){ ?>
                <input type="text" 
                     name="title" 
                     style="border-color: #ff6666"
                     placeholder="This field is required" />
              <button type="submit">Add &nbsp; <span>&#43;</span></button>

             <?php }else{ ?>
              <input type="text" 
                     name="title" 
                     placeholder="What do you need to do?" />
              <button type="submit">Add &nbsp; <span>&#43;</span></button>
             <?php } ?>
          </form>
       </div>
       <?php 
          $todos = $conn->query("SELECT * FROM todos ORDER BY id DESC");
       ?>
       <div class="show-todo-section">
            <?php if($todos->rowCount() <= 0){ ?>
                <div class="todo-item">
                    <div class="empty">
                        <img src="img/f.png" width="100%" />
                        <img src="img/Ellipsis.gif" width="80px">
                    </div>
                </div>
            <?php } ?>

            <?php while($todo = $todos->fetch(PDO::FETCH_ASSOC)) { ?>
                <div class="todo-item">
                    <span id="<?php echo $todo['id']; ?>"
                          class="remove-to-do">x</span>
                    <?php if($todo['checked']){ ?> 
                        <input type="checkbox"
                               class="check-box"
                               data-todo-id ="<?php echo $todo['id']; ?>"
                               checked />
                        <h2 class="checked"><?php echo $todo['title'] ?></h2>
                    <?php }else { ?>
                        <input type="checkbox"
                               data-todo-id ="<?php echo $todo['id']; ?>"
                               class="check-box" />
                        <h2><?php echo $todo['title'] ?></h2>
                    <?php } ?>
                    <br>
                    <small>created: <?php echo $todo['date_time'] ?></small> 
                </div>
            <?php } ?>
       </div>
    </div>

    <script src="js/jquery-3.2.1.min.js"></script>

    <script>
        $(document).ready(function(){
            $('.remove-to-do').click(function(){
                const id = $(this).attr('id');
                
                $.post("app/remove.php", 
                      {
                          id: id
                      },
                      (data)  => {
                         if(data){
                             $(this).parent().hide(600);
                         }
                      }
                );
            });

            $(".check-box").click(function(e){
                const id = $(this).attr('data-todo-id');
                
                $.post('app/check.php', 
                      {
                          id: id
                      },
                      (data) => {
                          if(data != 'error'){
                              const h2 = $(this).next();
                              if(data === '1'){
                                  h2.removeClass('checked');
                              }else {
                                  h2.addClass('checked');
                              }
                          }
                      }
                );
            });
        });
    </script>
</body>
</html>