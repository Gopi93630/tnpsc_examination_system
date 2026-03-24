<?php
include("../backend/db.php");

if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Users</title>
</head>
<body>

<h2>Users List</h2>

<table border="1">
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Action</th>
</tr>

<?php
$res = $conn->query("SELECT * FROM users");
while($row = $res->fetch_assoc()){
?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['email']; ?></td>
<td>
<a href="?delete=<?php echo $row['id']; ?>" onclick="return confirmDelete()">Delete</a>
</td>
</tr>
<?php } ?>

</table>

<script src="../assets/script.js"></script>
</body>
</html>