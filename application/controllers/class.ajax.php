<?php

class ajax extends controller {
    
    public function beforeRun() {
        $this -> render = false;
        parent::beforeRun();
    }

    public function __index() {
        echo 'ee';
    }

    public function addtocart() {
        
        if ($this -> id) {
            
            if (isset($_SESSION['cart']['products'])) {

                if (array_key_exists($this -> id, $_SESSION['cart']['products'])) {
                    $current = $_SESSION['cart']['products'][$this -> id];
                    $_SESSION['cart']['products'][$this -> id] = $current + 1;
                } else {
                    $_SESSION['cart']['products'][$this -> id] = 1;
                }

            } else {
                $_SESSION['cart']['products'][$this -> id] = 1;
            }

            self::getcart();

        }

    }

    public function updateCartQuantity() {
         $product_id = url(3);
         $quantity = trim(url(4));

         if (ctype_digit($quantity) && array_key_exists($product_id, $_SESSION['cart']['products'])) {
              $_SESSION['cart']['products'][$product_id] = $quantity;

              // Er wordt bekeken of het opgegeven aantal wel op voorraad is
              if (read_option('voorraad')) {
                   $result = $this -> model -> find('products', 'stock', array(
                                                                           'conditions' => array(
                                                                               'id' => $product_id
                                                                           )
                   ));

                   $in_stock = $result[0]['products']['stock'];
                   echo ($quantity > $in_stock ? 'false' : 'true');
              } else {
                   echo 'true';
              }

              echo '|';

              // De nieuwe prijzen worden ingeladen
              self::getCart();
         } else {
              echo 'false';
         }
    }

    public function getcart() {

        if (isset($_SESSION['cart']['products'])) {

            $products = $_SESSION['cart']['products'];

            $total_price = 0;
            $subtotal_price = 0;
            $product_count = 0;
            foreach($products as $id=>$quantity) {

                // De prijs van het product wordt opgezocht in de database
                $result = $this -> model -> find('products', 'price', array(
                                                                        'conditions' => array(
                                                                            'id' => $id
                                                                        )
                ));

                if ($this -> model -> num_rows > 0) {
                    $product_count = $product_count + $quantity;

                    $subtotal_price = $subtotal_price + ($result[0]['products']['price'] * $quantity);
                    $total_price = $total_price + (calculatePrice($result[0]['products']['price']) * $quantity);
                } else {
                    unset($_SESSION['cart']['products'][$id]);
                }

            }

            // De totale prijs wordt berekend
            $total_price = money($total_price);
            $subtotal_price = money($subtotal_price);

            // De output wordt geretouneerd
            echo $product_count . ':' . $total_price . ':' . $subtotal_price;

            // De product sessie wordt aangemaakt
            $_SESSION['cart']['info']['price'] = $total_price;
            $_SESSION['cart']['info']['count'] = $product_count;


        } else {
            echo '0:' . money(0). ':' . money(0);
        }

    }

    public function removefromcart() {

        if ($this -> id) {

            if (array_key_exists($this -> id, $_SESSION['cart']['products'])) {
                unset($_SESSION['cart']['products'][$this -> id]);

                // De huidige content van de winkelwagen wordt weergegeven
                self::getcart();
            } else {
                echo 'false';
            }

        } else {
            echo 'false';
        }

    }

}