<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <h2>Advanture start here</h2>
            <p>Create and account to Join Our Community</p>
        </div>
        <div class="right-panel">
            <div class="logo">
                <img src="logo.png" alt="Logo">
            </div>
            <h2>Sign-up</h2>
            <p>Register yourself to do something on Lidia.</p>
            <form action="signup.php" method="post">
                <div class="form-group">
                    <label for="first_name">First name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="e.g. Alex">
                </div>
                <div class="form-group">
                    <label for="last_name">Last name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="e.g. John">
                </div>
                 <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="e.g. Alex123">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="uiuxsaeed@gmail.com">
                </div>
                 <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" placeholder="e.g. +13448747684">
                </div>
                 <div class="form-group">
                    <label for="born">Born</label>
                    <input type="date" id="born" name="born" placeholder="08/16/2000">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="e.g. MH. AlexResidency, Block No.244321">
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <div class="radio-group">
                        <input type="radio" id="male" name="gender" value="male">
                        <label for="male">Male</label>
                        <input type="radio" id="female" name="gender" value="female">
                        <label for="female">Female</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="e.g. 2McDc6cdN8jk9z">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="e.g. 2McDc6cdN8jk9z">
                </div>
                <button type="submit">Sign-up</button>
            </form>
             <div class="create-account">
                    Already Have an account? <a href="login.php">Login</a>
                </div>
        </div>
    </div>
</body>
</html> 