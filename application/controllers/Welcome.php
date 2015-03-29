<?php

/**
 * Our homepage. Show the most recently added quote.
 * 
 * controllers/Welcome.php
 *
 * ------------------------------------------------------------------------
 */
class Welcome extends Application {

    protected $burgerTotal = 0;
    function __construct() {
        parent::__construct();
    }

    //-------------------------------------------------------------
    //  Homepage: show a list of the orders on file
    //-------------------------------------------------------------

    function index() {
        // Build a list of orders
        // Present the list to choose from
        $this->data['pagebody'] = 'homepage';
        $dir_map = directory_map('data/');
        $orders = array();
        foreach ($dir_map as $file) {
            if (strcmp(pathinfo($file, PATHINFO_EXTENSION), "xml") == 0) {
                if (strcmp($file, "menu.xml") != 0) {
                    $orders[] = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file);
                }
            }
        }
        $list = "<ul>";
        foreach ($orders as $order) {
            $this->order->setXml($order . '.xml');
            $list .= "<li><a href ='/welcome/order/" . $order . "'>" . $order . " (".$this->order->getCustomer().")</a></li>";
        }
        $list .= "</ul>";
        $this->data['order'] = $list;



        $this->render();
    }

    //-------------------------------------------------------------
    //  Show the "receipt" for a specific order
    //-------------------------------------------------------------

    function order($num) {
        // Build a receipt for the chosen order
        $this->order->setXml($num . '.xml');
        $output = ucfirst($num) . " for " . $this->order->getCustomer() . " (" . $this->order->getOrdertype() . ") <br>";
        $burgers = $this->order->getBurgers();
        $burgernum = 1;
        $orderTotal = 0;
        foreach ($burgers as $burger) {
            $this->burgerTotal = 0;
            $output .= "<br>*Burger #" . $burgernum . "*<br>";
            $output .= "Base: " . $burger->patty->name . "<br>";
            $this->burgerTotal += $burger->patty->price;
            $output = $this->setCheeses($burger, $output);
            $output = $this->setToppings($burger, $output);
            $output = $this->setSauces($burger, $output);
            $output .= "Burger total: $".$this->burgerTotal."<br>";
            $burgernum++;
            $orderTotal += $this->burgerTotal;
        }
        $output .= "<br>Order TOTAL: $".$orderTotal."<br>";
        $this->data['output'] = $output;
        // Present the list to choose from
        $this->data['pagebody'] = 'justone';
        $this->render();
    }

    function setCheeses($burger, $output) {
        if ($burger->cheeses['top'] != NULL || $burger->cheeses['bottom'] != NULL) {
            $output .= "Cheese: ";
            if ($burger->cheeses['top'] != NULL) {
                $output .= $burger->cheeses['top']->name . " (top)";
                $this->burgerTotal += $burger->cheeses['top']->price;
            }
            if ($burger->cheeses['bottom'] != NULL) {
                if ($burger->cheeses['top'] != NULL) $output.=", ";
                $output .= $burger->cheeses['bottom']->name . " (bottom) ";
                $this->burgerTotal += $burger->cheeses['bottom']->price;
            }
            
         $output .="<br>";
        }
        return $output;
    }
    
    function setSauces($burger, $output){
        $output .= "Sauces: ";
        $size = count($burger->sauces);
        if($size>0){
            $count = 1;
            foreach($burger->sauces as $sauce){
                $output .= $sauce->name;
                if($count < $size) $output.=", ";
                $count++;
            }
        }else{
            $output .= "none"; 
        }
        $output .="<br>";
        return $output;
    }
    
    function setToppings($burger, $output){
        $output .= "Toppings: ";
        $size = count($burger->toppings);
        if($size>0){
            $count = 1;
            foreach($burger->toppings as $topping){
                $output .= $topping->name;
                $this->burgerTotal += $topping->price;
                if($count < $size) $output.=", ";
                $count++;
            }
        }else{
            $output .= "none"; 
        }
        $output .="<br>";
        return $output;
    }
}
