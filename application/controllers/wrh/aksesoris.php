<?php
defined('BASEPATH') or exit('No direct script access allowed');

class aksesoris extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->fungsi->restrict();
        $this->load->model('wrh/m_aksesoris');
        $this->load->model('klg/m_fppp');
    }

    public function index()
    {
        $this->fungsi->check_previleges('aksesoris');
        $data['aksesoris']           = $this->m_aksesoris->getData();
        $data['stock_awal_bulan']    = $this->m_aksesoris->getStockAwalBulan();
        $data['total_bom']           = $this->m_aksesoris->getTotalBOM();
        $data['total_in_per_bulan']  = $this->m_aksesoris->getTotalInPerBulan();
        $data['total_out_per_bulan'] = $this->m_aksesoris->getTotalOutPerBulan();
        $data['warna']               = 'Warna';
        $this->load->view('wrh/aksesoris/v_aksesoris_list', $data);
    }

    public function monitoring_mf()
    {
        $this->fungsi->check_previleges('aksesoris');
        $data['aksesoris']           = $this->m_aksesoris->getdataMf();
        $data['stock_awal_bulan']    = $this->m_aksesoris->getStockAwalBulan();
        $data['total_bom']           = $this->m_aksesoris->getTotalBOM();
        $data['total_in_per_bulan']  = $this->m_aksesoris->getTotalInPerBulan();
        $data['total_out_per_bulan'] = $this->m_aksesoris->getTotalOutPerBulan();
        $data['warna']               = 'MF';
        $this->load->view('wrh/aksesoris/v_aksesoris_list', $data);
    }

    public function getDetailTabel()
    {
        $this->fungsi->check_previleges('aksesoris');
        $id                = $this->input->post('id');
        $data_aksesoris_in = $this->m_aksesoris->getDataDetailTabel($id);
        $arr               = array();
        foreach ($data_aksesoris_in as $key) {
            $stok_awal_bulan = $this->m_aksesoris->getAwalBulanDetailTabel($key->id_item, $key->id_divisi, $key->id_gudang, $key->keranjang);
            $qtyin           = $this->m_aksesoris->getQtyInDetailTabelMonth($key->id_item, $key->id_divisi, $key->id_gudang, $key->keranjang);
            $qtyout          = $this->m_aksesoris->getQtyOutDetailTabel($key->id_item, $key->id_divisi, $key->id_gudang, $key->keranjang);
            $temp            = array(
                "divisi"           => $key->divisi,
                "gudang"           => $key->gudang,
                "keranjang"        => $key->keranjang,
                "stok_awal_bulan"  => $stok_awal_bulan,
                "tot_in"           => $qtyin,
                "tot_out"          => $qtyout,
                "stok_akhir_bulan" => $qtyin - $qtyout,
                "rata_pemakaian"   => '0',
                "min_stock"        => '0',
            );

            array_push($arr, $temp);
            // echo $key->gt . '<br>';
        }
        $data['detail'] = $arr;
        echo json_encode($data);
    }

    public function stok_in()
    {
        $this->fungsi->check_previleges('aksesoris');
        $data['aksesoris'] = $this->m_aksesoris->getDataStock();

        $this->load->view('wrh/aksesoris/v_aksesoris_in_list', $data);
    }

    public function stok_in_add()
    {
        $this->fungsi->check_previleges('aksesoris');
        $data['item']     = $this->m_aksesoris->getdataItem();
        $data['divisi']   = $this->db->get_where('master_divisi_stock', array('id_jenis_item' => 2));
        $data['gudang']   = $this->db->get_where('master_gudang', array('id_jenis_item' => 2));
        $data['supplier'] = $this->db->get_where('master_supplier', array('id_jenis_item' => 2));
        $this->load->view('wrh/aksesoris/v_aksesoris_in', $data);
    }

    public function savestokin($value = '')
    {
        $this->fungsi->check_previleges('aksesoris');

        $datapost = array(
            'id_item'        => $this->input->post('item'),
            'inout'          => 1,
            'id_jenis_item'  => 2,
            'qty_in'         => $this->input->post('qty'),
            'id_supplier'    => $this->input->post('supplier'),
            'no_surat_jalan' => $this->input->post('no_surat_jalan'),
            'no_pr'          => $this->input->post('no_pr'),
            'id_divisi'      => $this->input->post('id_divisi'),
            'id_gudang'      => $this->input->post('id_gudang'),
            'keranjang'      => $this->input->post('keranjang'),
            'keterangan'     => $this->input->post('keterangan'),
            'created'        => date('Y-m-d H:i:s'),
        );
        $this->m_aksesoris->insertstokin($datapost);
        $data['id'] = $this->db->insert_id();
        $this->fungsi->catat($datapost, "Menyimpan detail stock-in aksesoris sbb:", true);
        $cekDataCounter = $this->m_aksesoris->getDataCounter($datapost['id_item'], $datapost['id_divisi'], $datapost['id_gudang'], $datapost['keranjang'])->num_rows();
        if ($cekDataCounter == 0) {
            $simpan = array(
                'id_jenis_item' => 2,
                'id_item'       => $this->input->post('item'),
                'id_divisi'     => $this->input->post('id_divisi'),
                'id_gudang'     => $this->input->post('id_gudang'),
                'keranjang'     => $this->input->post('keranjang'),
                'qty'           => $this->input->post('qty'),
                'created'       => date('Y-m-d H:i:s'),
            );
            $this->db->insert('data_counter', $simpan);
        } else {
            $cekQtyCounter = $this->m_aksesoris->getDataCounter($datapost['id_item'], $datapost['id_divisi'], $datapost['id_gudang'], $datapost['keranjang'])->row()->qty;
            $qty_jadi      = (int)$datapost['qty_in'] + (int)$cekQtyCounter;
            $this->m_aksesoris->updateDataCounter($datapost['id_item'], $datapost['id_divisi'], $datapost['id_gudang'], $datapost['keranjang'], $qty_jadi);
        }
        $data['msg'] = "stock Disimpan";
        echo json_encode($data);
    }

    public function deleteItemIn()
    {
        $this->fungsi->check_previleges('aksesoris');
        $id   = $this->input->post('id');
        $data = array('id' => $id,);
        $this->db->where('id', $id);
        $this->db->delete('data_stock');

        $this->fungsi->catat($data, "Menghapus Stock In aksesoris dengan data sbb:", true);
        $respon = ['msg' => 'Data Berhasil Dihapus'];
        echo json_encode($respon);
    }

    public function stok_out()
    {
        $this->fungsi->check_previleges('aksesoris');
        // $data['surat_jalan'] = $this->m_aksesoris->getSuratJalan(1, 1);
        $id_jenis_item = 2;
        $data['qty_bom']     = $this->m_aksesoris->getTotQtyBomFppp($id_jenis_item);
        $data['qty_out']     = $this->m_aksesoris->getTotQtyOutFppp($id_jenis_item);
        $data['dataFpppOut'] = $this->m_aksesoris->getFpppStockOut($id_jenis_item);
        $this->load->view('wrh/aksesoris/v_aksesoris_out_list', $data);
    }

    public function stok_out_make($id_fppp)
    {
        $id_jenis_item = 2;
        $data['id_fppp']   = $id_fppp;
        $data['rowFppp']   = $this->m_aksesoris->getRowFppp($id_fppp);
        $data['list_bom']  = $this->m_aksesoris->getItemBom($id_fppp);
        $data['divisi']    = $this->m_aksesoris->getDivisiBom($id_jenis_item);
        $data['gudang']    = $this->m_aksesoris->getGudangBom($id_jenis_item);
        $data['keranjang'] = $this->m_aksesoris->getKeranjangBom($id_jenis_item);
        $data['aksesoris'] = $this->m_aksesoris->getAllDataCounter($id_jenis_item);
        $this->load->view('wrh/aksesoris/v_aksesoris_detail_bom', $data);
    }

    public function stok_out_make_mf($id_fppp)
    {
        $id_jenis_item = 2;
        $data['id_fppp']   = $id_fppp;
        $data['rowFppp']   = $this->m_aksesoris->getRowFppp($id_fppp);
        $data['list_bom']  = $this->m_aksesoris->getItemBomMf($id_fppp);
        $data['divisi']    = $this->m_aksesoris->getDivisiBom($id_jenis_item);
        $data['gudang']    = $this->m_aksesoris->getGudangBomMf($id_jenis_item);
        $data['keranjang'] = $this->m_aksesoris->getKeranjangBom($id_jenis_item);
        $data['aksesoris'] = $this->m_aksesoris->getAllDataCounter($id_jenis_item);
        $this->load->view('wrh/aksesoris/v_aksesoris_detail_bom_mf', $data);
    }

    public function saveout()
    {
        $this->fungsi->check_previleges('aksesoris');
        $field  = $this->input->post('field');
        $value  = $this->input->post('value');
        $editid = $this->input->post('id');
        // $id_fppp = $this->input->post('id_fppp');
        if ($field == 'produksi_' . $editid) {
            $this->m_aksesoris->editRowOut('produksi', $value, $editid);
            $this->m_aksesoris->editRowOut('lapangan', 0, $editid);
        } else if ($field == 'lapangan_' . $editid) {
            $this->m_aksesoris->editRowOut('lapangan', $value, $editid);
            $this->m_aksesoris->editRowOut('produksi', 0, $editid);
        } else {
            $obj = array(
                'id_divisi' => $this->input->post('divisi'),
                'id_gudang' => $this->input->post('gudang'),
                'keranjang' => $this->input->post('keranjang'),
                'qty_out'   => $value,
            );
            $this->m_aksesoris->editQtyOut($editid, $obj);
        }
        if ($field == 'qty_out') {
            $this->m_aksesoris->editStatusInOut($editid);
        }
        $id_item      = $this->db->get_where('data_stock', array('id' => $editid))->row()->id_item;
        $id_divisi    = $this->input->post('divisi');
        $id_gudang    = $this->input->post('gudang');
        $keranjang    = $this->input->post('keranjang');
        $qtyin        = $this->m_aksesoris->getQtyInDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $qtyout       = $this->m_aksesoris->getQtyOutDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $data['qty_gudang'] = $qtyin - $qtyout;
        $this->m_aksesoris->updateDataCounter($id_item, $id_divisi, $id_gudang, $keranjang, $data['qty_gudang']);

        $data['status'] = "berhasil";
        echo json_encode($data);
    }

    public function saveoutcheck()
    {
        $this->fungsi->check_previleges('aksesoris');
        $field  = $this->input->post('field');
        $value  = $this->input->post('value');
        $editid = $this->input->post('id');
        // $id_fppp = $this->input->post('id_fppp');
        if ($field == 'produksi_' . $editid) {
            $this->m_aksesoris->editRowOut('produksi', $value, $editid);
            $this->m_aksesoris->editRowOut('lapangan', 0, $editid);
        } else if ($field == 'lapangan_' . $editid) {
            $this->m_aksesoris->editRowOut('lapangan', $value, $editid);
            $this->m_aksesoris->editRowOut('produksi', 0, $editid);
        }
        $obj = array(
            'id_divisi' => $this->input->post('divisi'),
            'id_gudang' => $this->input->post('gudang'),
            'keranjang' => $this->input->post('keranjang'),
            'qty_out'   => $this->input->post('qtytxt'),
        );
        $this->m_aksesoris->editQtyOut($editid, $obj);
        $this->m_aksesoris->editStatusInOut($editid);
        $id_item      = $this->db->get_where('data_stock', array('id' => $editid))->row()->id_item;
        $id_divisi    = $this->input->post('divisi');
        $id_gudang    = $this->input->post('gudang');
        $keranjang    = $this->input->post('keranjang');
        $qtyin        = $this->m_aksesoris->getQtyInDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $qtyout       = $this->m_aksesoris->getQtyOutDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $data['qty_gudang'] = $qtyin - $qtyout;
        $this->m_aksesoris->updateDataCounter($id_item, $id_divisi, $id_gudang, $keranjang, $data['qty_gudang']);

        $data['status'] = "berhasil";
        echo json_encode($data);
    }

    public function kirim_parsial($id_fppp, $id_stock)
    {
        $this->fungsi->check_previleges('aksesoris');
        $getRowStock = $this->m_aksesoris->getRowStock($id_stock);
        $object      = array(
            'id_fppp'       => $id_fppp,
            'is_bom'        => $getRowStock->is_bom,
            'id_jenis_item' => $getRowStock->id_jenis_item,
            'id_item'       => $getRowStock->id_item,
            'qty_bom'       => $getRowStock->qty_bom,
            'created'       => date('Y-m-d H:i:s'),
        );
        $this->db->insert('data_stock', $object);
        $this->fungsi->message_box("Kirim Parsial berhasil", "success");
        $this->fungsi->catat($object, "Membuat kirim parsial data sbb:", true);
        $this->fungsi->run_js('load_silent("wrh/aksesoris/stok_out_make/' . $id_fppp . '","#content")');
    }

    public function buat_surat_jalan($id_fppp)
    {
        $this->fungsi->check_previleges('aksesoris');
        $data['id_fppp']        = $id_fppp;
        $data['row_fppp']        = $this->m_aksesoris->getRowFppp($id_fppp);
        $kode_divisi = $this->m_aksesoris->getKodeDivisi($id_fppp);
        $data['no_surat_jalan'] = str_pad($this->m_aksesoris->getNoSuratJalan(), 3, '0', STR_PAD_LEFT) . '/SJ/' . $kode_divisi . '/' . date('m') . '/' . date('Y');

        $this->load->view('wrh/aksesoris/v_aksesoris_buat_surat_jalan', $data);
    }

    public function stok_out_add()
    {
        $this->fungsi->check_previleges('aksesoris');
        $data['no_fppp']        = $this->db->get_where('data_fppp', array('id_status' => 1));
        $data['no_surat_jalan'] = str_pad($this->m_aksesoris->getNoSuratJalan(), 3, '0', STR_PAD_LEFT) . '/SJ/AK/' . date('m') . '/' . date('Y');

        $this->load->view('wrh/aksesoris/v_aksesoris_out', $data);
    }

    public function list_surat_jalan()
    {
        $this->fungsi->check_previleges('aksesoris');
        $data['surat_jalan'] = $this->m_aksesoris->getSuratJalan(1, 2);
        $this->load->view('wrh/aksesoris/v_aksesoris_out_sj_list', $data);
    }

    public function getDetailFppp()
    {
        $this->fungsi->check_previleges('aksesoris');
        $id = $this->input->post('no_fppp');

        $data['nama_proyek']         = $this->m_aksesoris->getRowFppp($id)->nama_proyek;
        $data['alamat_proyek']       = $this->m_aksesoris->getRowFppp($id)->alamat_proyek;
        $data['sales']               = $this->m_aksesoris->getRowFppp($id)->sales;
        $data['deadline_pengiriman'] = $this->m_aksesoris->getRowFppp($id)->deadline_pengiriman;
        echo json_encode($data);
    }



    public function simpanSuratJalan()
    {
        $this->fungsi->check_previleges('aksesoris');
        $no_fppp           = $this->input->post('no_fppp');
        $penerima          = $this->input->post('penerima');
        $alamat_pengiriman = $this->input->post('alamat_pengiriman');
        $sopir             = $this->input->post('sopir');
        $no_kendaraan      = $this->input->post('no_kendaraan');
        $kode_divisi = $this->m_aksesoris->getKodeDivisi($no_fppp);
        $no_surat_jalan = str_pad($this->m_aksesoris->getNoSuratJalan(), 3, '0', STR_PAD_LEFT) . '/SJ/' . $kode_divisi . '/' . date('m') . '/' . date('Y');
        $obj               = array(
            'id_fppp'           => $no_fppp,
            'no_surat_jalan'    => $no_surat_jalan,
            'penerima'          => $penerima,
            'alamat_pengiriman' => $alamat_pengiriman,
            'sopir'             => $sopir,
            'no_kendaraan'      => $no_kendaraan,
            'id_jenis_item'     => 2,
            'tipe'              => 1,
            'created'           => date('Y-m-d H:i:s'),
        );
        $this->db->insert('data_surat_jalan', $obj);
        $data['id']    = $this->db->insert_id();
        $this->m_aksesoris->updateJadiSuratJalan($no_fppp, $data['id']);
        echo json_encode($data);
    }

    public function updateSuratJalan()
    {
        $this->fungsi->check_previleges('aksesoris');
        $id_sj             = $this->input->post('id_sj');
        $penerima          = $this->input->post('penerima');
        $alamat_pengiriman = $this->input->post('alamat_pengiriman');
        $sopir             = $this->input->post('sopir');
        $no_kendaraan      = $this->input->post('no_kendaraan');
        $obj               = array(
            'penerima'          => $penerima,
            'alamat_pengiriman' => $alamat_pengiriman,
            'sopir'             => $sopir,
            'no_kendaraan'      => $no_kendaraan,
            'updated'           => date('Y-m-d H:i:s'),
        );
        $this->db->where('id', $id_sj);
        $this->db->update('data_surat_jalan', $obj);
        $data['id'] = 'ok';
        echo json_encode($data);
    }

    public function detailbom($id_sj)
    {
        $this->fungsi->check_previleges('aksesoris');

        $data['id_sj']               = $id_sj;
        $id                    = $this->m_aksesoris->getRowSj($id_sj)->row()->id_fppp;
        $data['id_fppp']             = $id;
        $data['list_bom_sj']         = $this->m_aksesoris->getBomSJ($id_sj);
        $data['no_fppp']             = $this->m_aksesoris->getRowFppp($id)->no_fppp;
        $data['nama_proyek']         = $this->m_aksesoris->getRowFppp($id)->nama_proyek;
        $data['alamat_proyek']       = $this->m_aksesoris->getRowFppp($id)->alamat_proyek;
        $data['sales']               = $this->m_aksesoris->getRowFppp($id)->sales;
        $data['deadline_pengiriman'] = $this->m_aksesoris->getRowFppp($id)->deadline_pengiriman;
        $data['no_surat_jalan']      = $this->m_aksesoris->getRowSj($id_sj)->row()->no_surat_jalan;
        $data['penerima']            = $this->m_aksesoris->getRowSj($id_sj)->row()->penerima;
        $data['alamat_pengiriman']   = $this->m_aksesoris->getRowSj($id_sj)->row()->alamat_pengiriman;
        $data['sopir']               = $this->m_aksesoris->getRowSj($id_sj)->row()->sopir;
        $data['no_kendaraan']        = $this->m_aksesoris->getRowSj($id_sj)->row()->no_kendaraan;
        // $data['divisi']              = get_options($this->db->get_where('master_divisi_stock', array('id_jenis_item' => 1)), 'id', 'divisi', true);
        $data['divisi']    = $this->m_aksesoris->getDivisiBom();
        $data['gudang']    = $this->m_aksesoris->getGudangBom();
        $data['keranjang'] = $this->m_aksesoris->getKeranjangBom();
        $this->load->view('wrh/aksesoris/v_aksesoris_detail_bom', $data);
    }

    // public function kuncidetailbom($id_sj, $id_detail)
    // {
    //     $this->fungsi->check_previleges('aksesoris');
    //     $this->m_aksesoris->kuncidetailbom($id_detail);
    //     $data['id_sj']               = $id_sj;
    //     $id                    = $this->m_aksesoris->getRowSj($id_sj)->row()->id_fppp;
    //     $data['id_fppp']             = $id;
    //     $data['list_bom_sj']         = $this->m_aksesoris->getBomSJ($id_sj);
    //     $data['no_fppp']             = $this->m_aksesoris->getRowFppp($id)->no_fppp;
    //     $data['nama_proyek']         = $this->m_aksesoris->getRowFppp($id)->nama_proyek;
    //     $data['alamat_proyek']       = $this->m_aksesoris->getRowFppp($id)->alamat_proyek;
    //     $data['sales']               = $this->m_aksesoris->getRowFppp($id)->sales;
    //     $data['deadline_pengiriman'] = $this->m_aksesoris->getRowFppp($id)->deadline_pengiriman;
    //     $data['no_surat_jalan']      = $this->m_aksesoris->getRowSj($id_sj)->row()->no_surat_jalan;
    //     $data['penerima']            = $this->m_aksesoris->getRowSj($id_sj)->row()->penerima;
    //     $data['alamat_pengiriman']   = $this->m_aksesoris->getRowSj($id_sj)->row()->alamat_pengiriman;
    //     $data['sopir']               = $this->m_aksesoris->getRowSj($id_sj)->row()->sopir;
    //     $data['no_kendaraan']        = $this->m_aksesoris->getRowSj($id_sj)->row()->no_kendaraan;
    //     $data['divisi']              = $this->m_aksesoris->getDivisiBom();
    //     $data['gudang']              = $this->m_aksesoris->getGudangBom();
    //     $data['keranjang']           = $this->m_aksesoris->getKeranjangBom();
    //     $this->load->view('wrh/aksesoris/v_aksesoris_detail_bom', $data);
    // }

    // public function bukakuncidetailbom($id_sj, $id_detail)
    // {
    //     $this->fungsi->check_previleges('aksesoris');
    //     $this->m_aksesoris->bukakuncidetailbom($id_detail);
    //     $data['id_sj']               = $id_sj;
    //     $id                    = $this->m_aksesoris->getRowSj($id_sj)->row()->id_fppp;
    //     $data['id_fppp']             = $id;
    //     $data['list_bom_sj']         = $this->m_aksesoris->getBomSJ($id_sj);
    //     $data['no_fppp']             = $this->m_aksesoris->getRowFppp($id)->no_fppp;
    //     $data['nama_proyek']         = $this->m_aksesoris->getRowFppp($id)->nama_proyek;
    //     $data['alamat_proyek']       = $this->m_aksesoris->getRowFppp($id)->alamat_proyek;
    //     $data['sales']               = $this->m_aksesoris->getRowFppp($id)->sales;
    //     $data['deadline_pengiriman'] = $this->m_aksesoris->getRowFppp($id)->deadline_pengiriman;
    //     $data['no_surat_jalan']      = $this->m_aksesoris->getRowSj($id_sj)->row()->no_surat_jalan;
    //     $data['penerima']            = $this->m_aksesoris->getRowSj($id_sj)->row()->penerima;
    //     $data['alamat_pengiriman']   = $this->m_aksesoris->getRowSj($id_sj)->row()->alamat_pengiriman;
    //     $data['sopir']               = $this->m_aksesoris->getRowSj($id_sj)->row()->sopir;
    //     $data['no_kendaraan']        = $this->m_aksesoris->getRowSj($id_sj)->row()->no_kendaraan;
    //     $data['divisi']              = $this->m_aksesoris->getDivisiBom();
    //     $data['gudang']              = $this->m_aksesoris->getGudangBom();
    //     $data['keranjang']           = $this->m_aksesoris->getKeranjangBom();
    //     $this->load->view('wrh/aksesoris/v_aksesoris_detail_bom', $data);
    // }

    public function finishdetailbom($id_sj)
    {
        $this->fungsi->check_previleges('aksesoris');
        $this->m_aksesoris->finishdetailbom($id_sj);
        $datapost = array('id_sj' => $id_sj,);
        $this->fungsi->message_box("Fisnish Surat Jalan", "success");
        $this->fungsi->catat($datapost, "Finish Suraj Jalan dengan id:", true);
        $this->stok_out();
    }

    public function additemdetailbom($id_sj, $id_fppp)
    {
        $content   = "<div id='divsubcontent'></div>";
        $header    = "Form Tambah Item BOM";
        $subheader = "";
        $buttons[]          = button('', 'Tutup', 'btn btn-default', 'data-dismiss="modal"');
        echo $this->fungsi->parse_modal($header, $subheader, $content, $buttons, "");
        $this->fungsi->run_js('load_silent("wrh/aksesoris/showformitemdetailbom/' . $id_sj . '/' . $id_fppp . '","#divsubcontent")');
    }

    public function showformitemdetailbom($id_sj = '', $id_fppp = '')
    {
        $this->fungsi->check_previleges('aksesoris');
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'id_item',
                'label' => 'id_item',
                'rules' => 'required'
            )
        );
        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<span class="error-span">', '</span>');

        if ($this->form_validation->run() == FALSE) {
            $data['id_fppp'] = $id_fppp;
            $data['id_sj']   = $id_sj;
            $data['item']    = $this->db->get_where('master_item', array('id_jenis_item' => 2,));
            $this->load->view('wrh/aksesoris/v_aksesoris_add_item_bom', $data);
        } else {
            $datapost_bom = array(
                'id_fppp'       => $this->input->post('id_fppp'),
                'id_jenis_item' => 2,
                'id_item'       => $this->input->post('id_item'),
                'qty'           => $this->input->post('qty'),
                'keterangan'    => 'TAMBAHAN',
                'created'       => date('Y-m-d H:i:s'),
            );
            $this->db->insert('data_fppp_bom', $datapost_bom);

            $object = array(
                'inout'          => 0,
                'id_fppp'        => $this->input->post('id_fppp'),
                'id_surat_jalan' => $this->input->post('id_sj'),
                'id_jenis_item'  => 2,
                'id_item'        => $this->input->post('id_item'),
                'qty_bom'        => $this->input->post('qty'),
                'created'        => date('Y-m-d H:i:s'),
            );
            $this->db->insert('data_stock', $object);
            $this->fungsi->run_js('load_silent("wrh/aksesoris/detailbom/' . $this->input->post('id_sj') . '","#content")');
            $this->fungsi->message_box("BOM baru disimpan!", "success");
            $this->fungsi->catat($datapost_bom, "Menambah BOM data sbb:", true);
        }
    }

    public function getQtyRowGudang()
    {
        $this->fungsi->check_previleges('aksesoris');
        $field  = $this->input->post('field');
        $value  = $this->input->post('value');
        $editid = $this->input->post('id');
        // $id_fppp = $this->input->post('id_fppp');

        $id_item      = $this->db->get_where('data_stock', array('id' => $editid))->row()->id_item;
        $id_divisi    = $this->input->post('divisi');
        $id_gudang    = $this->input->post('gudang');
        $keranjang    = $this->input->post('keranjang');
        $qtyin        = $this->m_aksesoris->getQtyInDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $qtyout       = $this->m_aksesoris->getQtyOutDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $data['qty_gudang'] = $qtyin - $qtyout;

        $data['status'] = "berhasil";
        echo json_encode($data);
    }



    public function cetakSj($id)
    {
        $data = array(
            'id'     => $id,
            'header' => $this->m_aksesoris->getHeaderSendCetak($id)->row(),
            'detail' => $this->m_aksesoris->getDataDetailSendCetak($id)->result(),
        );
        // print_r($data['detail']);

        $this->load->view('wrh/aksesoris/v_aksesoris_cetak_sj', $data);
    }

    public function bon_manual()
    {
        $this->fungsi->check_previleges('aksesoris');
        $data['surat_jalan'] = $this->m_aksesoris->getSuratJalan(2, 2);
        $this->load->view('wrh/aksesoris/v_aksesoris_bon_list', $data);
    }

    public function bon_manual_add()
    {
        $this->fungsi->check_previleges('aksesoris');
        $data['no_fppp']        = $this->db->get_where('data_fppp', array('id_status' => 1));
        $data['no_surat_jalan'] = str_pad($this->m_aksesoris->getNoSuratJalan(), 3, '0', STR_PAD_LEFT) . '/SJ/AK/' . date('m') . '/' . date('Y');

        $this->load->view('wrh/aksesoris/v_aksesoris_bon_add', $data);
    }

    public function simpanSuratJalanBon()
    {
        $this->fungsi->check_previleges('aksesoris');
        $penerima          = $this->input->post('penerima');
        $no_surat_jalan    = $this->input->post('no_surat_jalan');
        $alamat_pengiriman = $this->input->post('alamat_pengiriman');
        $sopir             = $this->input->post('sopir');
        $no_kendaraan      = $this->input->post('no_kendaraan');
        $obj               = array(
            'no_surat_jalan'    => $no_surat_jalan,
            'penerima'          => $penerima,
            'alamat_pengiriman' => $alamat_pengiriman,
            'sopir'             => $sopir,
            'no_kendaraan'      => $no_kendaraan,
            'id_jenis_item'     => 2,
            'tipe'              => 2,
            'created'           => date('Y-m-d H:i:s'),
        );
        $this->db->insert('data_surat_jalan', $obj);
        $data['id'] = $this->db->insert_id();
        echo json_encode($data);
    }

    public function edit_bon_manual($id_sj = '')
    {
        $this->fungsi->check_previleges('aksesoris');
        $id_jenis_item = 2;

        $data['id_sj']             = $id_sj;
        $data['fppp']              = $this->db->get('data_fppp');
        $data['item']              = $this->m_aksesoris->getDataItem();
        $data['no_surat_jalan']    = $this->m_aksesoris->getRowSj($id_sj)->row()->no_surat_jalan;
        $data['penerima']          = $this->m_aksesoris->getRowSj($id_sj)->row()->penerima;
        $data['alamat_pengiriman'] = $this->m_aksesoris->getRowSj($id_sj)->row()->alamat_pengiriman;
        $data['sopir']             = $this->m_aksesoris->getRowSj($id_sj)->row()->sopir;
        $data['no_kendaraan']      = $this->m_aksesoris->getRowSj($id_sj)->row()->no_kendaraan;
        $data['divisi']            = $this->m_aksesoris->getDivisiBom($id_jenis_item);
        $data['gudang']            = $this->m_aksesoris->getGudangBom($id_jenis_item, 1);
        $data['keranjang']         = $this->m_aksesoris->getKeranjangBom($id_jenis_item);
        $data['list_sj']           = $this->m_aksesoris->getListItemBonManual($id_sj);
        $this->load->view('wrh/aksesoris/v_aksesoris_bon_item', $data);
    }

    public function getQtyRowGudangBon()
    {
        $id_item      = $this->input->post('item');
        $id_divisi    = $this->input->post('divisi');
        $id_gudang    = $this->input->post('gudang');
        $keranjang    = $this->input->post('keranjang');
        $qtyin        = $this->m_aksesoris->getQtyInDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $qtyout       = $this->m_aksesoris->getQtyOutDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $data['qty_gudang'] = $qtyin - $qtyout;

        $data['status'] = "berhasil";
        echo json_encode($data);
    }

    public function savebonmanual()
    {
        $this->fungsi->check_previleges('aksesoris');
        $id_item = $this->input->post('item');
        $id_divisi = $this->input->post('id_divisi');
        $id_gudang = $this->input->post('id_gudang');
        $keranjang = $this->input->post('keranjang');
        $datapost = array(
            'inout'          => 2,
            'id_jenis_item'  => 2,
            'id_surat_jalan' => $this->input->post('id_sj'),
            'id_fppp'        => $this->input->post('id_fppp'),
            'id_item'        => $this->input->post('item'),
            'id_divisi'      => $this->input->post('id_divisi'),
            'id_gudang'      => $this->input->post('id_gudang'),
            'keranjang'      => $this->input->post('keranjang'),
            'qty_out'        => $this->input->post('qty'),
            'produksi'       => $this->input->post('produksi'),
            'lapangan'       => $this->input->post('lapangan'),
            'created'        => date('Y-m-d H:i:s'),
        );
        $this->db->insert('data_stock', $datapost);
        $qtyin        = $this->m_aksesoris->getQtyInDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $qtyout       = $this->m_aksesoris->getQtyOutDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $data['qty_gudang'] = $qtyin - $qtyout;
        $this->m_aksesoris->updateDataCounter($id_item, $id_divisi, $id_gudang, $keranjang, $data['qty_gudang']);

        $this->fungsi->catat($datapost, "Menyimpan detail BON Manual sbb:", true);
        $data['msg'] = "BON Disimpan";
        echo json_encode($data);
    }

    public function deleteItemBonManual()
    {
        $this->fungsi->check_previleges('aksesoris');
        $id   = $this->input->post('id');

        $id_item      = $this->db->get_where('data_stock', array('id' => $id))->row()->id_item;
        $id_divisi    = $this->db->get_where('data_stock', array('id' => $id))->row()->id_divisi;
        $id_gudang    = $this->db->get_where('data_stock', array('id' => $id))->row()->id_gudang;
        $keranjang    = $this->db->get_where('data_stock', array('id' => $id))->row()->keranjang;
        $this->m_aksesoris->deleteItemBonManual($id);
        $qtyin        = $this->m_aksesoris->getQtyInDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $qtyout       = $this->m_aksesoris->getQtyOutDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $data['qty_gudang'] = $qtyin - $qtyout;
        $this->m_aksesoris->updateDataCounter($id_item, $id_divisi, $id_gudang, $keranjang, $data['qty_gudang']);

        $data = array('id' => $id,);
        $this->fungsi->catat($data, "Menghapus BON manual Detail dengan data sbb:", true);
        $respon = ['msg' => 'Data Berhasil Dihapus'];
        echo json_encode($respon);
    }

    public function finishdetailbon($id_sj)
    {
        $this->fungsi->check_previleges('aksesoris');
        // $this->m_aksesoris->finishdetailbom($id_sj);
        $datapost = array('id_sj' => $id_sj,);
        $this->fungsi->message_box("Fisnish BON Manual", "success");
        $this->fungsi->catat($datapost, "Finish BON Manual dengan id:", true);
        $this->bon_manual();
    }

    public function mutasi_stock_add($id = '')
    {
        $this->fungsi->check_previleges('aksesoris');
        $data['id_item'] = $id;
        $data['item']    = $this->m_aksesoris->getDataItem();
        $data['divisi']  = $this->db->get_where('master_divisi_stock', array('id_jenis_item' => 2));
        $data['gudang']  = $this->db->get_where('master_gudang', array('id_jenis_item' => 2));
        $this->load->view('wrh/aksesoris/v_aksesoris_mutasi_stock_add', $data);
    }

    public function optionGetGudangDivisi()
    {
        $this->fungsi->check_previleges('aksesoris');
        $id_item   = $this->input->post('item');
        $id_divisi = $this->input->post('divisi');
        $get_data  = $this->m_aksesoris->getGudangDivisi($id_item, $id_divisi);
        $data      = array();
        foreach ($get_data as $val) {
            $data[] = $val;
        }
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function optionGetKeranjangGudang()
    {
        $this->fungsi->check_previleges('aksesoris');
        $id_item   = $this->input->post('item');
        $id_divisi = $this->input->post('divisi');
        $id_gudang = $this->input->post('gudang');
        $get_data  = $this->m_aksesoris->getKeranjangGudang($id_item, $id_divisi, $id_gudang);
        $data      = array();
        foreach ($get_data as $val) {
            $data[] = $val;
        }
        echo json_encode($data, JSON_PRETTY_PRINT);
    }
    public function optionGetQtyKeranjang()
    {
        $this->fungsi->check_previleges('aksesoris');
        $id_item   = $this->input->post('item');
        $id_divisi = $this->input->post('divisi');
        $id_gudang = $this->input->post('gudang');
        $keranjang = $this->input->post('keranjang');
        $qtyin     = $this->m_aksesoris->getQtyInDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $qtyout    = $this->m_aksesoris->getQtyOutDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);

        $get_data = $qtyin - $qtyout;
        $data['qty']    = $get_data;
        echo json_encode($data);
    }

    public function simpanMutasi()
    {
        $this->fungsi->check_previleges('aksesoris');

        $id_item   = $this->input->post('item');
        $id_divisi = $this->input->post('divisi');
        $id_gudang = $this->input->post('gudang');
        $keranjang = $this->input->post('keranjang');

        $datapost_out = array(
            'id_item'    => $this->input->post('id_item'),
            'inout'      => 2,
            'mutasi'     => 1,
            'qty_out'    => $this->input->post('qty2'),
            'id_divisi'  => $this->input->post('id_divisi'),
            'id_gudang'  => $this->input->post('id_gudang'),
            'keranjang'  => $this->input->post('keranjang'),
            'keterangan' => 'Mutasi Out',
            'created'    => date('Y-m-d H:i:s'),
        );
        $this->m_aksesoris->insertstokin($datapost_out);
        $this->fungsi->catat($datapost_out, "Mutasi OUT sbb:", true);
        $qtyin        = $this->m_aksesoris->getQtyInDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $qtyout       = $this->m_aksesoris->getQtyOutDetailTabel($id_item, $id_divisi, $id_gudang, $keranjang);
        $data['qty_gudang'] = $qtyin - $qtyout;
        $this->m_aksesoris->updateDataCounter($id_item, $id_divisi, $id_gudang, $keranjang, $data['qty_gudang']);


        $datapost_in = array(
            'id_item'    => $this->input->post('id_item'),
            'mutasi'     => 1,
            'inout'      => 1,
            'qty_in'     => $this->input->post('qty2'),
            'id_divisi'  => $this->input->post('id_divisi2'),
            'id_gudang'  => $this->input->post('id_gudang2'),
            'keranjang'  => $this->input->post('keranjang2'),
            'keterangan' => 'Mutasi IN',
            'created'    => date('Y-m-d H:i:s'),
        );
        $this->m_aksesoris->insertstokin($datapost_in);
        $this->fungsi->catat($datapost_in, "Mutasi IN sbb:", true);

        $cekDataCounter = $this->m_aksesoris->getDataCounter($datapost_in['id_item'], $datapost_in['id_divisi'], $datapost_in['id_gudang'], $datapost_in['keranjang'])->num_rows();
        if ($cekDataCounter == 0) {
            $simpan = array(
                'id_jenis_item' => 2,
                'id_item'       => $datapost_in['id_item'],
                'id_divisi'     => $datapost_in['id_divisi'],
                'id_gudang'     => $datapost_in['id_gudang'],
                'keranjang'     => $datapost_in['keranjang'],
                'qty'           => $datapost_in['qty_in'],
                'created'       => date('Y-m-d H:i:s'),
            );
            $this->db->insert('data_counter', $simpan);
        } else {
            $cekQtyCounter = $this->m_aksesoris->getDataCounter($datapost_in['id_item'], $datapost_in['id_divisi'], $datapost_in['id_gudang'], $datapost_in['keranjang'])->row()->qty;
            $qty_jadi      = (int)$datapost_in['qty_in'] + (int)$cekQtyCounter;
            $this->m_aksesoris->updateDataCounter($datapost_in['id_item'], $datapost_in['id_divisi'], $datapost_in['id_gudang'], $datapost_in['keranjang'], $qty_jadi);
        }
        $data['pesan'] = "Berhasil";
        echo json_encode($data);
    }

    public function mutasi_stock_history($id = '')
    {
        $this->fungsi->check_previleges('aksesoris');
        $data['item']   = $this->db->get_where('master_item', array("id" => $id))->row();
        $data['mutasi'] = $this->m_aksesoris->getMutasiHistory($id);

        $this->load->view('wrh/aksesoris/v_aksesoris_mutasi_stock_history', $data);
    }
}

/* End of file aksesoris.php */
/* Location: ./application/controllers/wrh/aksesoris.php */