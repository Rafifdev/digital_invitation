<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '../modules/penjualan_marketplace/controllers/backend/Penjualan_marketplace.php';

/**
 * Controller: Tb_marketplace_orders (Alias & Wrapper for Penjualan_marketplace)
 * Extends Penjualan_marketplace controller for 100% backward & forward URL compatibility
 */
class Tb_marketplace_orders extends Penjualan_marketplace {

    public function __construct() {
        parent::__construct();
    }
}

/* End of file Tb_marketplace_orders.php */
/* Location: ./modules/tb_marketplace_orders/controllers/backend/Tb_marketplace_orders.php */
