<?php
 
    ini_set('display_errors', 1);
    ini_set('display_startup_error', 1);
    error_reporting(E_ALL);

    include_once 'config.php';

    class Db extends Config
    {
        //To check an exists seller
        public function existSeller($mobile)
        {
            $sql = "SELECT mobile FROM seller WHERE mobile = :mobile";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['mobile' => $mobile]);
            return $stmt->fetch();
        }
        //To register new seller
        public function registerSeller($shopName, $name, $mobile, $password, $shopDescription, $shopImages, $address, $city, $state, $pincode, $country)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $hashing = md5($password);

            $sql = "INSERT INTO seller (shop_name, name, mobile, password, shop_description, shop_images, address, city, state, pincode, country, created_at) VALUES (:shopName, :name, :mobile, :password, :shopDescription, :shopImages, :address, :city, :state, :pincode, :country, :createdAt)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['shopName' => $shopName, 'name' => $name, 'mobile' => $mobile, 'password' => $hashing, 'shopDescription' => $shopDescription, 'shopImages' => $shopImages, 'address' => $address, 'city' => $city, 'state' => $state, 'pincode' => $pincode, 'country' => $country, 'createdAt' => $createdAt]);
            return true;
        }
        //To check whether the push notification is enable or disable
        public function pushNotifications($sellerId, $pushNotification)
        {
            date_default_timezone_set('Asia/Kolkata');
            $updatedAt = date("Y-m-d H:i:s", time());

            $sql = "UPDATE seller SET push_notification = :pushNotification, updated_at = :updatedAt WHERE id = :sellerId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['pushNotification' => $pushNotification, 'sellerId' => $sellerId, 'updatedAt' => $updatedAt]);
            return true;
        }
        //To upload 3 shop images
        public function shopImage($shopImages, $i, $newfilename)
        {
            $fileName  =  $newfilename;
            $tempPath  =  $shopImages['tmp_name'][$i];
            $fileSize  =  $shopImages['size'][$i];
            $upload_path = 'shops/'; // set upload folder path 
            $fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION)); // get image extension
            
            // valid image extensions
            $valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); 
            // allow valid image file formats
            if ($shopImages["error"][$i] > 0)
            {
                $errorMSG = json_encode(array("message" => $shopImages["error"][$i], "error" => false));   
                echo $errorMSG;
                exit; 
            }
    
            if(in_array($fileExt, $valid_extensions))
            {
                // check file size '50MB'
                if($fileSize < 50000000)
                {
                    move_uploaded_file($tempPath, $upload_path . $fileName); // move file from system temporary path to our upload folder path 
                }
                else
                {       
                    $errorMSG = json_encode(array("message" => "Sorry, your file is too large, please upload below 50 MB size", "error" => false));   
                    echo $errorMSG;
                    exit;
                }               
            }
            else
            {       
                $errorMSG = json_encode(array("message" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed", "error" => false));   
                echo $errorMSG;
                exit;   
            }            
        }

        //To login user
        public function loginSeller($mobile, $password)
        {
            $hashing = md5($password);

            $sql = "SELECT id, mobile, name FROM seller WHERE (email = '".$mobile."' OR mobile = '".$mobile."') AND password = '".$hashing."'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetch();
        }

        //To edit Seller's profile
        public function updateSellerProfile($shopName, $name, $mobile, $password, $email, $shopDescription, $shopImages, $address, $city, $state, $pincode, $country, $delivery)
        {
            date_default_timezone_set('Asia/Kolkata');
            $updatedAt = date("Y-m-d H:i:s", time());

            $hashing = md5($password);

            $sql = "UPDATE seller SET shop_name = :shopName, name = :name, password = :password, email = :email, shop_description = :shopDescription, shop_images = :shopImages, address = :address, city = :city, state = :state, pincode = :pincode, country = :country, delivery = :delivery, updated_at = :updatedAt WHERE mobile = :mobile";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['shopName' => $shopName, 'name' => $name, 'mobile' => $mobile, 'password' => $hashing, 'email' => $email, 'shopDescription' => $shopDescription, 'shopImages' => $shopImages, 'address' => $address, 'city' => $city, 'state' => $state, 'pincode' => $pincode, 'country' => $country, 'delivery' => $delivery, 'updatedAt' => $updatedAt]);
            return true;
        } 


        //To check an exists Customer
        public function existCustomer($mobile)
        {
            $sql = "SELECT mobile FROM customer WHERE mobile = :mobile";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['mobile' => $mobile]);
            return $stmt->fetch();
        }        
        //To register new customer
        public function registerCustomer($name, $mobile, $password, $address, $city, $state, $pincode, $country)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $hashing = md5($password);

            $sql = "INSERT INTO customer (name, mobile, password, address, city, state, pincode, country, created_at) VALUES (:name, :mobile, :password, :address, :city, :state, :pincode, :country, :createdAt)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['name' => $name, 'mobile' => $mobile, 'password' => $hashing, 'address' => $address, 'city' => $city, 'state' => $state, 'pincode' => $pincode, 'country' => $country, 'createdAt' => $createdAt]);
            return true;
        }
 
        //To login customer profile
        public function loginCustomer($mobile, $password)
        {
            $hashing = md5($password);

            $sql = "SELECT id, mobile, name FROM customer WHERE (email = '".$mobile."' OR mobile = '".$mobile."') AND password = '".$hashing."'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetch();
        }

        //To edit Customer's profile
        public function updateCustomerProfile($name, $mobile, $password, $email, $address, $city, $state, $pincode, $country, $delivery)
        {
            date_default_timezone_set('Asia/Kolkata');
            $updatedAt = date("Y-m-d H:i:s", time());

            $hashing = md5($password);

            $sql = "UPDATE customer SET name = :name, password = :password, email = :email, address = :address, city = :city, state = :state, pincode = :pincode, country = :country, delivery = :delivery, updated_at = :updatedAt WHERE mobile = :mobile";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['name' => $name, 'mobile' => $mobile, 'password' => $hashing, 'email' => $email, 'address' => $address, 'city' => $city, 'state' => $state, 'pincode' => $pincode, 'country' => $country, 'delivery' => $delivery, 'updatedAt' => $updatedAt]);
            return true;
        } 
        
        //To check an exist mobile number in delivery service
        public function existCarrier($mobile)
        {
            $sql = "SELECT mobile FROM delivery_service WHERE mobile = :mobile";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['mobile' => $mobile]);
            return $stmt->fetch();
        }
        //To add delivery boy's details
        public function registerCarrier($sellerId, $firstName, $lastName, $email, $mobile, $password, $carrierImage, $address, $city, $pinCode, $state, $country)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $hashing = md5($password);

            $sql = "INSERT INTO delivery_service (seller_id, first_name, second_name, email, mobile, pass_word, profile, address, city, pincode, state, country, created_at) VALUES (:sellerId, :firstName, :lastName, :email, :mobile, :password, :carrierImage, :address, :city, :pinCode, :state, :country, :createdAt)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId, 'firstName' => $firstName, 'lastName' => $lastName, 'email' => $email, 'mobile' => $mobile, 'password' => $hashing, 'carrierImage' => $carrierImage, 'address' => $address, 'city' => $city, 'pinCode' => $pinCode, 'state' => $state, 'country' => $country, 'createdAt' => $createdAt]);
            return true;
        }
        //To login delivery boy's profile
        public function loginCarrier($mobile, $password)
        {
            $hashing = md5($password);

            $sql = "SELECT id, mobile, CONCAT(first_name,' ',second_name) AS carrierName FROM delivery_service WHERE (email = '".$mobile."' OR mobile = '".$mobile."') AND pass_word = '".$hashing."'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetch();
        }
        //To update delivery boy's details
        public function updateCarrier($firstName, $lastName, $email, $mobile, $password, $carrierImage, $address, $city, $pinCode, $state, $country)
        {
            date_default_timezone_set('Asia/Kolkata');
            $updatedAt = date("Y-m-d H:i:s", time());

            $hashing = md5($password);

            $sql = "UPDATE delivery_service SET first_name = :firstName, second_name = :lastName, email = :email, pass_word = :password, profile = :carrierImage, address = :address, city = :city, pincode = :pinCode, state = :state, country = :country, updated_at = :updatedAt WHERE mobile = :mobile";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['firstName' => $firstName, 'lastName' => $lastName, 'email' => $email, 'mobile' => $mobile, 'password' => $hashing, 'carrierImage' => $carrierImage, 'address' => $address, 'city' => $city, 'pinCode' => $pinCode, 'state' => $state, 'country' => $country, 'updatedAt' => $updatedAt]);
            return true;
        }
        //To insert delivery man's image
        public function carrierImage($carrierImage, $newfilename) 
        {
            $fileName  =  $newfilename;
            $tempPath  =  $carrierImage['tmp_name'];
            $fileSize  =  $carrierImage['size'];
            $upload_path = 'carrier/';
        
            $fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION)); 
       
            $valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); 
    
            if ($carrierImage["error"] > 0)
            {
                $errorMSG = json_encode(array("message" => $carrierImage["error"], "status" => false));   
                echo $errorMSG;
                exit; 
            }
    
            if(in_array($fileExt, $valid_extensions))
            {               

                if($fileSize < 5000000)
                {
                    move_uploaded_file($tempPath, $upload_path . $fileName); 
                }
                else
                {       
                    $errorMSG = json_encode(array("message" => "Sorry, your file is too large, please upload 5 MB size", "status" => false));   
                    echo $errorMSG;
                    exit;
                }
            }
            else
            {       
                $errorMSG = json_encode(array("message" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed", "status" => false));   
                echo $errorMSG;
                exit;   
            }
    
        }


        //To add UPI details of seller
        public function addSellerUPI($sellerId, $upiId, $upiName)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $sql = "INSERT INTO seller_upi_details (seller_id, upi_id, upi_name, created_at) VALUES (:sellerId, :upiId, :upiName, :createdAt)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId, 'upiId' => $upiId, 'upiName' => $upiName, 'createdAt' => $createdAt]);
            return true;
        }

        //To get list of bug category
        public function getBugCategory()
        {
            $sql = "SELECT id, bug_category AS bugCategory FROM bug_category";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        //To add customer query or bugs
        public function addQueries($sellerId, $customerId, $customerName, $customerMail, $customerNumber, $shopName, $bugCategory, $query)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $sql = "INSERT INTO customer_queries (seller_id, customer_id, customer_name, customer_mail, customer_number, shop_name, bug_category, query, created_at) VALUES (:sellerId, :customerId, :customerName, :customerMail, :customerNumber, :shopName, :bugCategory, :query, :createdAt)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId, 'customerId' => $customerId, 'customerName' => $customerName, 'customerMail' => $customerMail, 'customerNumber' => $customerNumber, 'shopName' => $shopName, 'bugCategory' => $bugCategory, 'query' => $query, 'createdAt' => $createdAt]);
            return true;
        }

        //To get customer query list
        public function getQueries($sellerId)
        {
            $sql = "SELECT c.name AS customerName, c.email AS customerMail, c.mobile AS customerNumber, c.customer_profile AS customerProfile, q.bug_category AS bugCategory, q.query, DATE_FORMAT(q.created_at,'%d-%m-%Y') AS bugReportedOn FROM customer_queries q INNER JOIN customer c ON q.customer_id = c.id WHERE q.seller_id = :sellerId";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId]);
            return $stmt->fetchAll();
        }

        //To select customer id from customer table
        public function getCustomerId($customerId)
        {
            $sql = "SELECT id FROM customer WHERE id = :customerId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['customerId' => $customerId]);
            return $stmt->fetch();
        }
        //To select customer id from reviews table
        public function getFeedbackCustomerId($customerId)
        {
            $sql = "SELECT customer_id FROM feedback WHERE customer_id = :customerId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['customerId' => $customerId]);
            return $stmt->fetch();
        }
        //To add reviews and ratings
        public function addFeedback($customerId, $rating, $reviews)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $sql = "INSERT INTO feedback (customer_id, rating, reviews, created_at) VALUES (:customerId, :rating, :reviews, :createdAt)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['customerId' => $customerId, 'rating' => $rating, 'reviews' => $reviews, 'createdAt' => $createdAt]);
            return true;
        }

        //To get shop reviews and ratings
        public function getFeedback()
        {
            $sql = "SELECT c.name AS customerName, c.customer_profile AS customerProfile, f.rating, f.reviews, DATE_FORMAT(f.created_at,'%d-%m-%Y') AS feedbackCreated FROM feedback f INNER JOIN customer c ON f.customer_id = c.id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

?>