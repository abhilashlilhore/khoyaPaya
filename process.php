<?php
class KhoyaPaya
{

    private $conn = '';
    private $image_dir = '';
    function __construct($con)
    {
        $this->conn = $con;
        $this->image_dir = './image/';
    }

    function add_image($filename, $dir)
    {

        function is_image($filename)
        {
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
            $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            return in_array($file_extension, $allowed_extensions);
        }

        $target_dir = $this->image_dir . '/' . $dir;
        $uploadOk = 1;

        // Check if the images directory exists, if not, create it
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file = $filename;
        $file_name = basename($file["name"]);
        $target_file = $target_dir . '/' . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is an actual image
        $check = getimagesize($file["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {

            $responce['status'] = 0;
            $responce['message'] = "File is not an image.";
            return $responce;
        }

        // Check file type
        if (!is_image($file_name)) {

            $responce['status'] = 0;
            $responce['message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            return $responce;
        }


        // Generate a unique file name
        $unique_file_name = uniqid() . '.' . $imageFileType;
        $unique_target_file = $target_dir . $unique_file_name;

        // Attempt to move the uploaded file to the target directory
        if (move_uploaded_file($file["tmp_name"], $unique_target_file)) {


            $responce['status'] = 1;
            $responce['message'] = "The file " . htmlspecialchars($unique_file_name) . " has been uploaded.";
            $responce['data'] = $unique_file_name;
            return $responce;
        } else {


            $responce['status'] = 0;
            $responce['message'] = "Sorry, there was an error uploading your file.";
            return $responce;
        }
    }



    function signup($data)
    {

        extract($_REQUEST);
        if (!isset($data['first_name']) || !isset($data['mobile'])) {

            $responce['status'] = 0;
            $responce['message'] = "First name and mobile number required fields";
            return json_encode($responce);
        }

        ///// mobiel check 
        $sql = "SELECT * From users where mobile ='" . $data['mobile'] . "'";
        $data = mysqli_num_rows(mysqli_query($this->conn, $sql));

        if ($data > 0) {
            $responce['status'] = 0;
            $responce['message'] = "This mobile number allready registerd";

            return json_encode($responce);
        }


        if ($_FILES['image']) {

            $image = $this->add_image($_FILES['image'], 'user');
            if ($image['status'] == 1) {
                $image = $image['data'];
            } else {

                $responce['status'] = 0;
                $responce['message'] = $image['message'];
                return json_encode($responce);
            }
        } else {
            $image = '';
        }


        $first_name = $first_name;
        $last_name = $last_name;
        $dob = date("Y-m-d h:i:s", strtotime($dob));
        $email = $email;
        $phone = $mobile;
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $image = $image;
        $aadhar_id = $aadhar_id;
        $reg_id = $reg_id;




        $sql = "INSERT INTO users (first_name, last_name, dob, email, phone, password, image, aadhar_id,reg_id) VALUES ('$first_name', '$last_name', '$dob', '$email', '$phone', '$password', '$image', '$aadhar_id', '$reg_id')";



        if ($this->conn->query($sql) !== TRUE) {
            $responce['status'] = 0;
            $responce['message'] = "Error inserting found item: " . $this->conn->error;
            return json_encode($responce);
        } else {
            $insert_id = mysqli_insert_id($this->conn);
            $sql = "SELECT * From users where id ='" . $insert_id . "'";
            $data = mysqli_fetch_assoc(mysqli_query($this->conn, $sql));

            $responce['status'] = 1;
            $responce['message'] = "User registered successfully";
            $responce['data'] = $data;
            return json_encode($responce);
        }
    }

    function get_cat()
    {
        $sql = "SELECT * FROM categories";
        $result = $this->conn->query($sql);


        if ($result->num_rows > 0) {
            $categories = array();
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }


            $responce['status'] = 1;
            $responce['message'] = "Data fetched successfully";
            $responce['data'] = $categories;
            return json_encode($responce);
        } else {

            $responce['status'] = 0;
            $responce['message'] = "Category not found";
            return json_encode($responce);
        }
    }

    function add_found_item($data)
    {

        if (!isset($data['user_id']) || !isset($data['found_date']) || !isset($data['type'])) {
           
            $responce['status'] = 0;
            $responce['message'] = "Found date, user ID and type is required";
           
            return json_encode($responce);
        }

        $user_id = $data['user_id'];
        $category_id = $data['category_id'];
        $description = $data['description'];
        $found_date = $data['found_date'];
        $location = $data['location'];
        $image = $data['image'];
        $type = $data['type'];

        // Insert found item into the found_items table
        $sql = "INSERT INTO found_items (user_id, category_id, description, found_date, location, image,type) VALUES ('$user_id', '$category_id', '$description', '$found_date', '$location', '$image','$type')";
        if ($this->conn->query($sql) !== TRUE) {
            
            $responce['status'] = 1;
            $responce['message'] = "Error inserting found item: " . $this->conn->error;
           
            return json_encode($responce);
        } else {
             $insert_id = mysqli_insert_id($this->conn);
            $sql = "SELECT * From found_items where id ='" . $insert_id . "'";
            $data = mysqli_fetch_assoc(mysqli_query($this->conn, $sql));

            $responce['status'] = 1;
            $responce['message'] = "Data inserted successfully";
            $responce['data'] = $data;
            return json_encode($responce);
        }
    }


    function get_item($data)
    {

        $filter = '';
        if ($_POST['user_id']) {
            $filter = ' where user_id=' . $_POST['user_id'];
        }
        $sql = "SELECT * FROM found_items $filter ";
        $result = $this->conn->query($sql);


        if ($result->num_rows > 0) {
            $categories = array();
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }

            echo json_encode($categories);
        } else {

            echo json_encode(array());
        }
    }
}
