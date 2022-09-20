<?php

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: X-Requested-With');
    header('Content-Type: application/json');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once 'database.php';

    $product = new Database();

    $method = $_SERVER['REQUEST_METHOD'];
    $link = basename($_SERVER['REQUEST_URI']);

    $id = intval($_GET['id'] ?? '');
    $type = ($_GET['type'] ?? '');

    if ($method == 'POST') 
    {
        $headers = array();
        foreach (getallheaders() as $name => $value) 
        {
            $headers[$name] = $value;
        }
    }
    elseif ($method == 'GET') 
    {
        $headers = array();
        foreach (getallheaders() as $name => $value) 
        {
            $headers[$name] = $value;
        }
    }


    //To insert product details
    if ($method == 'POST' && $link == "products")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $sellerId = $product->test_input($_POST['sellerId']);
        $productName = $product->test_input($_POST['productName']); 
        $productQty = $product->test_input($_POST['productQty']);
        $productPrice = $product->test_input($_POST['productPrice']);
        $productDescription = $product->test_input($_POST['productDescription']);
        $productImage = ($_FILES['productImage']['name']);
        $productCategory = $product->test_input($_POST['productCategory']);
        $soldOut = $product->test_input($_POST['soldOut']);
        $discountAvailable = $product->test_input($_POST['discountAvailable']);
        $productDiscountPrice = $product->test_input($_POST['productDiscountPrice']);
        $productDiscountNote = $product->test_input($_POST['productDiscountNote']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                $newfilename = '';
                if (!empty($productImage)) 
                {
                    $temp = explode(".", $productImage);
                    $newfilename = $temp[0]."-".round(microtime(true)) . '.' . end($temp);
                    $data = $product->productImages($_FILES['productImage'], $newfilename);
                }
        
                if ($sellerId && $productName && $productQty && $productPrice && $productDescription && $productCategory != null)
                {
                    if ($product->productCategoryId($productCategory))
                    {
                        if ($product->insertProduct($sellerId, $productName, $productQty, $productPrice, $productDescription, $newfilename, $productCategory, $soldOut, $discountAvailable, $productDiscountPrice, $productDiscountNote))
                        {
                            echo $product->message('Product added successfully', false);
                        }
                        else
                        {
                            echo $product->message('Failed to add product', true);
                        }
                    }
                    else
                    {
                        echo $product->message('Please enter valid id in product category field to add products', true);
                    }
                }
                else
                {
                    echo $product->message('Please fill required data to add product', true);
                }            
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter an valid app key', true);
        }
    }


    //To get all products list
    if ($method == 'GET' && $link == "getallproducts")
    {
        $app_key = intval($headers['app_key'] ?? '');

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($productDetails = $product->getAllProducts())
                {
                    echo $product->message('Product details found', false, $productDetails);
                }
                else
                {
                    echo json_encode(['message' => 'No product details found', 'error' => true, 'data' => []]);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To get all product details by sellerId
    if ($method == 'POST' && $link == "getallproducts")
    {
        $app_key = intval($headers['app_key'] ?? '');
        $sellerId = $product->test_input($_POST['sellerId']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId != null)
                {
                    if ($productDetails = $product->getProducts($sellerId))
                    {
                        echo $product->message('Product details found', false, $productDetails);
                    }
                    else
                    {
                        echo json_encode(['message' => 'No product details found', 'error' => true, 'data' => []]);
                    }
                }
                else
                {
                    echo $product->message('Enter a valid seller id to list products', false);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }


    //To get category and discount products
    if ($method == 'POST' && $link == "categorydiscountproducts")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $productCategory = $product->test_input($_POST['productCategory']);
        $discountAvailable = $product->test_input($_POST['discountAvailable']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($productCategory || $discountAvailable != null)
                {
                    if ($productDetails = $product->getDiscountCategoryProducts($productCategory, $discountAvailable))
                    {
                        echo $product->message('Product details found', false, $productDetails);
                    }
                    else
                    {
                        echo json_encode(['message' => 'No product details found', 'error' => true, 'data' => []]);
                    }
                }
                else
                {
                    echo $product->message('Please fill any one column', false);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    
    //To search products
    if ($method == 'POST' && $link == "searchproducts")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $productCategory = $product->test_input($_POST['productCategory']);
        $productDescription = $product->test_input($_POST['productDescription']);
        $productName = $product->test_input($_POST['productName']);
        $productDiscountNote = $product->test_input($_POST['productDiscountNote']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($productCategory && $productDescription && $productName != null)
                {
                    if ($productDetails = $product->searchProducts($productCategory, $productDescription, $productName, $productDiscountNote))
                    {
                        echo $product->message('Product details found', false, $productDetails);
                    }
                    else
                    {
                        echo json_encode(['message' => 'No product details found', 'error' => true, 'data' => []]);
                    }
                }
                else
                {
                    echo $product->message('Please fill all the fields', false);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }
    
    //To place orders
    if ($method == 'POST' && $link == "placeorders")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $items = json_decode($_POST['items']);
        $customerId = $product->test_input($_POST['customerId']);
        $deliveryFee = $product->test_input($_POST['deliveryFee']);
        $deliveryOption = $product->test_input($_POST['deliveryOption']);
        $deliveryTime = $product->test_input($_POST['deliveryTime']);
        $firstPromoOffer = $product->test_input($_POST['firstPromoOffer']);
        $orderCost = $product->test_input($_POST['orderCost']);
        $paymentOption = $product->test_input($_POST['paymentOption']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($items && $customerId && $deliveryFee && $deliveryOption && $deliveryTime && $orderCost && $paymentOption != null)
                {
                    if ($product->getCustomerId($customerId))
                    {
                        if ($id = $product->orderedProductDetails($customerId, $deliveryFee, $deliveryOption, $deliveryTime, $firstPromoOffer, $orderCost, $paymentOption))
                        {
                            foreach ($items as $key => $value)
                            {
                                $i = $key;
                                $productId = $value->productId;
                                $productName = $value->productName;
                                $productQty = $value->productQty;
                                $productPrice = $value->productPrice;
                                $orderedQty = $value->orderedQty;
                
                                $product->orderedProductLists($id, $productId, $productName, $productQty, $productPrice, $orderedQty);
                                
                            }
                            echo json_encode(['message' => 'Order placed successfully', 'error' => false, 'orderId' => $id]);
                        }
                        else
                        {
                            echo $product->message('Failed to place order', true);
                        }
                    }
                    else
                    {
                        echo $product->message('Enter valid customer id to place order', true);
                    }
                }
                else
                {
                    echo $product->message('Please fill all the details', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To fetch place ordered list
    if ($method == 'POST' && $link == "orderlist")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $customerId = $product->test_input($_POST['customerId']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($customerId != null)
                {
                    if ($orderlist = $product->orderList($customerId))
                    {
                        echo $product->message('Order list found', false, $orderlist);
                    }
                    else
                    {
                        echo json_encode(['message' => 'No order list found', 'error' => true, 'data' => []]);
                    }
                }
                else
                {
                    echo $product->message('Please enter a valid customer id', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }  
    
    //To fetch order status list
    if ($method == 'GET' && $link == "orderstatuslist")
    {
        $app_key = intval($headers['app_key'] ?? '');

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($productDetails = $product->statusList())
                {
                    echo $product->message('Order status list found', false, $productDetails);
                }
                else
                {
                    echo json_encode(['message' => 'No order status list found', 'error' => true, 'data' => []]);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }
    
    //To fetch detailed order
    if ($method == 'POST' && $link == "detailedorder")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $customerId = $product->test_input($_POST['customerId']);
        $orderId = $product->test_input($_POST['orderId']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($customerId && $orderId != null)
                {
                    $orders = $product->fetchOrderedProducts($orderId);
                    $order = $product->fetchOrderDetails($orderId, $customerId);
                    
                    if ($orders && $order)
                    {
                        echo json_encode(['message' => 'Order list found', 'error' => true, 'items' => $product->object($orders), $order]);
                    }
                    else
                    {
                        echo json_encode(['message' => 'No order list found', 'error' => true, 'items' => []]);
                    }
                }
                else
                {
                    echo $product->message('Please enter a valid customer id', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To insert which delivery man is gonna to deliver the orders
    if ($method == 'POST' && $link == "add-deliverperson")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $orderId = $product->test_input($_POST['orderId']);
        $carrierId = $product->test_input($_POST['carrierId']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($orderId && $carrierId != null)
                {
                    if ($product->ordersDeliverBy($orderId, $carrierId))
                    {
                        echo json_encode(['message' => 'Details added successfully', 'error' => false]);
                    }
                    else
                    {
                        echo json_encode(['message' => 'Failed to add details', 'error' => true]);
                    }
                }
                else
                {
                    echo $product->message('Please enter a valid customer id', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To display only delivered order details
    if ($method == 'GET' && $link == "deliveredorder")
    {
        $app_key = intval($headers['app_key'] ?? '');

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($deliveredOrder = $product->getDeliveredOrder())
                {
                    echo $product->message('Delivered order found', false, $deliveredOrder);
                }
                else
                {
                    echo json_encode(['message' => 'No delivered order found', 'error' => true, 'data' => []]);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }


    //To add data in category screen
    if ($method == 'POST' && $link == "addcategory")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $sellerId = $product->test_input($_POST['sellerId']);
        $mainCategory = $product->test_input($_POST['mainCategory']);
        $subCategory = $product->test_input($_POST['subCategory']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId && $mainCategory && $subCategory != null)
                {
                    if ($mainId = $product->addMainCategory($sellerId, $mainCategory))
                    {
                        $product->addSubCategory($sellerId, $mainId, $subCategory);
                        echo json_encode(['message' => 'Category added successfully', 'error' => false]);
                    }
                    else
                    {
                        echo json_encode(['message' => 'Failed to add category', 'error' => true]);
                    }
                }
                else
                {
                    echo $product->message('Please enter a valid customer id', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To add time in delivery time configuration screen
    if ($method == 'POST' && $link == "add-deliverytime")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $sellerId = $product->test_input($_POST['sellerId']);
        $startTime = $product->test_input($_POST['startTime']);
        $endTime = $product->test_input($_POST['endTime']);
        $thresholdTime = $product->test_input($_POST['thresholdTime']);
        $versionNumber = $product->test_input($_POST['versionNumber']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId && $startTime && $endTime && $thresholdTime != null)
                {
                    if (!$product->deliveryTimeId($sellerId))
                    {
                        $product->addDeliveryTime($sellerId, $startTime, $endTime, $thresholdTime, $versionNumber);
                        echo json_encode(['message' => 'Delivery time added successfully', 'error' => false]);
                    }
                    elseif ($product->deliveryTimeId($sellerId))
                    {
                        $product->updateDeliveryTime($sellerId, $startTime, $endTime, $thresholdTime, $versionNumber);
                        echo json_encode(['message' => 'Delivery time updated successfully', 'error' => false]);
                    }
                    else
                    {
                        echo json_encode(['message' => 'Failed to add delivery time', 'error' => true]);
                    }
                }
                else
                {
                    echo $product->message('Please enter all fields', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To display only delivered order details
    if ($method == 'POST' && $link == "get-deliverytime")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $sellerId = $product->test_input($_POST['sellerId']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId != null)
                {
                    if ($deliveryTime = $product->getDeliveryTime($sellerId))
                    {
                        echo $product->message('Delivery time found', false, $deliveryTime);
                    }
                    else
                    {
                        echo json_encode(['message' => 'No delivery time found', 'error' => true, 'data' => []]);
                    }
                }
                else
                {
                    echo $product->message('Please enter a valid seller id', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To add promotion code
    if ($method == 'POST' && $link == "add-promotioncode")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $sellerId = $product->test_input($_POST['sellerId']);
        $promoCode = $product->test_input($_POST['promoCode']);
        $promoDescription = $product->test_input($_POST['promoDescription']);
        $promoPrice = $product->test_input($_POST['promoPrice']);
        $promoMinimumPrice = $product->test_input($_POST['promoMinimumPrice']);
        $promoExpiryDate = $product->test_input($_POST['promoExpiryDate']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId && $promoCode && $promoDescription && $promoPrice && $promoMinimumPrice && $promoExpiryDate != null)
                {
                    if ($product->addPromotionCode($sellerId, $promoCode, $promoDescription, $promoPrice, $promoMinimumPrice, $promoExpiryDate))
                    {
                        echo json_encode(['message' => 'Promotion code added successfully', 'error' => false]);
                    }
                    else
                    {
                        echo json_encode(['message' => 'Failed to add promotion code', 'error' => true]);
                    }
                }
                else
                {
                    echo $product->message('Please enter all the fields to add promotion code', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To get promotion code by seller id
    if ($method == 'POST' && $link == "getall-promotioncode")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $sellerId = $product->test_input($_POST['sellerId']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId != null)
                {
                    if ($promotionCode = $product->getPromotionCodeBySellerId($sellerId))
                    {
                        echo $product->message('Promotion code found', false, $promotionCode);
                    }
                    else
                    {
                        echo json_encode(['message' => 'No promotion code found', 'error' => true, 'data' => []]);
                    }
                }
                else
                {
                    echo $product->message('Please enter a valid seller id', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To get promotion code by promo id
    if ($method == 'POST' && $link == "get-promotioncode")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $promoId = $product->test_input($_POST['promoId']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($promoId != null)
                {
                    if ($promotionCode = $product->getPromotionCodeByPromoId($promoId))
                    {
                        echo $product->message('Promotion code found', false, $promotionCode);
                    }
                    else
                    {
                        echo json_encode(['message' => 'No promotion code found', 'error' => true, 'data' => []]);
                    }
                }
                else
                {
                    echo $product->message('Please enter a valid promo id', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To delete promotion code
    if ($method == 'POST' && $link == "delete-promotioncode")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $promoId = $product->test_input($_POST['promoId']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($promoId != null)
                {
                    if ($product->deletePromotionCode($promoId))
                    {
                        echo $product->message('Promotion code is deleted', false);
                    }
                    else
                    {
                        echo json_encode(['message' => 'Failed to delete promotion code', 'error' => true, 'data' => []]);
                    }
                }
                else
                {
                    echo $product->message('Please enter a valid promo id', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To update promotion code
    if ($method == 'POST' && $link == "update-promotioncode")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $promoId = $product->test_input($_POST['promoId']);
        $promoCode = $product->test_input($_POST['promoCode']);
        $promoDescription = $product->test_input($_POST['promoDescription']);
        $promoPrice = $product->test_input($_POST['promoPrice']);
        $promoMinimumPrice = $product->test_input($_POST['promoMinimumPrice']);
        $promoExpiryDate = $product->test_input($_POST['promoExpiryDate']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($promoId != null)
                {
                    if ($product->updatePromotionCode($promoId, $promoCode, $promoDescription, $promoPrice, $promoMinimumPrice, $promoExpiryDate))
                    {
                        echo json_encode(['message' => 'Promotion code updated successfully', 'error' => false]);
                    }
                    else
                    {
                        echo json_encode(['message' => 'Failed to update promotion code', 'error' => true]);
                    }
                }
                else
                {
                    echo $product->message('Please enter valid promo id to update promotion code', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To add promotion offer
    if ($method == 'POST' && $link == "add-promotionoffer")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $sellerId = $product->test_input($_POST['sellerId']);
        $offerPrice = $product->test_input($_POST['offerPrice']);
        $offerText = $product->test_input($_POST['offerText']);
        $offerStatus = $product->test_input($_POST['offerStatus']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId && $offerPrice && $offerText && $offerStatus != null)
                {
                    if ($product->getSellerId($sellerId))
                    {
                        if (!$product->getPromotionOfferSellerId($sellerId))
                        {
                            if ($product->addPromotionOffers($sellerId, $offerPrice, $offerText, $offerStatus))
                            {
                                echo json_encode(['message' => 'Promotion offer added successfully', 'error' => false]);
                            }
                            else
                            {
                                echo json_encode(['message' => 'Failed to add promotion offer', 'error' => true]);
                            }
                        }
                        elseif ($product->getPromotionOfferSellerId($sellerId))
                        {
                            if ($product->updatePromotionOffers($sellerId, $offerPrice, $offerText, $offerStatus))
                            {
                                echo json_encode(['message' => 'Promotion offer updated successfully', 'error' => false]);
                            }
                            else
                            {
                                echo json_encode(['message' => 'Failed to update promotion offer', 'error' => true]);
                            }
                        }
                    }
                    else
                    {
                        echo json_encode(['message' => 'Invalid seller id', 'error' => true]);
                    }
                }
                else
                {
                    echo $product->message('Please enter all the fields to add promotion offer', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To get promotion offer by seller id
    if ($method == 'POST' && $link == "get-promotionoffer")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $sellerId = $product->test_input($_POST['sellerId']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId != null)
                {
                    if ($promotionOffer = $product->getPromotionOffer($sellerId))
                    {
                        echo $product->message('Promotion offers found', false, $promotionOffer);
                    }
                    else
                    {
                        echo json_encode(['message' => 'No promotion offers found', 'error' => true, 'data' => []]);
                    }
                }
                else
                {
                    echo $product->message('Please enter a valid seller id', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To add promotion offer
    if ($method == 'POST' && $link == "add-firstpromotionoffer")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $sellerId = $product->test_input($_POST['sellerId']);
        $firstOfferText = $product->test_input($_POST['firstOfferText']);
        $firstOfferStatus = $product->test_input($_POST['firstOfferStatus']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId && $firstOfferText && $firstOfferStatus != null)
                {
                    if ($product->getSellerId($sellerId))
                    {
                        if (!$product->getFirstPromotionOfferSellerId($sellerId))
                        {
                            if ($product->addFirstPromotionOffers($sellerId, $firstOfferText, $firstOfferStatus))
                            {
                                echo json_encode(['message' => 'First promotion offer added successfully', 'error' => false]);
                            }
                            else
                            {
                                echo json_encode(['message' => 'Failed to add first promotion offer', 'error' => true]);
                            }
                        }
                        elseif ($product->getFirstPromotionOfferSellerId($sellerId))
                        {
                            if ($product->updateFirstPromotionOffers($sellerId, $firstOfferText, $firstOfferStatus))
                            {
                                echo json_encode(['message' => 'First promotion offer updated successfully', 'error' => false]);
                            }
                            else
                            {
                                echo json_encode(['message' => 'Failed to update first promotion offer', 'error' => true]);
                            }
                        }
                    }
                    else
                    {
                        echo json_encode(['message' => 'Invalid seller id', 'error' => true]);
                    }
                }
                else
                {
                    echo $product->message('Please enter all the fields to add first promotion offer', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }

    //To get first time promo offer text
    if ($method == 'POST' && $link == "get-firstpromotionoffer")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $sellerId = $product->test_input($_POST['sellerId']);     

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId != null)
                {
                    if ($promoOffer = $product->getFirstPromotionOffer($sellerId))
                    {
                        echo $product->message('First time promotion offer found', false, $promoOffer);
                    }
                    else
                    {
                        echo json_encode(['message' => 'No first time promotion offer found', 'error' => true, 'data' => []]);
                    }
                }  
                else
                {
                    echo $product->message('Please enter a valid seller id', true);
                }
            }
            else
            {
                echo $product->message('Please verify your app key', true);
            }
        }
        else
        {
            echo $product->message('Enter a valid app key', true);
        }
    }
?>


