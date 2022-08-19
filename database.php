<?php
 
    ini_set('display_errors', 1);
    ini_set('display_startup_error', 1);
    error_reporting(E_ALL);

    include_once 'config.php';

    class Database extends Config
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

            $sql = "SELECT id, mobile, name FROM seller WHERE mobile = '".$mobile."' AND password = '".$hashing."'";
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

            $sql = "SELECT id, mobile, name FROM customer WHERE mobile = '".$mobile."' AND password = '".$hashing."'";
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

     /*--------------------------------------------PRODUCTS DETAIL ONLY------------------------------------------PRODUCTS DETAIL ONLY------------------------------------------PRODUCTS DETAIL ONLY------------------------------------------PRODUCTS DETAIL ONLY------------------------------------------PRODUCTS DETAIL ONLY------------------------------------------PRODUCTS DETAIL ONLY------------------------------------------PRODUCTS DETAIL ONLY------------------------------------------PRODUCTS DETAIL ONLY------------------------------------------PRODUCTS DETAIL ONLY------------------------------------------PRODUCTS DETAIL ONLY------------------------------------------PRODUCTS DETAIL ONLY----------------*/        

        //To insert product details
        public function insertProduct($sellerId, $productName, $productQty, $productPrice, $productDescription, $productImage, $productCategory, $soldOut, $discountAvailable, $productDiscountPrice, $productDiscountNote)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $sql = "INSERT INTO products (seller_id, product_name, product_qty, product_price, product_description, product_image, product_category, soldout, discount_available, product_discount_price, product_discount_note, created_at) VALUES (:sellerId, :productName, :productQty, :productPrice, :productDescription, :productImage, :productCategory, :soldOut, :discountAvailable, :productDiscountPrice, :productDiscountNote, :createdAt)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId, 'productName' => $productName, 'productQty' => $productQty, 'productPrice' => $productPrice, 'productDescription' => $productDescription, 'productImage' => $productImage, 'productCategory' => $productCategory, 'soldOut' => $soldOut, 'discountAvailable' => $discountAvailable, 'productDiscountPrice' => $productDiscountPrice, 'productDiscountNote' => $productDiscountNote, 'createdAt' => $createdAt]);
            return true;
        }
        //To insert Product images
        public function productImages($productImage, $newfilename) 
        {
            $fileName  =  $newfilename;
            $tempPath  =  $productImage['tmp_name'];
            $fileSize  =  $productImage['size'];
            $upload_path = 'products/';
        
            $fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION)); 
       
            $valid_extensions = array('jpeg', 'jpg', 'png'); 
    
            if ($productImage["error"] > 0)
            {
                $errorMSG = json_encode(array("message" => $productImage["error"], "status" => false));   
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


        //To get all product details
        public function getAllProducts()
        {
            $sql = "SELECT id AS productId, seller_id AS sellerId, product_name AS productName, product_qty AS productQty, product_price AS productPrice, product_description AS productDescription, product_image AS productImage, product_category AS productCategory, soldout AS SoldOut, discount_available AS discountAvailable, product_discount_price AS productDiscountPrice, product_discount_note AS productDiscountNote FROM products";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }


        //To get category and discount products
        public function getDiscountCategoryProducts($productCategory, $discountAvailable)
        {
            $sql = "SELECT id AS productId, seller_id AS sellerId, product_name AS productName, product_qty AS productQty, product_price AS productPrice, product_description AS productDescription, product_image AS productImage, product_category AS productCategory, soldout AS SoldOut, discount_available AS discountAvailable, product_discount_price AS productDiscountPrice, product_discount_note AS productDiscountNote FROM products WHERE product_category = :productCategory OR discount_available = :discountAvailable";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['productCategory' => $productCategory, 'discountAvailable' => $discountAvailable]);
            return $stmt->fetchAll();
        }

        //To search products
        public function searchProducts($productCategory, $productDescription, $productName, $productDiscountNote)
        {
            $sql = "SELECT id AS productId, seller_id AS sellerId, product_name AS productName, product_qty AS productQty, product_price AS productPrice, product_description AS productDescription, product_image AS productImage, product_category AS productCategory, soldout AS SoldOut, discount_available AS discountAvailable, product_discount_price AS productDiscountPrice, product_discount_note AS productDiscountNote FROM products WHERE product_category = :productCategory AND product_description = :productDescription AND product_name = :productName AND product_discount_note = :productDiscountNote";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['productCategory' => $productCategory, 'productDescription' => $productDescription, 'productName' => $productName, 'productDiscountNote' => $productDiscountNote]);
            return $stmt->fetchAll();
        }


        //To insert placed orders delivery details
        public function orderedProductDetails($customerId, $deliveryFee, $deliveryOption, $deliveryTime, $firstPromoOffer, $orderCost, $orderStatus, $paymentOption)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $sql = "INSERT INTO orders_placed (customer_id, delivery_fee, delivery_option, delivery_time, first_promo_offer_text, order_cost, order_status, pay_option, created_at) VALUES (:customerId, :deliveryFee, :deliveryOption, :deliveryTime, :firstPromoOffer, :orderCost, :orderStatus, :paymentOption, :createdAt)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['customerId' => $customerId, 'deliveryFee' => $deliveryFee, 'deliveryOption' => $deliveryOption, 'deliveryTime' => $deliveryTime, 'firstPromoOffer' => $firstPromoOffer, 'orderCost' => $orderCost, 'orderStatus' => $orderStatus, 'paymentOption' => $paymentOption, 'createdAt' => $createdAt]);
            $id = $this->conn->lastInsertId();
            return $id;
        }
        //To insert placed orders products
        public function orderedProductLists($orderId, $productId, $productName, $productQty, $productPrice, $orderedQty)
        {
            $sql = "INSERT INTO ordered_product (order_id, product_id, product_name, product_qty, product_price, ordered_qty) VALUES(:orderId, :productId, :productName, :productQty, :productPrice, :orderedQty)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['orderId' => $orderId, 'productId' => $productId, 'productName' => $productName, 'productQty' => $productQty, 'productPrice' => $productPrice, 'orderedQty' => $orderedQty]);
            return true;
        }

        //To fetch place ordered list
        public function orderList($customerId)
        {
            $sql = "SELECT id AS orderId, order_cost AS orderCost, order_status AS orderStatus, CONCAT(date(created_at),     LOWER(DATE_FORMAT(created_at, ' %h:%i %p'))) AS orderTime, pay_option AS paymentOption FROM orders_placed WHERE customer_id = :customerId";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['customerId' => $customerId]);
            return $stmt->fetchAll();
        }


        //To fetch detailed order
        public function fetchOrderedProducts($orderId)
        {
            $sql = "SELECT product_id AS productId, product_name AS productName, product_qty AS productQty, product_price AS productPrice, ordered_qty AS orderedQty FROM ordered_product WHERE order_id = :orderId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['orderId' => $orderId]);
            return $stmt->fetchAll();
        }
        //To fetch detailed order
        public function fetchOrderDetails($orderId, $customerId)
        {
            $sql = "SELECT id AS orderId, customer_id AS customerId, delivery_fee AS deliveryFee, delivery_option AS deliveryOption, delivery_time AS deliveryTime, first_promo_offer_text AS firstPromoOffer, order_cost As orderCost, order_status AS orderStatus, pay_option AS paymentOption, created_at AS orderTime FROM orders_placed WHERE id = :orderId AND customer_id = :customerId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['orderId' => $orderId, 'customerId' => $customerId]);
            return $stmt->fetch();
        }


        function object($array) 
        {
            $object = new stdClass();

            foreach ($array as $k => $v) 
            {
                $object->$k = $v;
            }
            return $object;
        }
    }
?>

