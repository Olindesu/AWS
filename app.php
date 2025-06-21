<?php
// --- DB CONNECTION ---
$host = 'myproject-database.cwkqen8zmqvn.us-east-1.rds.amazonaws.com'; 
$db = 'inventorydb'; 
$user = 'admin'; 
$pass = 'adminuser123'; 

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- DEFINE EDIT ITEM DEFAULT ---
$editItem = null;

// --- HANDLE ADD ---
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $stmt = $conn->prepare("INSERT INTO items (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $desc);
    $stmt->execute();
    header("Location: app.php");
    exit;
}

// --- HANDLE DELETE ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM items WHERE id=$id");
    header("Location: app.php");
    exit;
}

// --- HANDLE UPDATE ---
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $stmt = $conn->prepare("UPDATE items SET name=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $desc, $id);
    $stmt->execute();
    header("Location: app.php");
    exit;
}

// --- HANDLE EDIT FORM DISPLAY ---
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM items WHERE id=$id");
    $editItem = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple PHP App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Item Manager</h1>

        <table>
            <tr><th>ID</th><th>Name</th><th>Description</th><th>Action</th></tr>
            <?php
            $result = $conn->query("SELECT * FROM items");
            while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td>
                        <a href="?edit=<?= $row['id'] ?>">Edit</a> |
                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this item?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h2><?= $editItem ? 'Edit Item' : 'Add New Item' ?></h2>
        <form method="post" class="form">
            <?php if ($editItem): ?>
                <input type="hidden" name="id" value="<?= $editItem['id'] ?>">
            <?php endif; ?>
            <input type="text" name="name" placeholder="Item name" required value="<?= $editItem['name'] ?? '' ?>">
            <textarea name="description" placeholder="Item description"><?= $editItem['description'] ?? '' ?></textarea>
            <button type="submit" name="<?= $editItem ? 'update' : 'add' ?>">
                <?= $editItem ? 'Update' : 'Add' ?>
            </button>
            <?php if ($editItem): ?>
                <a href="app.php" class="cancel-btn">Cancel</a>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
