<?php
session_start();
if(!isset($_SESSION["loggedin"]))
{
  header('Location: ../../../');
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Text Edit</title>
  <link rel="stylesheet" href="../../../../style.css">
</head>
<body>
<h1>Changes Saved</h1>
<?php
  $mysqli = new mysqli("", "jhartma0", "jhartma0", "Quiz");
  if($mysqli)
  {
    $query = "update LikertAnswerSubCategory set text_area1 = '".$_POST["first"]."' where id = ".$_POST['id'].";";
    $mysqli->query($query);
    $query = "update LikertAnswerSubCategory set text_area2 = '".$_POST["second"]."' where id = ".$_POST['id'].";";
    $mysqli->query($query);
    echo $query;
    echo "<p>".$_POST["first"]."</p>";
    echo "<p>".$_POST["second"]."</p>";
    $mysqli->close();
  }
  else
  {
    exit();
  }
?>
<ol>
  <li><a href="../">Select Category</a></li>
  <li><a href="../../">Dashboard</a></li>
</ol>
</body>
</html>