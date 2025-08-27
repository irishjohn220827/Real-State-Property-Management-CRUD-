<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Property Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
    <script type="text/javascript" src="js/jquery.min.js"></script>

</head>
<body>
    <div class="login-card">
        <h2>Login</h2>
        <form id="loginForm">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <div class="login-footer">
            <p>No account? <a href="register.php">Register here</a></p>
        </div>
    </div>


    <script text="text/javasctript">
        $("#loginForm").submit(function(e){
            e.preventDefault();
            $.post("request/request.php", $(this).serialize() + "&login=1", function(res){
                let data = JSON.parse(res);
                if(data.status === "success"){
                    window.location.href = "index.php";
                } else {
                    alert(data.message);
                }
            });
        });
    </script>
</body>
</html>
