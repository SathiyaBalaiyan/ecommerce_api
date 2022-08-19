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
                    if ($product->insertProduct($sellerId, $productName, $productQty, $productPrice, $productDescription, $productImage, $productCategory, $soldOut, $discountAvailable, $productDiscountPrice, $productDiscountNote))
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
        $orderStatus = $product->test_input($_POST['orderStatus']);
        $paymentOption = $product->test_input($_POST['paymentOption']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($items && $customerId && $deliveryFee && $deliveryOption && $deliveryTime && $orderCost && $orderStatus && $paymentOption != null)
                {
                    if ($id = $product->orderedProductDetails($customerId, $deliveryFee, $deliveryOption, $deliveryTime, $firstPromoOffer, $orderCost, $orderStatus, $paymentOption))
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

?>


