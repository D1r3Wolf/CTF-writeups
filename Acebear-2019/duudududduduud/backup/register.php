<?php
	include("header.php");
	$error = "";
	if(isset($_SESSION["is_logged"]))
	{
		header("Location: index.php");
		exit();
	}
	if ( isset($_POST["submit"]))
	{
		$username = $_POST['usn'];
		$password = $_POST['pwd'];
		if (  empty($username) || empty($password) || strlen($username) > 30 || strlen($password) > 30 || check_input($username) === 0) {
			$error = "<div class='alert alert-danger' role='alert'>
  							<strong>Input error.</strong>
							</div>";
			$conn->close();
		}
		else
		{
      $query_check = $conn->prepare("SELECT username FROM Users WHERE username=? ");
      $query_check->bind_param("s",$username);
      $query_check->execute();
      $result = $query_check->get_result();
      if ( $result->num_rows != 0)
      {
        $error = "<div class='alert alert-danger' role='alert'>
  							<strong>Username is already exist</strong>
							</div>";
        $query_check->close();
        $conn->close();
      }
      else
      {
        $folder = md5($username);
        $query = $conn->prepare("INSERT INTO Users (username, password, admin , folder) VALUES (?,?,false,?)");
        $query->bind_param("sss",$username,$password,$folder);  
        if ($query->execute() === TRUE) 
        {
          $error = "<div class='alert alert-success' role='alert'>
          <strong>Successfully registered. Please move to <a href='login.php'>login page</a> to login</strong>
           </div>";
          $conn->close();
        }
        else
        {
          $conn->close();
          $error = "<div class='alert alert-danger' role='alert'>
          <strong>I dont know what are you doing???????????????</strong>
          </div>";
        }
		  }
		}
		
	}
?>
<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center text-white mb-4">Bootstrap 4 Login Form</h2>
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <span class="anchor" id="formLogin"></span>

                    <!-- form card login -->
                    <div class="card rounded-0">
                        <div class="card-header">
                            <center><h3 class="mb-0">Register</h3></center>
                        </div>
                        <div class="card-body">
                            <form  action="register.php" method="POST" class="form" role="form" autocomplete="off" id="formLogin">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" class="form-control form-control-lg rounded-0" name="usn" placeholder="Your username ...." required>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" class="form-control form-control-lg rounded-0" name="pwd" placeholder="Your password" required>
                                </div>
                                <center><button type="submit" name="submit" class="btn btn-success">Register</button></center>
                            </form>
  							<?php echo $error; ?>
                        </div>
                        <!--/card-block-->
                    </div>
                    <!-- /form card login -->

                </div>


            </div>
            <!--/row-->

        </div>
        <!--/col-->
    </div>
    <!--/row-->
</div>
<!--/container-->