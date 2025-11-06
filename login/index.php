<?php
session_start();

/* -----------------------------
   MySQL Database Configuration
------------------------------*/
$host = "localhost";
$user = "root";     // usually $user = "root";          // your MySQL username
$pass = "";              // your MySQL password
$dbname = "users";      // your database name

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

/* -----------------------------
   Handle logout
------------------------------*/
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login");
    exit;
}

/* -----------------------------
   Handle login form submission
------------------------------*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prepare & execute query (prevents SQL injection)
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // ✅ Compare hashed passwords (use password_hash() in DB)
        if (password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $user['username'];
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
    $stmt->close();
}

/* -----------------------------
   Show protected page if logged in
------------------------------*/
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 text-center">
        <div class="card p-5 shadow-sm">
            <h3>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h3>
            <p class="text-success mt-3">You are now logged in.</p>
            <a href="?logout" class="btn btn-danger mt-3">Logout</a>
        </div>
    </div>
</body>
</html>

<?php else: ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width:400px;">
        <div class="card p-4 shadow-sm">
            <h3 class="text-center mb-4">Login</h3>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php endif; ?>
