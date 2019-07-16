<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class M_kegiatan_jabatan extends CI_Model {

    var $table = 'opmt_kegiatan_jabatan a';
    var $column_order = array(null, 'jabatan'); //set column field database for datatable orderable
    var $column_search = array('jabatan'); //set column field database for datatable searchable 
    var $order = array('jabatan' => 'asc'); // default order 

    public function __construct() {
        parent::__construct();
        $this->db->_protect_identifiers = false;
        $this->load->database();
    }

    private function _get_datatables_query($nama) {
        $this->db->from($this->table);
        $this->db->join('tbljabatan b', 'a.kodejab=b.kodejab', 'LEFT');
        $this->db->where('jenis', 'jft');
        if (!empty($nama)) {
            $this->db->like('b.jabatan', $nama);
        }
        $i = 0;

        foreach ($this->column_search as $item) { // loop column 
                if (!empty($_POST['search']['value'])) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) { // here order processing
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($nama) {
        $this->_get_datatables_query($nama);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($nama) {
        $this->_get_datatables_query($nama);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($nama) {
        if (!empty($nama)) {
            $this->db->like('b.jabatan', $nama);
        }
        $this->db->from($this->table);
        $this->db->join('tbljabatan b', 'a.kodejab=b.kodejab', 'LEFT');
        $this->db->where('jenis', 'jft');
        return $this->db->count_all_results();
    }

}