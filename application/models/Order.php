<?php

class Order extends CI_Model {

    protected $xml = array();
    protected $customer = "";
    protected $ordertype = "";
    protected $burgers = array();
    protected $patty_names = array();
    protected $patties = array();
    

    // Constructor
    public function __construct() {
        parent::__construct();
    }

    // retrieve a list of patties, to populate a dropdown, for instance
    function patties() {
        return $this->patty_names;
    }
    function getOrderType(){
        return $this->ordertype;
    }
    function getCustomer(){
        return $this->customer;
    }
    function getBurgers(){
        return $this->burgers;
    }
    
    function setXml($filename){
        $this->xml = simplexml_load_file(DATAPATH . $filename);
        $this->customer = $this->xml->customer;
        $this->ordertype = $this->xml['type'];
        
        foreach ($this->xml->burger as $burger) {
            $record = new stdClass();
            $record->patty = $this->menu->getPatty((string) $burger->patty['type']);
            $record->cheeses['top'] = $this->menu->getCheese((string)$burger->cheeses['top']);
            $record->cheeses['bottom'] = $this->menu->getCheese((string)$burger->cheeses['bottom']);
            $record->toppings = array();
            foreach($burger->topping as $topping){
                $record->toppings[] = $this->menu->getTopping((string)$topping['type']);
            }
            $record->sauces = array();
            foreach($burger->sauce as $sauce){
                $record->sauces[] = $this->menu->getSauce((string)$sauce['type']);
            }
            $this->burgers[] = $record;
        }
    }

    // retrieve a patty record, perhaps for pricing
    function getPatty($code) {
        if (isset($this->patties[$code]))
            return $this->patties[$code];
        else
            return null;
    }

}
