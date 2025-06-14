<?php 

if(!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

echo $_SESSION;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>