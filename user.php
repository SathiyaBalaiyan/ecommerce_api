<?php

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: X-Requested-With');
    header('Content-Type: application/json');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once 'db.php';

    $user = new Db();

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
      

    //To register new seller
    if ($method == 'POST' && $link == "seller-signup")
    {
        $shopName = $user->test_input($_POST['shopName']);
        $name = $user->test_input($_POST['name']);
        $mobile = $user->test_input($_POST['mobile']);
        $password = $user->test_input($_POST['password']);
        $shopDescription = $user->test_input($_POST['shopDescription']);
        $shopImages = ($_FILES['shopImages']['name']);
        $address = $user->test_input($_POST['address']);
        $city = $user->test_input($_POST['city']);
        $state = $user->test_input($_POST['state']);
        $pincode = $user->test_input(($_POST['pincode']));
        $country = $user->test_input($_POST['country']);


        if ($shopName && $name && $mobile && $password && $shopDescription && $shopImages && $address && $city && $state && $pincode && $country != null)
        {
            $newfilename = '';
            $galleryNames = [];
            $newfilenames = '';
        
            if ($shopImages) 
            {
                for($i = 0; $i < count($shopImages); $i++) 
                {
                   $gallery = $shopImages[$i];
                    $temp = explode(".", $gallery);
                    $newfilename = $temp[0]."-".round(microtime(true)) . '.' . end($temp);
                    $data = $user->shopImage($_FILES['shopImages'], $i, $newfilename);
                    $galleryNames[] = $newfilename;
                }
                $newfilenames = implode(",", $galleryNames);
            }
            if ($user->existSeller($mobile))
            {
                echo $user->message('Mobile number exists already', true);
            }
            elseif ($user->registerSeller($shopName, $name, $mobile, $password, $shopDescription, $newfilenames, $address, $city, $state, $pincode, $country))
            {
                echo $user->message('Registered successfully', false);
            }
            else
            {
                echo $user->message('Failed to register', true);
            }
        }
        else
        {
            echo $user->message('Please fill all the details to register', true);
        }
    }


    //Login to seller profile
    if ($method == 'POST' && $link == "seller-signin")
    {
        $mobile = $user->test_input($_POST['mobile']);
        $password = $user->test_input($_POST['password']);

        if ($mobile && $password != null)
        {
            if ($login = $user->loginSeller($mobile, $password))
            {
                echo $user->message('Logged in successfully', false, $login);
            }
            else
            {
                echo $user->message('Incorrect mobile number or password', true);
            }
        }
        else
        {
            echo $user->message('Please enter a valid mobile number and password to log in', true);
        }
    }

    //To check whether the push notification is enable or disable
    if ($method == 'POST' && $link == "pushnotifications")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $sellerId = $user->test_input($_POST['sellerId']);
        $pushNotification = $user->test_input($_POST['pushNotification']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId && $pushNotification != null)
                {
                    if ($user->pushNotifications($sellerId, $pushNotification))
                    {
                        if ($pushNotification == '1')
                        {
                            echo $user->message('Notifications are enabled', false);
                        }
                        elseif ($pushNotification == '0')
                        {
                            echo $user->message('Notifications are disabled', false);
                        }
                    }
                    else
                    {
                        echo $user->message('Failed to disable or enable notifications', true);
                    }
                }
                else
                {
                    echo $user->message('Please enter all the fields', true);
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

    //To update seller's profile
    if ($method == 'POST' && $link == "update-sellerprofile")
    {
        $shopName = $user->test_input($_POST['shopName']);
        $name = $user->test_input($_POST['name']);
        $mobile = $user->test_input($_POST['mobile']);
        $password = $user->test_input($_POST['password']);
        $email = $user->test_input($_POST['email']);
        $shopDescription = $user->test_input($_POST['shopDescription']);
        $shopImages = ($_FILES['shopImages']['name']);
        $address = $user->test_input($_POST['address']);
        $city = $user->test_input($_POST['city']);
        $state = $user->test_input($_POST['state']);
        $pincode = $user->test_input($_POST['pincode']);
        $country = $user->test_input($_POST['country']);
        $delivery = $user->test_input($_POST['delivery']);

        if ($shopName && $name && $mobile && $password && $shopDescription && $shopImages && $address && $city && $state && $pincode && $country && $delivery != null)
        {
            $newfilename = '';
            $galleryNames = [];
            $newfilenames = '';
        
            if ($shopImages) 
            {
                for($i = 0; $i < count($shopImages); $i++) 
                {
                   $gallery = $shopImages[$i];
                    $temp = explode(".", $gallery);
                    $newfilename = $temp[0]."-".round(microtime(true)) . '.' . end($temp);
                    $data = $user->shopImage($_FILES['shopImages'], $i, $newfilename);
                    $galleryNames[] = $newfilename;
                }
                $newfilenames = implode(",", $galleryNames);
            }
            if ($user->updateSellerProfile($shopName, $name, $mobile, $password, $email, $shopDescription, $newfilenames, $address, $city, $state, $pincode, $country, $delivery))
            {
                echo $user->message('Profile updated successfully', false);
            }
            else
            {
                echo $user->message('Failed to update profile', true);
            }
        }
        else
        {
            echo $user->message('Please fill delivery column to update', true);
        }
            
    }


    //To add UPI details of seller
    if ($method == 'POST' && $link == "add-sellerupi")
    {
        $app_key = intval($headers['app_key'] ?? '');
        
        $sellerId = $user->test_input($_POST['sellerId']);
        $upiId = $user->test_input($_POST['upiId']);
        $upiName = $user->test_input($_POST['upiName']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId && $upiId && $upiName != null)
                {
                    if ($user->addSellerUPI($sellerId, $upiId, $upiName))
                    {
                        echo $user->message('UPI added successfully', false);
                    }
                    else
                    {
                        echo $user->message('Failed to add UPI details', true);
                    }
                }
                else
                {
                    echo $user->message('Fill all the details', true);
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

    //To register new seller
    if ($method == 'POST' && $link == "customer-signup")
    {
        $name = $user->test_input($_POST['name']);
        $mobile = $user->test_input($_POST['mobile']);
        $password = $user->test_input($_POST['password']);
        $address = $user->test_input($_POST['address']);
        $city = $user->test_input($_POST['city']);
        $state = $user->test_input($_POST['state']);
        $pincode = $user->test_input(($_POST['pincode']));
        $country = $user->test_input($_POST['country']);
        $customerImage = ($_FILES['customerImage']['name']);

        if ($name && $mobile && $password && $address && $city && $state && $pincode && $country != null)
        {
            $newfilename = '';
            if (!empty($customerImage)) 
            {
                $temp = explode(".", $customerImage);
                $newfilename = $temp[0]."-".round(microtime(true)) . '.' . end($temp);
                $data = $user->customerImage($_FILES['customerImage'], $newfilename);
            }
            if ($user->existCustomer($mobile))
            {
                echo $user->message('Mobile number exists already', true);
            }
            elseif ($user->registerCustomer($name, $mobile, $password, $address, $city, $state, $pincode, $country, $newfilename))
            {
                echo $user->message('Registered successfully', false);
            }
            else
            {
                echo $user->message('Failed to register', true);
            }
        }
        else
        {
            echo $user->message('Please fill all the details to register', true);
        }
    }


    //Login to customer profile
    if ($method == 'POST' && $link == "customer-signin")
    {
        $mobile = $user->test_input($_POST['mobile']);
        $password = $user->test_input($_POST['password']);

        if ($mobile && $password != null)
        {
            if ($login = $user->loginCustomer($mobile, $password))
            {
                echo $user->message('Logged in successfully', false, $login);
            }
            else
            {
                echo $user->message('Incorrect mobile number or password', true);
            }
        }
        else
        {
            echo $user->message('Please enter a valid mobile number and password to log in', true);
        }
    }


    //To update customer's profile
    if ($method == 'POST' && $link == "update-customerprofile")
    {
        $name = $user->test_input($_POST['name']);
        $mobile = $user->test_input($_POST['mobile']);
        $password = $user->test_input($_POST['password']);
        $email = $user->test_input($_POST['email']);
        $address = $user->test_input($_POST['address']);
        $city = $user->test_input($_POST['city']);
        $state = $user->test_input($_POST['state']);
        $pincode = $user->test_input($_POST['pincode']);
        $country = $user->test_input($_POST['country']);
        $delivery = $user->test_input($_POST['delivery']);
        $customerImage = ($_FILES['customerImage']['name']);

        if ($name && $mobile && $password && $address && $city && $state && $pincode && $country != null)
        {
            $newfilename = '';
            if (!empty($customerImage)) 
            {
                $temp = explode(".", $customerImage);
                $newfilename = $temp[0]."-".round(microtime(true)) . '.' . end($temp);
                $data = $user->customerImage($_FILES['customerImage'], $newfilename);
            }

            if ($user->updateCustomerProfile($name, $mobile, $password, $email, $address, $city, $state, $pincode, $country, $delivery, $newfilename))
            {
                echo $user->message('Profile updated successfully', false);
            }
            else
            {
                echo $user->message('Failed to update profile', true);
            }
        }
        else
        {
            echo $user->message('Please fill delivery column to update', true);
        }       
    }


    //To add delivery boy's details
    if ($method == 'POST' && $link == "carrier-signup")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $sellerId = $user->test_input($POST['sellerId']);
        $firstName = $user->test_input($_POST['firstName']);
        $lastName = $user->test_input($_POST['lastName']);
        $email = $user->test_input($_POST['email']);
        $mobile = $user->test_input($_POST['mobile']);
        $password = $user->test_input($_POST['password']);
        $carrierImage = ($_FILES['carrierImage']['name']);
        $address = $user->test_input($_POST['address']);
        $city = $user->test_input($_POST['city']);
        $pinCode = $user->test_input(($_POST['pinCode']));
        $state = $user->test_input($_POST['state']);
        $country = $user->test_input($_POST['country']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId && $firstName && $lastName && $mobile && $password && $address && $city && $pinCode && $state && $country != null)
                {
                    $newfilename = '';
                    if (!empty($carrierImage)) 
                    {
                        $temp = explode(".", $carrierImage);
                        $newfilename = $temp[0]."-".round(microtime(true)) . '.' . end($temp);
                        $data = $user->carrierImage($_FILES['carrierImage'], $newfilename);
                    }
                    if ($user->existCarrier($mobile))
                    {
                        echo $user->message('Mobile number exists already', true);
                    }
                    elseif ($user->registerCarrier($sellerId, $firstName, $lastName, $email, $mobile, $password, $carrierImage, $address, $city, $pinCode, $state, $country))
                    {
                        echo $user->message('Registered successfully', false);
                    }
                    else
                    {
                        echo $user->message('Failed to register', true);
                    }
                }
                else
                {
                    echo $user->message('Please fill all the details to register', true);
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

    //To login delivery boy's profile
    if ($method == 'POST' && $link == "carrier-signin")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $mobile = $user->test_input($_POST['mobile']);
        $password = $user->test_input($_POST['password']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($mobile && $password != null)
                {
                    if ($login = $user->loginCarrier($mobile, $password))
                    {
                        echo $user->message('Logged in successfully', false, $login);
                    }
                    else
                    {
                        echo $user->message('Incorrect mobile number or password', true);
                    }
                }
                else
                {
                    echo $user->message('Please enter a valid mobile number and password to log in', true);
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

    //To update delivery boy's details
    if ($method == 'POST' && $link == "update-carrier")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $firstName = $user->test_input($_POST['firstName']);
        $lastName = $user->test_input($_POST['lastName']);
        $email = $user->test_input($_POST['email']);
        $mobile = $user->test_input($_POST['mobile']);
        $password = $user->test_input($_POST['password']);
        $carrierImage = ($_FILES['carrierImage']['name']);
        $address = $user->test_input($_POST['address']);
        $city = $user->test_input($_POST['city']);
        $pinCode = $user->test_input(($_POST['pinCode']));
        $state = $user->test_input($_POST['state']);
        $country = $user->test_input($_POST['country']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($firstName && $lastName && $email && $mobile && $password && $address && $city && $pinCode && $state && $country != null)
                {
                    $newfilename = '';
                    if (!empty($carrierImage)) 
                    {
                        $temp = explode(".", $carrierImage);
                        $newfilename = $temp[0]."-".round(microtime(true)) . '.' . end($temp);
                        $data = $user->carrierImage($_FILES['carrierImage'], $newfilename);
                    }
                    
                    if ($user->updateCarrier($firstName, $lastName, $email, $mobile, $password, $carrierImage, $address, $city, $pinCode, $state, $country))
                    {
                        echo $user->message('Profile updated successfully', false);
                    }
                    else
                    {
                        echo $user->message('Failed to update profile', true);
                    }
                }
                else
                {
                    echo $user->message('Please fill all the details to register', true);
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

    //To get list of bug category
    if ($method == 'GET' && $link == "bugcategory")
    {
        $app_key = intval($headers['app_key'] ?? '');

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($bugcategory = $user->getBugCategory())
                {
                    echo $user->message('Bug category list found', false, $bugcategory);
                }
                else
                {
                    echo json_encode(['message' => 'No bug category list found', 'error' => true, 'data' => []]);
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

    //To add customer query or bugs
    if ($method == 'POST' && $link == "addqueries")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $sellerId = $user->test_input($_POST['sellerId']);
        $customerId = $user->test_input($_POST['customerId']);
        $customerName = $user->test_input($_POST['customerName']);
        $customerMail = $user->test_input($_POST['customerMail']);
        $customerNumber = $user->test_input($_POST['customerNumber']);
        $shopName = $user->test_input($_POST['shopName']);
        $bugCategory = $user->test_input($_POST['bugCategory']);
        $query = $user->test_input($_POST['query']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId && $customerId && $customerName && $customerMail && $customerNumber && $query != null)
                {
                    if ($login = $user->addQueries($sellerId, $customerId, $customerName, $customerMail, $customerNumber, $shopName, $bugCategory, $query))
                    {
                        echo $user->message('Bug reported successfully', false);
                    }
                    else
                    {
                        echo $user->message('Failed to report bug', true);
                    }
                }
                else
                {
                    echo $user->message('Fill all the details', true);
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

    //To get customer query list
    if ($method == 'POST' && $link == "getqueries")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $sellerId = $user->test_input($_POST['sellerId']);

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($sellerId != null)
                {
                    if ($queries = $user->getQueries($sellerId))
                    {
                        echo $user->message('Bug list found', false, $queries   );
                    }
                    else
                    {
                        echo json_encode(['message' => 'No bug list found', 'error' => true, 'data' => []]);
                    }
                }           
                else
                {
                    echo $user->message('Please enter valid seller id', true);
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

    //To add reviews and ratings
    if ($method == 'POST' && $link == "feedback")
    {
        $app_key = intval($headers['app_key'] ?? '');

        $customerId = $user->test_input($_POST['customerId']);
        $rating = $user->test_input($_POST['rating']);
        $reviews = $user->test_input($_POST['reviews']); 

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($customerId != null)
                {
                    if ($user->getCustomerId($customerId))
                    {
                        if (!$user->getFeedbackCustomerId($customerId))
                        {
                            if ($user->addFeedback($customerId, $rating, $reviews))
                            {
                                echo $user->message('Ratings and reviews added successfully', false);
                            }
                            else
                            {
                                echo $user->message('Failed to add ratings and reviews', true);
                            }
                        }
                        else
                        {
                            echo $user->message('Ratings have been given already', true);
                        }
                    }
                    else
                    {
                        echo $user->message('Invalid customer id', true);
                    }
                }
                else
                {
                    echo $user->message('Please enter a valid customer id', true);
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


    //To get shop reviews and ratings
    if ($method == 'GET' && $link == "getfeedback")
    {
        $app_key = intval($headers['app_key'] ?? '');

        if ($app_key != null)
        {
            if ($app_key == "655f636f6d6d657263655f6d6f62696c65")
            {
                if ($feedback = $user->getFeedback())
                {
                    echo $user->message('Ratings and reviews are found', false, $feedback);
                }
                else
                {
                    echo json_encode(['message' => 'No ratings and reviews are found', 'error' => true, 'data' => []]);
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