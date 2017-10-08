<?php

class PrintAura
{
    private $url;
    private $seller;
    private $postfields = array();

    public function __construct($key,$hash)
    {
        $this->url = 'https://api.printaura.com/api.php';
        $this->seller['key'] = $key;
        $this->seller['hash'] = $hash;
    }

    private function call_api( $method )
    {
        $parameters = array_merge( $this->seller, $this->postfields );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function printaura( $method )
    {
        $notfound = false;
        switch( $method )
        {
            case 'downloadimage':
                //{url: STR}
            case 'getpricing':
                //{price: DEC}
            case 'getprintingprice':
                //{printingprice: DEC}
            case 'getallpricing':
                //[{shipping_id: INT, base_price: DEC, printing_price: INT, tagremoval: INT, tagapplication: INT,
                //additionalmaterial: INT}]
            case 'addorder':
            case 'addorder1': //best for order of predefined products
                //[{order_id: INT, total_price: DEC, items: {item_id: INT}}]
            case 'updateorder': //only use when order status is New
                //{order_id: INT}
            case 'cancelorder': //only use when order status is New
                //{order_id: INT}
            case 'additem': //only use when order status is New
                //{item_id: INT}
            case 'removeitem': //only use when order status is New
                //{item_id: INT}
            case 'removeitem': //only use when order status is New
                //{item_id: INT}
            case 'viewproducts':
                //{product_id: INT}
            case 'addproduct':
                //{product_id: INT}
            case 'editproduct':
                //{product_id: INT}
            case 'deleteproduct':
                //{product_id: INT}
            case 'uninstallapp':
                //{app_id: INT}
            case 'editmybrand':
                if( $_POST['parameters'] ) $this->postfields = json_decode( $_POST['parameters'], true );
                break;
            case 'uploadimage':
                $ext = strtolower( pathinfo( $_FILES['uploadimage']['name'], PATHINFO_EXTENSION ) );
                if( !$this->valid_ext($ext) ) return json_encode( array( 'result' => 0, 'message' => 'ERROR: File type must be .ai, .eps, .jpg, .png, or .psd.' ) );
                $this->postfields['file'] = $_FILES['uploadimage'];
                break;
            case 'uploadimagefromurl':
                $ext = strtolower( substr(strrchr( $_POST['url'], "." ), 1 ) );
                if( !$this->valid_ext($ext) ) return json_encode( array( 'result' => 0, 'message' => 'ERROR: File type must be .ai, .eps, .jpg, .png, or .psd.' ) );
                $this->postfields['url'] = $_POST['url'];
                break;
            case 'listbrands':
                //[{brand_id: INT, brand_name: STR}]
            case 'listsizes':
                //[{size_id: INT, size_name: STR, size_group: STR, plus_size_charge: DEC}]
            case 'listcolors':
                //[{color_id: INT, color_name: STR, color_code: HEX, color_group: STR}]
            case 'listproducts':
                //[{product_id: INT, product_name: STR, brand_id: INT, shipping_id: INT,
                //price: DEC, color_price: DEC, colors: {INT(color_id): [INT(size_id)]}}]
            case 'listshipping':
                //{INT(key): [{shipping_id: INT, shipping_name: STR, shipping_option_name: STR, shipping_company: STR,
                //shipping_zone: STR, shipping_group: INT, first_item_price: DEC}]}
            case 'listmyimages':
                //[{image_id: INT, file_name: STR, file_size: INT, date_uploaded: DATETIME}]
            case 'listadditionalsettings':
                //{hang_tag_removal_price: DEC, tag_application_price: DEC, additional_material_price: DEC}
            case 'listorders':
                //[{order_id: INT, businessname: STR, businesscontact: STR, youremail: STR, returnlabel: TXT,
                //your_order_id: STR, shipping_id: INT, shippingaddress: TXT, customerphone: INT, packingslip: STR,
                //tagremoval: BOOL, tagapplication: BOOL}]
            case 'viewapps':
                //{apps: {INT: {app_id: INT, app_name: STR, shop_url: STR, date_added: DATETIME, custom_parameters: [STR]}}}
                break;
            default:
                $notfound = true;
        }
        if( $notfound ) {
            return json_encode( array( 'result' => 0, 'message' => 'ERROR: Illegal method or method not found.' ) );
        } else {
            $this->postfields['method'] = $method;
            return $this->call_api($method);
        }
    }

    private function valid_ext( $ext )
    {
        $accepted = array( 'ai', 'eps', 'jpg', 'png', 'psd' );
        //preferred file type is 200-300 DPI transparent PNG
        return ( in_array( $ext, $accepted ) );
    }

}