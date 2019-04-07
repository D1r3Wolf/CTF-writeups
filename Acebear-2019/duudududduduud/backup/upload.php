<?php 
    include("header.php");
    if ( isset($_SESSION["is_logged"]))
    {
        if (!$_SESSION["admin"])
        {   
            header("Location: index.php");
            exit();
        }
    }
    else
    {
        header("Location: login.php");
        exit();
    }
?>

<?php 
    $message = "";
    if (isset($_POST["submit"]))
    {
        $error = $_FILES["zip_file"]["error"];
        $file = $_FILES["zip_file"]["tmp_name"];
        if ($error == 1 || $error == 2) 
        {
            $message = "The uploaded file is too large. File must not larger than 10mb :)";
        }
        elseif ($error == 3 || !$file)
        {
            $message = "File upload failed.";
        } 

        include("lib/pclzip.php");
        $zip = new PclZip($file);
        if (!is_dir($_SESSION["folder"]))
        {
            mkdir($_SESSION["folder"], 0777);
        }
        $files = $zip->extract(PCLZIP_OPT_PATH,$_SESSION["folder"]);
        if (!$files)
        {
            exec(sprintf("rm -rf %s", escapeshellarg($_SESSION["folder"])));
            $message = "Cannot extract your file.";
        }
        else
        {
            $json = json_decode(file_get_contents($_SESSION["folder"]."/manifest.json"),true);
            if ($json["type"] !== "h4x0r" || !isset($json["name"]))
            {
                exec(sprintf("rm -rf %s", escapeshellarg($_SESSION["folder"])));
                $message = "Your file is invalid.";
            }
            else
            {
                $message = "Your file is successfully unzip-ed. Access your file at ".$_SESSION['folder']."/[your_file_name]";
            }
        }
    }
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Unzip file to server</title>
</head>

<body>
<center>
<form enctype="multipart/form-data" method="post" action="">
<label>Choose a zip file to upload: <input type="file" name="zip_file" /></label>
<br />
<input type="submit" name="submit" value="Upload" />
</form>
<?php 
    if ($message != "")
    {
        echo "<div class='alert alert-danger' role='alert'>
        <strong>$message</strong>
        </div>";
    }
?>
</center>
</body>
</html>