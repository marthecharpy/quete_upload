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
  <link rel="stylesheet" type="text/css" href="styles.css" />
</head>

<body>
     <header class="header py-3">
          <h1 class="h1 my-0">Upload</h1>
     </header>
     <main class="container py-3 clearfix">
          <?php foreach($failed as $errors) { ?>
               <?php foreach($errors as $error) { ?>
                    <p><?= $error ?></p>
               <?php } ?>
          <?php } ?>
          <aside class="float-right">
               <form class="form-inline" action="" method="post" enctype="multipart/form-data">
                    <input class="sr-only" name="MAX_FILE_SIZE" value="<?= $maxSize ?>"/>
                    <input type="file" class="form-control mr-2" name="files[]" multiple="multiple"/>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
               </form>
          </aside>
          <h2 class="h2 mb-3">les images enregistrées</h2>
          <div class="card-columns">
               <?php foreach($images as $image) { ?>
                    <article class="card text-center mb-3">
                         <img src="<?= $image ?>" alt="<?= $image ?>" class="card-img-top rounded">
                         <div class="card-body">
                              <h5 class="card-title"><?= basename($image) ?></h5>
                         </div>
                         <form class="card-footer" method='post' action=''>
                              <input type='hidden' class="form-control" name='fileDeleteName' value='<?= $image ?>'>
                              <input type='submit' class="btn btn-danger form-control" name='deleteFile' value='supprimer'>
                         </form>
                    </article>
               <?php } ?>
          </div>
     </main>
     <!-- Latest compiled and minified Bootstrap JavaScript + JQuery -->
     <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
     <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>