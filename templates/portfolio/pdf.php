<?php
require_once 'vendor/autoload.php'; 
//require_once 'includes/connect.php'; 

use Dompdf\Dompdf; 

$conn = new PDO('mysql:host=localhost;dbname=tefree1', 'root', ''); 

//$db = new clsDBconnection; 


//$mysqli = new mysqli("localhost", "root", "", "tefree1");
//$result=$mysqli->query("SELECT * FROM portfolio");

 $sql ='SELECT * FROM `portfolio`'; 
// $sql = "SELECT * FROM `portfolio` WHERE id = 190";


//$query = $db->query($sql); 
// $users = $result->fetchAll();

// $users = $query->fetchAll();

 //var_dump($users); die; 
//*********************** */
 $stmt = $conn->prepare($sql); 
 $stmt->execute();
 $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); 

 print_r($rows); 

?>



<!DOCTYPE html>
<html>
<head>
<title>PDF</title>

<style>
    h2{
        font-family: Verdana, sans-serif; 
        text-align: center; 
    }
    table{
        font-family: Arial,  sans-serif; 
        border-collapse: collapse;
        width: 100%; 
    }
    td,th{
        border: 1px solid #444; 
        padding: 8px; 
        text-align: left;
    }


</style>




</head>
<body>

<h1>This is a Heading</h1>
<p>This is a paragraph.</p>


    <h2>PDF Hello</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Intro</th>
                <th>About</th>

            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Zi intro</td>
                <td>Zi about of ziz portafilio me happy</td>

            </tr>
        </tbody>
    </table>



</body>
</html>