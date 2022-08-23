<?php
 
    ini_set('display_errors', 1);
    ini_set('display_startup_error', 1);
    error_reporting(E_ALL);

    include_once 'config.php';

    class Database extends Config
    {      
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
            $sql = "SELECT id AS orderId, order_cost AS orderCost, order_status AS orderStatus, CONCAT(date(created_at), LOWER(DATE_FORMAT(created_at, ' %h:%i %p'))) AS orderTime, pay_option AS paymentOption FROM orders_placed WHERE customer_id = :customerId";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['customerId' => $customerId]);
            return $stmt->fetchAll();
        }

        //To fetch order status list
        public function statusList()
        {
            $sql = "SELECT id AS statusId, order_status AS orderStatus FROM status_list";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
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
        public function object($array) 
        {
            $object = new stdClass();
            foreach ($array as $k => $v) 
            {
                $object->$k = $v;
            }
            return $object;
        }

        //To insert which delivery man is gonna to deliver the orders
        public function ordersDeliverBy($orderId, $carrierId)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $sql = "INSERT INTO order_deliver_by (order_id, carrier_id, created_at) VALUES (:orderId, :carrierId, :createdAt)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['orderId' => $orderId, 'carrierId' => $carrierId, 'createdAt' => $createdAt]);
            return true;
        }

        //To display only delivered order details
        public function getDeliveredOrder()
        {
            $sql = "SELECT op.id AS orderId, CONCAT (ds.first_name,' ',ds.second_name) AS deliveredBy, op.order_cost AS orderCost FROM order_deliver_by odb 
            INNER JOIN orders_placed op ON odb.order_id = op.id
            INNER JOIN delivery_service ds ON odb.carrier_id = ds.id WHERE op.order_status = '7'";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        //To add data in main category table
        public function addMainCategory($sellerId, $mainCategory)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $sql = "INSERT INTO main_category (seller_id, main_category, created_at) VALUES (:sellerId, :mainCategory, :createdAt)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId, 'mainCategory' => $mainCategory, 'createdAt' => $createdAt]);
            $id = $this->conn->lastInsertId();
            return $id;
        }
        //To add data in sub category table
        public function addSubCategory($sellerId, $mainId, $subCategory)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $sql = "INSERT INTO sub_category (seller_id, main_id, sub_category, created_at) VALUES (:sellerId, :mainId, :subCategory, :createdAt)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId, 'mainId' => $mainId, 'subCategory' => $subCategory, 'createdAt' => $createdAt]);
            return true;
        }

        //To get delivery time
        public function deliveryTimeId($sellerId)
        {
            $sql = "SELECT seller_id FROM delivery_time WHERE seller_id = :sellerId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId]);
            return $stmt->fetch();
        }
        //To add delivery time
        public function addDeliveryTime($sellerId, $startTime, $endTime, $thresholdTime, $versionNumber)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $sql = "INSERT INTO delivery_time (seller_id, start_time, end_time, threshold_time, version_number, created_at) VALUES (:sellerId, :startTime, :endTime, :thresholdTime, :versionNumber, :createdAt)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId, 'startTime' => $startTime, 'endTime' => $endTime, 'thresholdTime' => $thresholdTime, 'versionNumber' => $versionNumber, 'createdAt' => $createdAt]);
            return true;
        }
        //To update delivery time
        public function updateDeliveryTime($sellerId, $startTime, $endTime, $thresholdTime, $versionNumber)
        {
            date_default_timezone_set('Asia/Kolkata');
            $updatedAt = date("Y-m-d H:i:s", time());

            $sql = "UPDATE delivery_time SET start_time = :startTime, end_time = :endTime, threshold_time = :thresholdTime, version_number = :versionNumber, updated_at = :updatedAt WHERE seller_id = :sellerId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId, 'startTime' => $startTime, 'endTime' => $endTime, 'thresholdTime' => $thresholdTime, 'versionNumber' => $versionNumber, 'updatedAt' => $updatedAt]);
            return true;
        }

        //To get delivery time
        public function getDeliveryTime($sellerId)
        {
            $sql = "SELECT seller_id AS sellerId, start_time AS startTime, end_time AS endTime, threshold_time AS thresholdTime, version_number AS versionNumber FROM delivery_time WHERE seller_id = :sellerId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId]);
            return $stmt->fetch();
        }

        //To add promotion code
        public function addPromotionCode($sellerId, $promoCode, $promoDescription, $promoPrice, $promoMinimumPrice, $promoExpiryDate)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $sql = "INSERT INTO promotion_codes (seller_id, promo_code, promo_description, promo_price, promo_minimum_price, promo_expiry_date, created_at) VALUES (:sellerId, :promoCode, :promoDescription, :promoPrice, :promoMinimumPrice, :promoExpiryDate, :createdAt)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId, 'promoCode' => $promoCode, 'promoDescription' => $promoDescription, 'promoPrice' => $promoPrice, 'promoMinimumPrice' => $promoMinimumPrice, 'promoExpiryDate' => $promoExpiryDate, 'createdAt' => $createdAt]);
            return true;
        }

        //To get promotion code by seller id 
        public function getPromotionCodeBySellerId($sellerId)
        {
            $sql = "SELECT id AS promoId, promo_code AS promoCode, promo_description AS promoDescription, promo_price AS promoPrice, promo_minimum_price  AS promoMinimumPrice, promo_expiry_date AS promoExpiryDate FROM promotion_codes WHERE seller_id = :sellerId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId]);
            return $stmt->fetchAll();
        }

        //To get promotion code by promo id
        public function getPromotionCodeByPromoId($promoId)
        {
            $sql = "SELECT id AS promoId, promo_code AS promoCode, promo_description AS promoDescription, promo_price AS promoPrice, promo_minimum_price  AS promoMinimumPrice, promo_expiry_date AS promoExpiryDate FROM promotion_codes WHERE id = :promoId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['promoId' => $promoId]);
            return $stmt->fetchAll();
        }

        //To update promotion code by promo id
        public function updatePromotionCode($promoId, $promoCode, $promoDescription, $promoPrice, $promoMinimumPrice, $promoExpiryDate)
        {
            date_default_timezone_set('Asia/Kolkata');
            $updatedAt = date("Y-m-d H:i:s", time());
            
            $sql = "UPDATE promotion_codes SET promo_code = :promoCode, promo_description = :promoDescription, promo_price = :promoPrice, promo_minimum_price  = :promoMinimumPrice, promo_expiry_date = :promoExpiryDate, updated_at = :updatedAt WHERE id = :promoId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['promoId' => $promoId, 'promoCode' => $promoCode, 'promoDescription' => $promoDescription, 'promoPrice' => $promoPrice, 'promoMinimumPrice' => $promoMinimumPrice, 'promoExpiryDate' => $promoExpiryDate, 'updatedAt' => $updatedAt]);
            return true;
        }

        //To delete promotion code
        public function deletePromotionCode($promoId)
        {
            $sql = "DELETE FROM promotion_codes WHERE id = :promoId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['promoId' => $promoId]);
            return true;
        }

        //To select seller id from promotion offers table
        public function getSellerId($sellerId)
        {
            $sql = "SELECT id FROM seller WHERE id = :sellerId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId]);
            return $stmt->fetch();
        }
        //To select seller id from promotion offers table
        public function getPromotionOfferSellerId($sellerId)
        {
            $sql = "SELECT seller_id FROM promotion_offers WHERE seller_id = :sellerId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId]);
            return $stmt->fetch();
        }
        //To add promotion offers
        public function addPromotionOffers($sellerId, $offerPrice, $offerText, $offerStatus)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $sql = "INSERT INTO promotion_offers (seller_id, offer_price, offer_text, offer_status, created_at) VALUES (:sellerId, :offerPrice, :offerText, :offerStatus, :createdAt)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId, 'offerPrice' => $offerPrice, 'offerText' => $offerText, 'offerStatus' => $offerStatus, 'createdAt' => $createdAt]);
            return true;
        }
        //To update promotion offers
        public function updatePromotionOffers($sellerId, $offerPrice, $offerText, $offerStatus)
        {
            date_default_timezone_set('Asia/Kolkata');
            $updatedAt = date("Y-m-d H:i:s", time());

            $sql = "UPDATE promotion_offers SET offer_price = :offerPrice, offer_text = :offerText, offer_status = :offerStatus, updated_at = :updatedAt WHERE seller_id = :sellerId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId, 'offerPrice' => $offerPrice, 'offerText' => $offerText, 'offerStatus' => $offerStatus, 'updatedAt' => $updatedAt]);
            return true;
        }
        //To get promotion offers 
        public function getPromotionOffer($sellerId)
        {
            $sql = "SELECT seller_id AS sellerId, offer_price AS offerPrice, offer_text AS offerText, offer_status AS offerStatus FROM promotion_offers WHERE seller_id = :sellerId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId]);
            return $stmt->fetch();
        }


        //To select seller id from promotion offers table
        public function getFirstPromotionOfferSellerId($sellerId)
        {
            $sql = "SELECT seller_id FROM first_promo_offer WHERE seller_id = :sellerId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId]);
            return $stmt->fetch();
        }
        //To add promotion offers
        public function addFirstPromotionOffers($sellerId, $firstOfferText, $firstOfferStatus)
        {
            date_default_timezone_set('Asia/Kolkata');
            $createdAt = date("Y-m-d H:i:s", time());

            $sql = "INSERT INTO first_promo_offer (seller_id, offer_text, offer_status, created_at) VALUES (:sellerId, :firstOfferText, :firstOfferStatus, :createdAt)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId, 'firstOfferText' => $firstOfferText, 'firstOfferStatus' => $firstOfferStatus, 'createdAt' => $createdAt]);
            return true;
        }
        //To update promotion offers
        public function updateFirstPromotionOffers($sellerId, $firstOfferText, $firstOfferStatus)
        {
            date_default_timezone_set('Asia/Kolkata');
            $updatedAt = date("Y-m-d H:i:s", time());

            $sql = "UPDATE first_promo_offer SET offer_text = :firstOfferText, offer_status = :firstOfferStatus, updated_at = :updatedAt WHERE seller_id = :sellerId";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId, 'firstOfferText' => $firstOfferText, 'firstOfferStatus' => $firstOfferStatus, 'updatedAt' => $updatedAt]);
            return true;
        }
        //To get first time promo offer text
        public function getFirstPromotionOffer($sellerId)
        {
            $sql = "SELECT seller_id AS sellerId, offer_text AS firstOfferText, offer_status AS firstOfferStatus FROM first_promo_offer";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['sellerId' => $sellerId]);
            return $stmt->fetch();
        }

    }
?>

