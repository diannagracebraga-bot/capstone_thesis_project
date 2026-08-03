<?php
include '../database/database_connection.php';

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/apply_internet.css">
	<title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>

<form action="../admin/admin_applicants.php" method= "POST" enctype="multipart/form-data">
		<div class = "container">

		<H1>INTERNET APPLICATION FORM</H1>
		 <div class="form_group ">
                <label>PERSONAL DETAILS:</label>
            </div>

			<br>

            <div class="form_group ">
                <label>First Name:</label>
                <input type="text"  id="_first_name" class = "form-control"  name="first_name" required>
            </div>

            <div class="form_group">
                <label>Middle Name:</label>
                <input type="text" id="middle_name"class = "form-control"  name="middle_name">
            </div>

			<div class="form_group">
                <label>Last Name:</label>
                <input type="text" id="last_name"class = "form-control"  name="last_name" required>
            </div>

				<div class="form_group">
                <label>Birth Date:</label>
                <input type="date" id="birth_date" class = "form-control"  name="birth_date" required>
            </div>
				<div class="form_group">
			  <p>Sex:</p>
  				<input   class="form-check-input"  type="radio" id="male" name="sex" value="male">
  				<label>Male</label><br>
 				<input   class="form-check-input" type="radio" id="female" name="sex" value="female">
				<label>Female</label><br> <br>
				
</div>
			 <div class="form_group">
                <label>CONTACT AND ADDRESS DETAILS:</label>
            </div>

			<br>

			<div class="form_group">
                <label>Contact Number:</label>
                <input type="text" id ="contact_number" class = "form-control" name="contact_number" required>
            </div>
			
            <div class="form_group">
                <label>Barangay:</label>
                <select class = "form-control" id = "barangay"  name="barangay" required>
                    <option value="">-- Select Barangay --</option>
                  <option>Bagtas</option>
                  <option>Punta I</option>
                </select>
            </div>

            <div class="form_group">
                <label>House Number:</label>
                <input type="text" id ="house_number" class = "form-control"  name="house_number" required>
            </div>

            <div class="form_group">
                <label>Street:</label>
                <input type="text" id ="street" class = "form-control"  name="street">
            </div>

            <div class="form_group">
                <label>Subdivision:</label>
                <input type="text" id ="subdivision" class = "form-control" name="subdivision">
            </div>

			 <div class="form_group">
                <label>Internet Plan:</label>
                <select  class = "form-control" id="internet_plan" name="internet_plan" required>
                    <option value="">-- Select Plan --</option>
                    <option>BRONZE 800 - 50 Mbps</option>
                    <option>SILVER 900 - 70 Mbps</option>
                    <option>GOLD 1000 - 100 Mbps</option>
                    <option>DIAMOND 1500 - 150 Mbps</option>
                    <option>PLATINUM 1800 - 200 Mbps</option>
                </select>
            </div>

			<br>

			<div class = "form_group">
			
			<p>Upload Valid ID:</p>

			<label>Select image to upload: </label> <br>
			<p>FRONT:</p>
 			 <input type="file" class = "form-control"  name="id_front" id="id_front">
			 <p>BACK:</p>
			 <input type="file" class = "form-control" name="id_back" id="id_back">	
			 <br>
			 </div>

             <button href = "../index.php"type ="submit" class = "btn btn-primary">Submit</button>
		</div>

		 
    </div>
</form>
</body>
</html>