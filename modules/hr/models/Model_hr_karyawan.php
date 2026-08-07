<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_hr_karyawan extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'hr_karyawan';
	private $field_search 	= ['hr_karyawan.nik', 'hr_karyawan.jabatan', 'aauth_users.full_name'];

	public function __construct()
	{
		$config = array(
			'primary_key' 	=> $this->primary_key,
		 	'table_name' 	=> $this->table_name,
		 	'field_search' 	=> $this->field_search,
		 );
		parent::__construct($config);
	}

	public function join_available()
	{
		$this->db->select('hr_karyawan.*, aauth_users.full_name, aauth_users.username');
		$this->db->join('aauth_users', 'aauth_users.id = hr_karyawan.user_id', 'LEFT');
		return $this;
	}

	public function count_all($q = '', $field = '')
	{
		$this->join_available();
		$where = $this->build_where($q, $field);
		if (!empty($where)) { $this->db->where($where); }
		return $this->db->get($this->table_name)->num_rows();
	}

	public function get($q = '', $field = '', $limit = 0, $offset = 0)
	{
		$this->join_available();
		$where = $this->build_where($q, $field);
		if (!empty($where)) { $this->db->where($where); }
		$this->db->limit($limit, $offset);
		$this->db->order_by('hr_karyawan.'.$this->primary_key, 'DESC');
		return $this->db->get($this->table_name)->result();
	}

	private function build_where($q = '', $field = '')
	{
		$iterasi = 1;
		$num = count($this->field_search);
		$where = NULL;
		$q = $this->scurity($q);
		$field = $this->scurity($field);
		if (!empty($q)) {
			if (empty($field)) {
				foreach ($this->field_search as $f) {
					if ($iterasi == 1) { $where .= "(" . $f . " LIKE '%" . $q . "%' "; } 
                    else if ($iterasi == $num) { $where .= "OR " . $f . " LIKE '%" . $q . "%') "; } 
                    else { $where .= "OR " . $f . " LIKE '%" . $q . "%' "; }
					$iterasi++;
				}
			} else {
				$where .= "(" . $field . " LIKE '%" . $q . "%' )";
			}
		}
		return $where;
	}

    public function find_with_detail($id)
    {
        $this->join_available();
        return $this->db->get_where($this->table_name, ['hr_karyawan.id' => $id])->row();
    }
}
