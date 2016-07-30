


<?php
    include 'connect.php';
    session_start();
    $u = $_SESSION['username'];
?>
<?php  include_once 'inc/_head_editor.php'; ?>
<?php
    if ($u=='admin') {    ?>
        <!-- update page -->
        <div class="container">
            <div class="row clear-50" style="text-align: center;">
              
                
<?php
// connect to the database

// server info
$server = 'localhost';
$user = 'benholfeld';
$pass = '';
$db = 'labsportal';

// connect to the database
$mysqli = new mysqli($server, $user, $pass, $db);
// show errors (remove this line if on a live site)
mysqli_report(MYSQLI_REPORT_ERROR);
//***********************************  End ******

// creates the new/edit record form
// since this form is used multiple times in this file, I have made it a function that is easily reusable
function renderForm($name = '', $link ='', $contact ='', $description ='', $image='', $error = '', $id = '')
{ ?>



<h1><?php if ($id != '') { echo "Edit Event"; } else { echo "Add New Event"; } ?></h1>
<hr />
<?php if ($error != '') {
echo "<div style='padding:4px; border:1px solid red; color:red'>" . $error
. "</div>";
} ?>

<form action="" method="post" enctype="multipart/form-data">
<?php if ($id != '') { ?>
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<p><?php // echo $id; ?></p>
<h2 class="center"><?php echo $name; ?></h2>
<?php } ?>
<table class="form_table">
    <tr>
        <td><label>Name:<span class="required">*</span></label></td> 
        <td><div class="form-group"><input type="text" name="name" value="<?php echo $name; ?>" class="form-control" size="80" required ></div> </td> 
    </tr>
    <tr>
        <td><label for="">Link:</label></td>
        <td><div class="form-group"><input class="form-control" type="url" name="link" value="<?php echo $link; ?>"/></div></td>
    </tr>
    
    <tr>
        <td><label for="">Contact:</label></td>
        <td><div class="form-group"><input class="form-control" type="text" name="contact" value="<?php echo $contact; ?>"/></div></td>
    </tr>
    
    <tr>
        <td><label for="">Description:</label></td>
        <td><div class="form-group"> <textarea class="form-control" name="description"><?php echo $description; ?></textarea></div></td>
    </tr>
    
    <tr>
        <td><label for="">Image:</label></td>
        <td><div class="form-group"> <?php if ($id != '') { ?> <img style="margin-bottom: 10px;" src="http://labsportal-benholfeld.c9users.io/workspace/labsfiles/images/company/<?php echo $image; ?>" width="" height="300" /> <?php } ?> <br />
        <input type="file" name="file" value="<?php echo $image; ?>"/> </div></td>
    </tr>
    <tr>
        <td></td>
        <td><div class="form-group"><input class="form-control" type="submit" name="submit" value="Submit" /></div></td>
    </tr>
</table>
</form>

<?php }



/*

EDIT RECORD

*/
// if the 'id' variable is set in the URL, we know that we need to edit a record
if (isset($_GET['id']))
{
// if the form's submit button is clicked, we need to process the form
if (isset($_POST['submit']))
{
// make sure the 'id' in the URL is valid
if (is_numeric($_POST['id']))
{
// get variables from the URL/form
$id = $_POST['id'];
$name = htmlentities($_POST['name'], ENT_QUOTES);
$link = htmlentities($_POST['link'], ENT_QUOTES);
$contact = htmlentities($_POST['contact'], ENT_QUOTES);
$description = htmlentities($_POST['description'], ENT_QUOTES);

$image = $_FILES['file']['name'];
                    $tmpName = $_FILES['file']['tmp_name']; //  for image
                    $uploadDir = '../../labsfiles/images/company/';
                        function guid() {
                            if (function_exists('com_create_guid')) {
                                return com_create_guid();
                            } else {
                                mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
                                $charid = strtoupper(md5(uniqid(rand(), true)));
                                $hyphen = chr(45); // "-"
                                $uuid = chr(123)// "{"
                                        . substr($charid, 0, 8) . $hyphen
                                        . substr($charid, 8, 4) . $hyphen
                                        . substr($charid, 12, 4) . $hyphen
                                        . substr($charid, 16, 4) . $hyphen
                                        . substr($charid, 20, 12)
                                        . chr(125); // "}"
                                return $uuid;
                            }
                        }

                        // echo guid();
                        $path_parts = pathinfo($_FILES["file"]["name"]);
                        $ext = $path_parts['extension'];
                        $image = trim(guid(), '{}') . '.' . $ext;
                        $filePath = $uploadDir . $image;
                        $result = move_uploaded_file($tmpName, $filePath);

                        /* if (!$result) {
                            echo "Error uploading file";
                            exit;
                        } */
                        


// check that name and link are both not empty
if ($name == '' || $link == '')
{
// if they are empty, show an error message and display the form
$error = 'ERROR: Please fill in all required fields!';
renderForm($name, $link, $contact, $description, $image, $error, $id);
}
else
{
    
if(!empty($_FILES['file']['name'])) //new image uploaded
{
   //process your image and data
   // if everything is fine, update the record in the database
    if ($stmt = $mysqli->prepare("UPDATE companies SET name = ?, link = ?, contact = ?, description = ?, image = ? WHERE id=?"))
    {
    $stmt->bind_param("sssssi", $name, $link, $contact, $description, $image, $id);
    $stmt->execute();
    $stmt->close();
    }
    // show an error message if the query has an error
    else
    {
    echo "ERROR: could not prepare SQL statement.";
    }
}
else // no image uploaded
{
   // save data, but no change the image column in MYSQL, so it will stay the same value
      if ($stmt = $mysqli->prepare("UPDATE companies SET name = ?, link = ?, contact = ?, description = ? WHERE id=?"))
        {
        $stmt->bind_param("ssssi", $name, $link, $contact, $description, $id);
        $stmt->execute();
        $stmt->close();
        }
        // show an error message if the query has an error
        else
        {
        echo "ERROR: could not prepare SQL statement.";
        }
}

// redirect the user once the form is updated
//header("Location: view.php");
?>
<script>
    // window.location.href = "select-event.php";
</script>
<?php
}
}
// if the 'id' variable is not valid, show an error message
else
{
echo "Error!";
}
}
// if the form hasn't been submitted yet, get the info from the database and show the form
else
{
// make sure the 'id' value is valid
if (is_numeric($_GET['id']) && $_GET['id'] > 0)
{
// get 'id' from URL
$id = $_GET['id'];

// get the recod from the database
if($stmt = $mysqli->prepare("SELECT * FROM companies WHERE id=?"))
{
$stmt->bind_param("i", $id);
$stmt->execute();

$stmt->bind_result($id, $name, $link, $contact, $description, $image);
$stmt->fetch();

// show the form
renderForm($name, $link, $contact, $description, $image, NULL, $id);

$stmt->close();
}
// show an error if the query has an error
else
{
echo "Error: could not prepare SQL statement";
}
}
// if the 'id' value is not valid, redirect the user back to the view.php page
else
{
// header("Location: select-event.php");
}
}
}



/*

NEW RECORD

*/
// if the 'id' variable is not set in the URL, we must be creating a new record
else
{
// if the form's submit button is clicked, we need to process the form
if (isset($_POST['submit']))
{
// get the form data
$name = htmlentities($_POST['name'], ENT_QUOTES);
$link = htmlentities($_POST['link'], ENT_QUOTES);
$contact = htmlentities($_POST['contact'], ENT_QUOTES);
$description = htmlentities($_POST['description'], ENT_QUOTES);

$image = $_FILES['file']['name'];
                    $tmpName = $_FILES['file']['tmp_name']; //  for image
                    $uploadDir = '../../labsfiles/images/company/';
                        function guid() {
                            if (function_exists('com_create_guid')) {
                                return com_create_guid();
                            } else {
                                mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
                                $charid = strtoupper(md5(uniqid(rand(), true)));
                                $hyphen = chr(45); // "-"
                                $uuid = chr(123)// "{"
                                        . substr($charid, 0, 8) . $hyphen
                                        . substr($charid, 8, 4) . $hyphen
                                        . substr($charid, 12, 4) . $hyphen
                                        . substr($charid, 16, 4) . $hyphen
                                        . substr($charid, 20, 12)
                                        . chr(125); // "}"
                                return $uuid;
                            }
                        }

                        // echo guid();
                        $path_parts = pathinfo($_FILES["file"]["name"]);
                        $ext = $path_parts['extension'];
                        $image = trim(guid(), '{}') . '.' . $ext;
                        $filePath = $uploadDir . $image;
                        $result = move_uploaded_file($tmpName, $filePath);

                        /* if (!$result) {
                            echo "Error uploading file";
                            exit;
                        } */

// check that name and link are both not empty
if ($name == '' || $link == '')
{
// if they are empty, show an error message and display the form
$error = 'ERROR: Please fill in all required fields!';
renderForm($name, $link, $type, $email, $clientorg, $received, $error);
}
else
{
// insert the new record into the database
if ($stmt = $mysqli->prepare("INSERT companies (name, link, contact, description, image) VALUES (?, ?, ?, ?, ?)"))
{
$stmt->bind_param("sssss", $name, $link, $contact, $description, $image);
$stmt->execute();
$stmt->close();
}
// show an error if the query has an error
else
{
echo "ERROR: Could not prepare SQL statement.";
}

// redirec 
//header("Location: view.php");
?>
<script>
    // window.location.href = "select-event.php";
</script>
<?php
}

}
// if the form hasn't been submitted yet, show the form
else
{
renderForm();
}
}

// close the mysqli connection
$mysqli->close();
?>



            </div>
        </div>
<?php
    }
    else {
        include_once 'inc/_login.php';
    } ?>
<!--************** ******************* -->
<?php include_once 'inc/_footer.php'; ?>
