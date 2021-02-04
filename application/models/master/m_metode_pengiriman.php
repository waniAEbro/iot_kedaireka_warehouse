<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_metode_pengiriman extends CI_Model
{

    public function getData($value = '')
    {
        return $this->db->get('master_metode_pengiriman ');
    }

    public function insertData($data = '')
    {

        $this->db->insert('master_metode_pengiriman', $data);
    }

    public function updateData($data = '')
    {
        $this->db->where('id', $data['id']);
        $this->db->update('master_metode_pengiriman', $data);
    }

    public function deleteData($id = '')
    {
        $this->db->where('id', $id);
        $this->db->delete('master_metode_pengiriman');
    }
}

/* End of file m_metode_pengiriman.php */
/* Location: ./application/models/master/m_metode_pengiriman.php */