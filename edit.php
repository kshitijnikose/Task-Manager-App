<?php
include 'db.php';

$id = $_GET["id"] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

$error = "";
$title = "";
$description = "";
$status = "pending";

// Fetch current task
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$row = $res->fetch_assoc();
$title = $row["title"];
$description = $row["description"];
$status = $row["status"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $status = $_POST["status"];

    if (empty($title)) {
        $error = "Title is required!";
    } else {
        $stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, status=? WHERE id=?");
        $stmt->bind_param("sssi", $title, $description, $status, $id);
        $stmt->execute();
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3>Edit Task</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="POST" action="edit.php?id=<?= $id ?>">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($description) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="pending" <?= $status == "pending" ? "selected" : "" ?>>Pending</option>
                <option value="completed" <?= $status == "completed" ? "selected" : "" ?>>Completed</option>
            </select>
        </div>
        <button class="btn btn-primary">Update Task</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
