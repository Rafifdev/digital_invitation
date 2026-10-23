<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_orders extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'orders';
    private $field_search   = ['invoice_number', 'user_id', 'template_id', 'invitation_id', 'amount', 'payment_method', 'payment_proof', 'payment_status', 'verified_by', 'paid_at', 'created_at', 'updated_at'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    public function count_all($q = null, $field = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "orders.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "orders.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "orders.".$field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [])
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "orders.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "orders.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "orders.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('orders.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('aauth_users', 'aauth_users.id = orders.user_id', 'LEFT');
        $this->db->join('templates', 'templates.id = orders.template_id', 'LEFT');
        $this->db->join('aauth_users aauth_users1', 'aauth_users1.id = orders.verified_by', 'LEFT');
        
        $this->db->select('orders.*, aauth_users.username as aauth_user_username, aauth_users.full_name as aauth_user_name, templates.name as templates_name, aauth_users1.full_name as verified_by_name');

        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_orders.php */
/* Location: ./application/models/Model_orders.php */