<?php
$files = $_FILES['files'];
$uploaded = array();
$failed = array();
$extensionsAllowed = array('png', 'gif', 'jpg');
$imageFolder = 'img/upload/';
$maxSize = 1000000;

//Loop through each file
foreach($files['name'] as $key => $fileName) {
     $fileTemp = $files['tmp_name'][$key];
     $fileSize = $files['size'][$key];
     $fileError = $files['error'][$key];
     $fileExtension = pathinfo($files['name'][$key], PATHINFO_EXTENSION);

     //Début des vérifications de sécurité...
     if(!in_array($fileExtension, $extensionsAllowed)) {
          $failed[$key][] = "file extension of $fileName is not in [ " .
               implode(', ', $extensionsAllowed) .
               " ]";
     }
     if($fileError !== 0) {
          // TODO : expliciter le message d'erreur
          // voir http://php.net/manual/fr/features.file-upload.errors.php
          $failed[$key][] = "file error $fileError";
     }
     if($fileSize >= $maxSize) {
          $failed[$key][] = "$fileName is too big";
     }

     if(!isset($failed[$key])) {
          $uploadFile = 'image'.uniqid().'.'.$fileExtension;
          $uploadDir = $imageFolder . $uploadFile;

          if(move_uploaded_file($fileTemp, $uploadDir)) {
               $uploaded[$key] = $uploadDir;
          }
          else {
               $failed[$key] = "$fileName failed to uploaded";
          }
     }
}
//Suppression image
if(isset($_POST['deleteFile'])) {
     $imageToDelete = $_POST['fileDeleteName'];
     if (file_exists($imageToDelete)) {
          unlink($imageToDelete); 
     }
}

$imageExt = '{*.jpg,*.png,*.gif}';
$images = glob($imageFolder . $imageExt, GLOB_BRACE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Upload</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
     <div class="container">
          <div class="row">
               <h1>Upload</h1>
               <?php foreach($failed as $errors) { ?>
               <?php foreach($errors as $error) { ?>
                    <p><?= $error ?></p>
               <?php } ?>
               <?php } ?>
               <form class="col-12" action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="MAX_FILE_SIZE" value="<?= $maxSize ?>"/>
                    <input type="file" name="files[]" multiple="multiple"/>
                    <input type="submit" value="Send" />
               </form>

               <?php foreach($images as $image) { ?>
                    <div class="col-3" width="50%">
                         <img src="<?= $image ?>" width="100" height="100" alt="<?= $image ?>" class="img-thumbnail">
                         <p><?= $image ?></p>
                         <form method='post' action=''>
                              <input type='hidden' name='fileDeleteName' value='<?= $image ?>'>
                              <input type='submit' name='deleteFile' value='supprimer'>
                         </form>
                    </div>
               <?php } ?>
          </div>
     </div>
     <!-- Latest compiled and minified Bootstrap JavaScript + JQuery -->
     <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
     <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>