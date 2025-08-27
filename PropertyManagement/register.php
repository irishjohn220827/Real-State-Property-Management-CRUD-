<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Property Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">
    <script type="text/javascript" src="js/jquery.min.js"></script>


</head>
<body>
    <div class="register-card">
        <h2>Register</h2>
        <form id="registerForm">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-success">Register</button>
        </form>
        <div class="register-footer">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>


    <script text="text/javasctript">
        $("#registerForm").submit(function(e){
            e.preventDefault();
            $.post("request/request.php", $(this).serialize() + "&register=1", function(res){
                let data = JSON.parse(res);
                if(data.status === "success"){
                    alert(data.message);
                    window.location.href = "login.php";
                } else {
                    alert(data.message);
                }
            });
        });
    </script>
</body>
</html>
