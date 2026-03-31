<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$conn = mysqli_connect(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    $_ENV['DB_NAME']
);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


//APPROVE TEACHER 

if(isset($_GET['approve'])){

    $teacher_id = intval($_GET['approve']);

    $query = "UPDATE teachers 
              SET status='approved' 
              WHERE id='$teacher_id'";

    mysqli_query($conn,$query);
}


//get pending teachers for approval

$query = "SELECT * FROM teachers WHERE status='pending'";
$result = mysqli_query($conn,$query);

?>

<!DOCTYPE html>
<html>
<head>
<title>Pending Teachers</title>
</head>

<body>

<h2>Pending Teacher Approvals</h2>

<table border="1" cellpadding="10">

<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Action</th>
</tr>

<?php

while($row = mysqli_fetch_assoc($result)){

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['name']; ?></td>

<td><?php echo $row['email']; ?></td>

<td>
<a href="approve_teacher.php?approve=<?php echo $row['id']; ?>">
Approve
</a>
</td>

</tr>

<?php
}
?>

</table>

</body>
</html>