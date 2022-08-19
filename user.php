<?php

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: X-Requested-With');
    header('Content-Type: application/json');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once 'database.php';

    $user = new Database();

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

        if ($name && $mobile && $password && $address && $city && $state && $pincode && $country != null)
        {
            if ($user->existCustomer($mobile))
            {
                echo $user->message('Mobile number exists already', true);
            }
            elseif ($user->registerCustomer($name, $mobile, $password, $address, $city, $state, $pincode, $country))
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

        if ($name && $mobile && $password && $address && $city && $state && $pincode && $country != null)
        {
            if ($user->updateCustomerProfile($name, $mobile, $password, $email, $address, $city, $state, $pincode, $country, $delivery))
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



?>