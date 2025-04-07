<?php

session_start();

if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

include_once 'db.php';
include_once 'header.php';



if (isset($_POST['email'])) {
    $conn = db_connect();

    $email = $_POST['email'];
    
    // check if the email is valid
    if (!preg_match('/^[a-zA-Z0-9_\.\@]+$/', $email)) {
        echo "<div class='alert alert-danger'>Invalid email</div>";
    } else {
       
        // check if the user exists
        $emailsql = "SELECT * FROM users WHERE email = '$email'";
        $emailresult = $conn->query($emailsql);
        if (!isset($_POST['username'])) {
            if ($emailresult->num_rows > 0) {
                // check password
                $row = $emailresult->fetch_assoc();
                if ($row['password'] === $_POST['password']) {
                    $_SESSION['user'] = $row['id'];
                    echo "<meta http-equiv='refresh' content='0;url=/'>";
                } else {
                    echo "<div class='alert alert-danger'>Invalid password</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>User not found, please register</div>";
            }
        } else {
                // create a new user
                $id = bin2hex(random_bytes(16));

                //check whether the random id has been rolled before, reroll if so.
                $sql = "SELECT * FROM users WHERE id = '$id'";
                $result = $conn->query($sql);
                while ($result->num_rows > 0) {
                    $id = bin2hex(random_bytes(16));
                    $sql = "SELECT * FROM users WHERE id = '$id'";
                    $result = $conn->query($sql);
                }
                $password = bin2hex(random_bytes(8));
                
                $sql = "INSERT INTO users (id, email, username, password) VALUES ('$id', '$email', ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ss', $_POST['username'] , $password);
                
                
                if ($emailresult->num_rows == 0) {
                    try {
                        if (!$stmt->execute()) {
                            echo "Error creating user";
                            $stmt->close();
                            $conn->close();
                            exit();
                        } else {
                            //user created, create row in points table
                            $sql = "INSERT INTO points (id) VALUES ('$email')";
                            $stmt = $conn->prepare($sql);
                            if (!$stmt->execute()) {
                                echo "Error adding points to user at creation";
                                $stmt->close();
                                $conn->close();
                                exit();
                            }
                        }
                        echo "<div class='alert alert-success'>User created! Your password is \"$password\"</div>";
                    } catch (Exception $e) {
                        echo "<div class='alert alert-danger'>Email already registered</div>";
                    }
                    $stmt->close();
                }
                $sql = "UPDATE points set points=points+300 where id = '$email'";
                $conn->query($sql);

        } 
        $conn->close();
    }
}

?>

<div class=container>
    <div class="row align-items-start">
        <div class="col-6">
            <h3>Register</h3>
            <form method="post" class="mt-4 mx-4">
                <input type="text" class="form-control my-2" name="username" placeholder="username" required>
                <input type="text" class="form-control my-2" name="email" placeholder="email" minlength="5" required>
                <input type="submit" class="btn btn-primary my-2 w-100" value="Register">
            </form>
        </div>
        <div class="col-6">
            <h3>Login</h3>
            <form method="post" class="mt-4">
                <input type="text" class="form-control my-2" name="email" placeholder="email" required>
                <input type="password" class="form-control my-2" name="password" placeholder="password" required>
                <input type="submit" class="btn btn-primary my-2 w-100" value="Login">
            </form>
        </div>
    </div>
</div>
