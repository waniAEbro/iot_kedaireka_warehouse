<?php
defined('BASEPATH') or exit('No direct script access allowed');

class kaca extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->fungsi->restrict();
        $this->load->model('master/m_kaca');
        $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
        $this->load->library('zend');
    }

    public function index()
    {
        $this->fungsi->check_previleges('kaca');
        $data['kaca'] = $this->m_kaca->getData();
        $this->load->view('master/kaca/v_kaca_list', $data);
    }

    public function form($param = '')
    {
        $content   = "<div id='divsubcontent'></div>";
        $header    = "Form Master kaca";
        $subheader = "kaca";
        $buttons[]          = button('jQuery.facebox.close()', 'Tutup', 'btn btn-default', 'data-dismiss="modal"');
        echo $this->fungsi->parse_modal($header, $subheader, $content, $buttons, "");
        if ($param == 'base') {
            $this->fungsi->run_js('load_silent("master/kaca/show_addForm/","#divsubcontent")');
        } else {
            $base_kom = $this->uri->segment(5);
            $this->fungsi->run_js('load_silent("master/kaca/show_editForm/' . $base_kom . '","#divsubcontent")');
        }
    }

    public function show_addForm()
    {
        $this->fungsi->check_previleges('kaca');
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'item_code',
                'label' => 'item_code',
                'rules' => 'required'
            )
        );
        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<span class="error-span">', '</span>');

        if ($this->form_validation->run() == FALSE) {
            $data['status'] = '';
            $this->load->view('master/kaca/v_kaca_add', $data);
        } else {
            // $datapost = get_post_data(array('item_code', 'deskripsi', 'satuan'));
            $datapost = array(
                'item_code' => $this->input->post('item_code'),
                'id_jenis_item' => 3,
                'deskripsi' => $this->input->post('deskripsi'),
                'lebar' => $this->input->post('lebar'),
                'tinggi' => $this->input->post('tinggi'),
                'area' => $this->input->post('area'),
                'satuan' => $this->input->post('satuan'),
                'created'        => date('Y-m-d H:i:s'),

            );
            // $datapost = $_POST;
            $cek = $this->m_kaca->cekMaster($datapost);
            if ($cek > 0) {
                $this->fungsi->message_box("Data Master kaca sudah ada!", "warning");
            } else {
                $this->m_kaca->insertData($datapost);
                $id_item = $this->db->insert_id();
                // $code = '2' . str_pad($id_item, 10, '0', STR_PAD_LEFT);
                $code = $datapost['item_code'];
                $this->insertbarcode($code, $id_item);
                $this->fungsi->catat($datapost, "Menambah Master kaca dengan data sbb:", true);
            }
        }
    }

    public function show_editForm($id = '')
    {
        $this->fungsi->check_previleges('kaca');
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'id',
                'label' => 'wes mbarke',
                'rules' => ''
            ),
            array(
                'field' => 'item_code',
                'label' => 'item_code',
                'rules' => 'required'
            )
        );
        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<span class="error-span">', '</span>');

        if ($this->form_validation->run() == FALSE) {
            $data['edit'] = $this->db->get_where('master_item', array('id' => $id));
            $this->load->view('master/kaca/v_kaca_edit', $data);
        } else {
            // $datapost = get_post_data(array('id', 'item_code', 'deskripsi', 'satuan','supplier','lead_time'));
            $datapost = array(
                'id' => $this->input->post('id'),
                'item_code' => $this->input->post('item_code'),
                'deskripsi' => $this->input->post('deskripsi'),
                'lebar' => $this->input->post('lebar'),
                'tinggi' => $this->input->post('tinggi'),
                'area' => $this->input->post('area'),
                'satuan' => $this->input->post('satuan'),

            );
            $this->m_kaca->updateData($datapost);
            $this->fungsi->run_js('load_silent("master/kaca","#content")');
            $this->fungsi->message_box("Data Master kaca sukses diperbarui...", "success");
            $this->fungsi->catat($datapost, "Mengedit Master kaca dengan data sbb:", true);
        }
    }

    public function import()
    {
        $data['id'] = '';
        $this->load->view('master/kaca/v_kaca_upload', $data);
    }

    public function saveimport()
    {
        $fileName = time();
        //      $upload_folder = get_upload_folder('./excel_files/');

        // $config['upload_path']   = $upload_folder;

        $config['upload_path']   = './files/';      //buat folder dengan nama excel_files di root folder
        $config['file_name']     = $fileName;
        $config['allowed_types'] = 'xls|xlsx|csv';
        $config['max_size']      = 20000;

        $this->load->library('upload');
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('file'))
            $this->upload->display_errors();

        $media         = $this->upload->data('file');
        $inputFileName = './files/' . $media['file_name'];

        try {
            $inputFileType = IOFactory::identify($inputFileName);
            $objReader     = IOFactory::createReader($inputFileType);
            $objPHPExcel   = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        $sheet         = $objPHPExcel->getSheet(0);
        $highestRow    = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        for ($row = 2; $row <= $highestRow; $row++) {                  //  Read a row of data into an array
            $rowData = $sheet->rangeToArray(
                'A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE
            );

            $data = array(
                'id_jenis_item'      => 3,
                'item_code'          => $rowData[0][0],
                'deskripsi'          => $rowData[0][1],
                'divisi'             => $rowData[0][2],
                'supplier'           => $rowData[0][3],
                'deskripsi_supplier' => $rowData[0][4],
                'satuan'             => $rowData[0][5],
                'stock_akhir_bulan'  => $rowData[0][6],
                'created'            => date('Y-m-d H:i:s'),
            );
            $cek_item = $this->m_kaca->cekMasterkaca($data['item_code']);
            if ($cek_item < 1) {
                $this->m_kaca->insertData($data);
            } else {
                $this->m_kaca->updateItemCode($data);
            }
        }
        $data['msg'] = "Data Disimpan....";
        echo json_encode($data);
    }

    public function insertbarcode($code, $id)
    {
        $this->zend->load('Zend/Barcode');
        $barcode = $code; //nomor id barcode
        $imageResource = Zend_Barcode::factory('code128', 'image', array('text' => $barcode), array())->draw();
        $imageName = $barcode . '.jpg';
        $imagePath = 'files/'; // penyimpanan file barcode
        imagejpeg($imageResource, $imagePath . $imageName);
        $pathBarcode = $imagePath . $imageName; //Menyimpan path image bardcode kedatabase

        $data = array(
            'id' => $id,
            'barcode' => $barcode,
            'image_barcode' => $pathBarcode
        );
        $this->m_kaca->updateData($data);

        $this->fungsi->run_js('load_silent("master/kaca","#content")');
        $this->fungsi->message_box("Data Master kaca sukses disimpan...", "success");
    }

    public function cetak_barcode($id)
    {
        $this->db->where('id', $id);
        $data['bcd'] = $this->db->get('master_item')->row();
        $this->load->view('master/kaca/v_kaca_cetak_barcode', $data);
    }

    public function cetakExcel()
    {
        $this->fungsi->check_previleges('kaca');
        $data['kaca'] = $this->m_kaca->getData();
        $this->load->view('master/kaca/v_kaca_cetak', $data);
    }

    public function delete()
    {
        $this->fungsi->check_previleges('kaca');
        $id   = $this->input->post('id');
        $jum_counter = $this->db->get_where('data_counter', array('id_item' => $id))->num_rows();
        $jum_transaksi = $this->db->get_where('data_stock', array('id_item' => $id))->num_rows();

        $this->db->where('id', $id);
        $res = $this->db->get('master_item')->result();
        $obj = array(
            'id_penghapus' => from_session('id'),
            'id_tabel' => $id,
            'tabel' => 'master_item',
            'keterangan' => json_encode($res),
            'created' => date('Y-m-d H:i:s'),
            'jum_counter' => $jum_counter,
            'jum_transaksi' => $jum_transaksi,
        );
        $this->db->insert('data_delete', $obj);

        sleep(1);
        $data = array('id' => $id,);
        $this->db->where('id', $id);
        $this->db->delete('master_item');

        $this->fungsi->catat($data, "Menghapus Master kaca dengan data sbb:", true);
        $respon = ['msg' => 'Data Berhasil Dihapus'];
        echo json_encode($respon);
    }
}

/* End of file kaca.php */
/* Location: ./application/controllers/master/kaca.php */