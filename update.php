<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$first_name = $last_name = $town_name = $gender_id = "";
$first_name_err = $last_name_err = $town_name_err = $gender_id_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
    // Validate  first name
    $input_first_name = trim($_POST["first_name"]);
    if(empty($input_first_name)){
        $first_name_err = "Please enter a first name.";
    } elseif(!filter_var($input_first_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $first_name_err = "Please enter a valid name.";
    } else{
        $first_name = $input_first_name;
    }
    
    // Validate last name
    $input_last_name = trim($_POST["last_name"]);
    if(empty($input_last_name)){
        $last_name_err = "Please enter last name.";     
    } else{
        $last_name = $input_last_name;
    }
    
    // Validate town
    $input_town_name = trim($_POST["town_name"]);
    if(empty($input_town_name)){
        $town_name_err = "Please enter the town.";     
    }else{
        $town_name = $input_town_name;
    }

    //Validate gender
    $input_gender_id = trim($_POST["gender_id"]);
    if(empty($input_gender_id)){
        $gender_id_err = "Please enter gender.";     
    }else{
        $gender_id = $input_gender_id;
    }
    // Check input errors before inserting in database
    if(empty($first_name_err) && empty($last_name_err) && empty($town_name_err) && empty($gender_id_err)){
        // Prepare an update statement
        $sql = "UPDATE customers SET first_name=?, last_name=?, town_name=?, gender_id=? WHERE id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssi", $param_first_name, $param_last_name, $param_town_name, $param_gender_id, $param_id);
            
            // Set parameters
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_town_name = $town_name;
            $param_gender_id = $gender_id;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM customers WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $first_name = $row["first_name"];
                    $last_name = $row["last_name"];
                    $town_name = $row["town_name"];
                    $gender_id = $row["gender_id"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Update Record</h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                            <label>First Name</label>
                            <input type="text" name="first_name" maxlength="25" minlength="3" class="form-control" value="<?php echo $first_name; ?>">
                            <span class="help-block"><?php echo $first_name_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($last_name_err)) ? 'has-error' : ''; ?>">
                            <label>Last Name</label>
                            <input type="text" name="last_name" maxlength="25" class="form-control" value="<?php echo $last_name; ?>">
                            <span class="help-block"><?php echo $last_name_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($town_name_err)) ? 'has-error' : ''; ?>">
                            <label>Town Name</label>
                            <input type="text" name="town_name" maxlength="25" class="form-control" value="<?php echo $town_name; ?>">
                            <span class="help-block"><?php echo $town_name_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($gender_id_err)) ? 'has-error' : ''; ?>">
                            <label for="gender">Gender</label>
                            <select name="gender_id" id="genderSelect" size="1">
                                <option value="#" disabled selected> ~~ Select Gender ~~</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <span class="help-block"><?php echo $gender_id_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>