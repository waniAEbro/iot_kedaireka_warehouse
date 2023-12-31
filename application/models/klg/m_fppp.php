<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_fppp extends CI_Model
{

	public function getData($param)
	{
		$this->db->join('master_divisi md', 'md.id = df.id_divisi', 'left');
		$this->db->join('master_kaca mk', 'mk.id = df.id_kaca', 'left');
		$this->db->join('master_pengiriman mp', 'mp.id = df.id_pengiriman', 'left');
		$this->db->join('master_metode_pengiriman mpp', 'mpp.id = df.id_metode_pengiriman', 'left');
		$this->db->join('master_warna mwa', 'mwa.id = df.id_warna', 'left');
		$this->db->join('master_status ms', 'ms.id = df.id_status', 'left');

		$this->db->where('df.id_divisi', $param);
		$this->db->where('df.is_memo', 1);
		$this->db->where('DATE(df.tgl_pembuatan) >=', $this->session->userdata('tgl_awal'));
		$this->db->where('DATE(df.tgl_pembuatan) <=', $this->session->userdata('tgl_akhir'));
		$this->db->order_by('df.id', 'desc');

		$this->db->select('df.*,md.divisi,mk.kaca,mp.pengiriman,metode_pengiriman,mwa.warna,ms.status');

		return $this->db->get('data_fppp df');
	}

	public function getDataMemo($param)
	{
		$this->db->join('master_divisi md', 'md.id = df.id_divisi', 'left');
		$this->db->join('master_kaca mk', 'mk.id = df.id_kaca', 'left');
		$this->db->join('master_pengiriman mp', 'mp.id = df.id_pengiriman', 'left');
		$this->db->join('master_metode_pengiriman mpp', 'mpp.id = df.id_metode_pengiriman', 'left');
		$this->db->join('master_warna mwa', 'mwa.id = df.id_warna', 'left');
		$this->db->join('master_status ms', 'ms.id = df.id_status', 'left');

		$this->db->where('df.id_divisi', $param);
		$this->db->where('df.is_memo', 2);
		$this->db->where('DATE(df.tgl_pembuatan) >=', $this->session->userdata('tgl_awal'));
		$this->db->where('DATE(df.tgl_pembuatan) <=', $this->session->userdata('tgl_akhir'));
		$this->db->order_by('df.id', 'desc');

		$this->db->select('df.*,md.divisi,mk.kaca,mp.pengiriman,metode_pengiriman,mwa.warna,ms.status');

		return $this->db->get('data_fppp df');
	}

	public function getDataMockup($param)
	{
		$this->db->join('master_divisi md', 'md.id = df.id_divisi', 'left');
		$this->db->join('master_kaca mk', 'mk.id = df.id_kaca', 'left');
		$this->db->join('master_pengiriman mp', 'mp.id = df.id_pengiriman', 'left');
		$this->db->join('master_metode_pengiriman mpp', 'mpp.id = df.id_metode_pengiriman', 'left');
		$this->db->join('master_warna mwa', 'mwa.id = df.id_warna', 'left');
		$this->db->join('master_status ms', 'ms.id = df.id_status', 'left');

		$this->db->where('df.id_divisi', $param);
		$this->db->where('df.is_memo', 3);
		$this->db->where('DATE(df.tgl_pembuatan) >=', $this->session->userdata('tgl_awal'));
		$this->db->where('DATE(df.tgl_pembuatan) <=', $this->session->userdata('tgl_akhir'));
		$this->db->order_by('df.id', 'desc');

		$this->db->select('df.*,md.divisi,mk.kaca,mp.pengiriman,metode_pengiriman,mwa.warna,ms.status');

		return $this->db->get('data_fppp df');
	}

	public function getNumRow($id)
	{
		$this->db->where('id_fppp', $id);
		return $this->db->get('data_surat_jalan')->num_rows();
	}


	public function editDeadlineWorkshop($field = '', $value = '', $editid = '')
	{
		$this->db->query("UPDATE data_fppp SET " . $field . "='" . $value . "' WHERE id=" . $editid);
	}

	public function insertfppp($value = '')
	{
		$this->db->insert('data_fppp', $value);
	}

	public function insertfpppDetail($value = '')
	{
		$this->db->insert('data_fppp_detail', $value);
	}

	public function getDataDetailTabel($value = '')
	{
		$this->db->join('master_brand mb', 'mb.id = did.id_brand', 'left');
		$this->db->join('master_barang mi', 'mi.id = did.id_item', 'left');
		$this->db->join('master_warna mwa', 'mwa.id = did.finish_coating', 'left');
		$this->db->select('did.*,mb.brand,mi.barang as item,mwa.warna');

		$this->db->where('did.id_fppp', $value);
		return $this->db->get('data_fppp_detail did')->result();
	}
	public function getNoFppp($id_divisi = '')
	{
		$year  = date('Y');
		$month = date('m');
		$this->db->where('DATE_FORMAT(created,"%Y")', $year);
		$this->db->where('DATE_FORMAT(created,"%m")', $month);
		$this->db->where('id_divisi', $id_divisi);
		$this->db->where('is_memo', 1);
		$this->db->like('no_fppp', '/' . $month . '/' . $year);

		$this->db->order_by('id', 'desc');
		$this->db->limit(1);
		$hasil = $this->db->get('data_fppp');
		if ($hasil->num_rows() > 0) {

			$string = $hasil->row()->no_fppp;
			$arr    = explode("/", $string, 2);
			$first  = $arr[0];
			$no     = $first + 1;
			return $no;
		} else {
			return '1';
		}
	}

	public function cekIdTerakhir($id_divisi = '')
	{
		$year  = date('Y');
		$month = date('m');
		// $this->db->where('DATE_FORMAT(created,"%Y")', $year);
		// $this->db->where('DATE_FORMAT(created,"%m")', $month);
		$this->db->where('id_divisi', $id_divisi);
		$this->db->where('is_memo', 1);
		// $this->db->like('no_fppp', '/' . $month . '/' . $year);

		$this->db->order_by('id', 'desc');
		$this->db->limit(1);
		$hasil = $this->db->get('data_fppp');
		return $hasil;
	}

	public function getNoMemo($id_divisi = '')
	{
		$year  = date('Y');
		$month = date('m');
		$this->db->where('DATE_FORMAT(created,"%Y")', $year);
		$this->db->where('DATE_FORMAT(created,"%m")', $month);
		$this->db->where('id_divisi', $id_divisi);
		$this->db->where('is_memo', 2);
		$this->db->like('no_fppp', '/' . $month . '/' . $year);

		$this->db->order_by('id', 'desc');
		$this->db->limit(1);
		$hasil = $this->db->get('data_fppp');
		if ($hasil->num_rows() > 0) {

			$string = $hasil->row()->no_fppp;
			$arr    = explode("/", $string, 2);
			$first  = $arr[0];
			$no     = $first + 1;
			return $no;
		} else {
			return '1';
		}
	}

	public function getNoMockup($id_divisi = '')
	{
		$year  = date('Y');
		$month = date('m');
		$this->db->where('DATE_FORMAT(created,"%Y")', $year);
		$this->db->where('DATE_FORMAT(created,"%m")', $month);
		$this->db->where('id_divisi', $id_divisi);
		$this->db->where('is_memo', 3);
		$this->db->order_by('id', 'desc');
		$this->db->limit(1);
		$hasil = $this->db->get('data_fppp');
		if ($hasil->num_rows() > 0) {

			$string = $hasil->row()->no_fppp;
			$arr    = explode("/", $string, 2);
			$first  = $arr[0];
			$no     = $first + 1;
			return $no;
		} else {
			return '1';
		}
	}

	public function getIdFppp($id)
	{
		$this->db->where('id', $id);
		return $this->db->get('data_fppp_detail')->row()->id_fppp;
	}

	public function updateFppp($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update('data_fppp', $data);
	}

	public function updateDetail($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update('data_fppp_detail', $data);
	}

	public function getJmlBaris($id_fppp)
	{
		$this->db->where('id_fppp', $id_fppp);
		return $this->db->get('data_fppp_detail')->num_rows();
	}

	public function getJmlStsPengiriman($id_fppp)
	{
		$this->db->where('id_fppp', $id_fppp);
		$this->db->where('pengiriman_sts', 1);

		return $this->db->get('data_fppp_detail')->num_rows();
	}

	public function getJmlStsPasang($id_fppp)
	{
		$this->db->where('id_fppp', $id_fppp);
		$this->db->where('pasang_sts', 1);

		return $this->db->get('data_fppp_detail')->num_rows();
	}

	public function getJmlStsBST($id_fppp)
	{
		$this->db->where('id_fppp', $id_fppp);
		$this->db->where('bst_sts', 1);

		return $this->db->get('data_fppp_detail')->num_rows();
	}

	public function updatews($id, $jml_baris, $jml_pengiriman, $id_fppp)
	{
		if ($jml_pengiriman == 0) {
			$sts = 1;
			$sts_txt = 'PROSES';
		} else if ($jml_pengiriman == $jml_baris) {
			$sts = 3;
			$sts_txt = 'LUNAS';
		} else {
			$sts = 2;
			$sts_txt = 'PARSIAL';
		}
		$obj = array('ws_update' => $sts,);
		$this->db->where('id', $id_fppp);
		$this->db->update('data_fppp', $obj);
		return $sts_txt;
	}

	public function updatesite($id, $jml_baris, $jml_pengiriman, $id_fppp)
	{
		if ($jml_pengiriman == 0) {
			$sts = 1;
			$sts_txt = 'PROSES';
		} else if ($jml_pengiriman == $jml_baris) {
			$sts = 3;
			$sts_txt = 'LUNAS';
		} else {
			$sts = 2;
			$sts_txt = 'PARSIAL';
		}
		$obj = array('site_update' => $sts,);
		$this->db->where('id', $id_fppp);
		$this->db->update('data_fppp', $obj);
		return $sts_txt;
	}

	public function updatesitebst($id, $jml_baris, $jml_pengiriman, $id_fppp)
	{
		if ($jml_pengiriman == 0) {
			$sts = 1;
			$sts_txt = 'PROSES';
		} else if ($jml_pengiriman == $jml_baris) {
			$sts = 3;
			$sts_txt = 'LUNAS';
		} else {
			$sts = 2;
			$sts_txt = 'PARSIAL';
		}
		$obj = array('site_update_bst' => $sts,);
		$this->db->where('id', $id_fppp);
		$this->db->update('data_fppp', $obj);
		return $sts_txt;
	}

	function getTotalHold()
	{
		$res = $this->db->get('data_fppp_detail');
		$data = array();
		$nilai = 0;
		foreach ($res->result() as $key) {
			if (isset($data[$key->id_fppp])) {
				$nilai = $data[$key->id_fppp];
			} else {
				$nilai = 0;
			}
			$data[$key->id_fppp] = $key->hold_sts + $nilai;
		}
		return $data;
	}

	public function bom_aluminium($id_fppp)
	{
		$this->db->join('data_fppp df', 'df.id = db.id_fppp', 'left');
		$this->db->join('master_item mi', 'mi.id = db.id_item', 'left');
		$this->db->join('master_warna mwa', 'mwa.kode = mi.kode_warna', 'left');
		$this->db->join('master_brand mb', 'mb.id = db.id_multi_brand', 'left');

		$this->db->where('db.id_jenis_item', 1);
		$this->db->where('db.is_bom', 1);
		$this->db->where('db.ke_mf', 0);

		$this->db->where('db.id_fppp', $id_fppp);
		return $this->db->get('data_stock db');
	}

	public function bom_aksesoris($id_fppp)
	{
		$this->db->join('data_fppp df', 'df.id = db.id_fppp', 'left');
		$this->db->join('master_item mi', 'mi.id = db.id_item', 'left');
		$this->db->join('master_brand mb', 'mb.id = db.id_multi_brand', 'left');
		$this->db->where('db.id_jenis_item', 2);
		$this->db->where('db.is_bom', 1);
		$this->db->where('db.ke_mf', 0);

		$this->db->where('db.id_fppp', $id_fppp);
		return $this->db->get('data_stock db');
	}

	public function bom_lembaran($id_fppp)
	{
		$this->db->join('data_fppp df', 'df.id = db.id_fppp', 'left');
		$this->db->join('master_item mi', 'mi.id = db.id_item', 'left');
		$this->db->join('master_brand mb', 'mb.id = db.id_multi_brand', 'left');
		$this->db->where('db.id_jenis_item', 3);
		$this->db->where('db.is_bom', 1);
		$this->db->where('db.ke_mf', 0);

		$this->db->where('db.id_fppp', $id_fppp);
		return $this->db->get('data_stock db');
	}

	public function getMasterAluminium($section_ata = '', $section_allure = '', $temper = '', $kode_warna = '', $ukuran = '')
	{
		if ($section_ata != '' && $section_allure == '') {
			$this->db->where('section_ata', $section_ata);
		} elseif ($section_ata == '' && $section_allure != '') {
			$this->db->where('section_allure', $section_allure);
		} else {
			$this->db->where('section_ata', $section_ata);
			$this->db->where('section_allure', $section_allure);
		}
		$this->db->where('temper', $temper);
		$this->db->where('kode_warna', $kode_warna);
		$this->db->where('ukuran', $ukuran);
		return $this->db->get('master_item');
	}

	public function cekmasteraluminium($section_ata = '', $section_allure = '', $temper = '', $kode_warna = '', $ukuran = '')
	{
		$this->db->where('section_ata', $section_ata);
		$this->db->where('section_allure', $section_allure);
		$this->db->where('temper', $temper);
		$this->db->where('kode_warna', $kode_warna);
		$this->db->where('ukuran', $ukuran);
		return $this->db->get('master_item');
	}

	public function cekItemCodeAluminium($item_code)
	{
		$this->db->where('id_jenis_item', 1);
		$this->db->where('item_code', $item_code);
		return $this->db->get('master_item');
	}

	public function cekItemCode($id_jenis_item,$item_code)
	{
		$this->db->where('id_jenis_item', $id_jenis_item);
		$this->db->where('item_code', $item_code);
		return $this->db->get('master_item');
	}

	public function updateCounter($id_jenis_item,$item, $divisi, $gudang, $keranjang, $qty)
    {
        $object        = array('qty' => $qty,);
        $this->db->where('id_jenis_item', $id_jenis_item);
        $this->db->where('id_item', $item);
        $this->db->where('id_divisi', $divisi);
        $this->db->where('id_gudang', $gudang);
        $this->db->where('keranjang', $keranjang);
        $this->db->update('data_counter', $object);
    }

	public function cekCounter($id_jenis_item,$item, $divisi, $gudang, $keranjang)
    {
        $this->db->where('id_jenis_item', $id_jenis_item);
        $this->db->where('id_item', $item);
        $this->db->where('id_divisi', $divisi);
        $this->db->where('id_gudang', $gudang);
        $this->db->where('keranjang', $keranjang);
        return $this->db->get('data_counter');
    }

	public function cekCounterAlu($id_jenis_item,$item, $gudang, $keranjang)
    {
        $this->db->where('id_jenis_item', $id_jenis_item);
        $this->db->where('id_item', $item);
        $this->db->where('id_gudang', $gudang);
        $this->db->where('keranjang', $keranjang);
        return $this->db->get('data_counter');
    }

	public function updateCounterAlu($id_jenis_item,$item, $gudang, $keranjang, $qty)
    {
        $object        = array('qty' => $qty,);
        $this->db->where('id_jenis_item', $id_jenis_item);
        $this->db->where('id_item', $item);
        $this->db->where('id_gudang', $gudang);
        $this->db->where('keranjang', $keranjang);
        $this->db->update('data_counter', $object);
    }

	public function getMasterAksesoris($item_code = '')
	{
		$this->db->where('item_code', $item_code);
		return $this->db->get('master_item');
	}

	public function getMasterLembaran($nama_barang = '')
	{
		$this->db->where('nama_barang', $nama_barang);
		return $this->db->get('master_item');
	}

	public function simpanItem($object)
	{
		$this->db->insert('master_item', $object);
	}

	public function getjmltglkosong($id_fppp)
	{
		$this->db->where('id_fppp', $id_fppp);
		$res = $this->db->get('data_fppp_detail');
		$pa = 0;
		$qc = 0;
		$p = 0;
		foreach ($res->result() as $key) {
			$j_pa = ($key->produksi_aluminium == '') ? 1 : 0;
			$pa = $pa + $j_pa;
			$j_qc = ($key->qc_cek == '') ? 1 : 0;
			$qc = $qc + $j_qc;
			$j_p = ($key->pengiriman == '') ? 1 : 0;
			$p = $p + $j_p;
		}

		$total = $pa + $qc + $p;
		if ($total == 0) {
			$obj = array('ws_update' => "LUNAS",);
			$this->db->where('id', $id_fppp);
			$this->db->update('data_fppp', $obj);
		} else {
			$obj = array('ws_update' => "PARSIAL",);
			$this->db->where('id', $id_fppp);
			$this->db->update('data_fppp', $obj);
		}

		return $total;
	}

	public function getjml_pasang_bst($id_fppp)
	{
		$this->db->where('id_fppp', $id_fppp);
		$res = $this->db->get('data_fppp_detail');
		$pa = 0;
		$qc = 0;
		foreach ($res->result() as $key) {
			$j_pa = ($key->pasang == '') ? 1 : 0;
			$pa = $pa + $j_pa;
			$j_qc = ($key->bst == '') ? 1 : 0;
			$qc = $qc + $j_qc;
		}

		$total = $pa + $qc;
		if ($total == 0) {
			$obj = array('id_status' => 3,);
			$this->db->where('id', $id_fppp);
			$this->db->update('data_fppp', $obj);
		} else {
			$obj = array('id_status' => 2,);
			$this->db->where('id', $id_fppp);
			$this->db->update('data_fppp', $obj);
		}

		return $total;
	}

	public function getRowNamaDivisi($id)
	{
		$this->db->where('id', $id);
		return $this->db->get('master_divisi')->row();
	}

	public function deleteDetailItem($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('data_fppp_detail');
	}

	public function getDetailFppp($value = '')
	{
		$this->db->where('id', $value);
		return $this->db->get('data_fppp_detail')->row();
	}

	public function updateInvoiceFppp($data = '', $id = '')
	{
		$this->db->where('id', $id);
		$this->db->update('data_fppp_detail', $data);
	}

	public function getRowFppp($id)
	{
		$this->db->where('df.id', $id);
		$this->db->join('master_logo_kaca mlk', 'mlk.id = df.id_logo_kaca', 'left');
		$this->db->join('master_kaca mk', 'mk.id = df.id_kaca', 'left');
		$this->db->join('master_warna mwa', 'mwa.id = df.id_warna', 'left');
		$this->db->join('master_warna mwal', 'mwal.id = df.id_warna_lainya', 'left');
		$this->db->join('master_metode_pengiriman mp', 'mp.id = df.id_metode_pengiriman', 'left');
		$this->db->join('master_penggunaan_sealant mps', 'mps.id = df.id_penggunaan_sealant', 'left');
		$this->db->join('master_pengiriman mpe', 'mpe.id = df.id_pengiriman', 'left');
		$this->db->join('master_penggunaan_peti mpp', 'mpp.id = df.id_penggunaan_peti', 'left');

		$this->db->select('df.*,mps.penggunaan_sealant,mpp.penggunaan_peti,mpe.pengiriman,mp.metode_pengiriman,mlk.logo_kaca,mk.kaca,mwa.warna,mwal.warna as warna_lainya');

		return $this->db->get('data_fppp df');
	}

	public function getRowFpppDetail($id)
	{
		$this->db->join('master_brand mb', 'mb.id = dfd.id_brand', 'left');
		$this->db->join('master_barang mbr', 'mbr.id = dfd.id_item', 'left');
		$this->db->join('master_warna mwa', 'mwa.id = dfd.finish_coating', 'left');

		$this->db->where('dfd.id_fppp', $id);
		$this->db->select('dfd.*,mb.brand,mbr.barang,mwa.warna');

		return $this->db->get('data_fppp_detail dfd')->result();
	}

	public function updateFpppDetail($datapost, $id)
	{
		$this->db->where('id', $id);
		$this->db->update('data_fppp_detail', $datapost);
	}

	public function cekSudahAdaKeluar($id_fppp)
	{
		$this->db->where('id_fppp', $id_fppp);
		$this->db->where('is_bom', 1);
		$this->db->where('inout', 2);
		return $this->db->get('data_stock')->num_rows();
	}

	public function deleteBomSebelum($id_fppp, $jenis_item)
	{
		$this->db->where('id_fppp', $id_fppp);
		$this->db->where('id_jenis_item', $jenis_item);
		$this->db->where('is_bom', 1);
		$this->db->where('inout', 0);
		$this->db->delete('data_stock');
	}

	public function delete_temp($id_fppp, $jenis_item)
	{
		$this->db->where('id_fppp', $id_fppp);
		$this->db->where('id_jenis_item', $jenis_item);
		$this->db->where('id_penginput', from_session('id'));
		$this->db->delete('data_temp');
	}

	public function getTemp($id_fppp, $jenis_item)
	{
		$this->db->where('id_fppp', $id_fppp);
		$this->db->where('id_jenis_item', $jenis_item);
		$this->db->where('id_penginput', from_session('id'));
		return $this->db->get('data_temp')->result();
	}

	public function updateStatusUploadBom($id_fppp, $object)
	{
		$this->db->where('id', $id_fppp);
		$this->db->update('data_fppp', $object);
	}

	public function getCounter($id_item, $id_divisi, $id_gudang, $keranjang)
	{
		$this->db->where('id_item', $id_item);
		$this->db->where('id_divisi', $id_divisi);
		$this->db->where('id_gudang', $id_gudang);
		$this->db->where('keranjang', $keranjang);

		return $this->db->get('data_counter');
	}

	public function updateDataCounter($item, $divisi, $gudang, $keranjang, $qty)
	{
		$object = array('qty' => $qty,);
		$this->db->where('id_item', $item);
		$this->db->where('id_divisi', $divisi);
		$this->db->where('id_gudang', $gudang);
		$this->db->where('keranjang', $keranjang);
		$this->db->update('data_counter', $object);
	}

	public function getBomTemp()
	{
		$this->db->join('master_jenis_item mji', 'mji.id = dt.id_jenis_item', 'left');
		$this->db->join('data_fppp df', 'df.id = dt.id_fppp', 'left');
		$this->db->select('dt.*,mji.jenis_item,df.no_fppp,df.nama_proyek');
		$this->db->order_by('dt.id', 'desc');

		return $this->db->get('data_temp dt');
	}

	public function cekKodeProyek($kode_proyek)
	{
		$this->db->where('kode_proyek', $kode_proyek);
		return $this->db->get('data_fppp_finance');
	}

	public function getKodeProyek($kode_proyek)
	{
		$this->db->where('kode_proyek', $kode_proyek);
		return $this->db->get('master_proyek')->row();
	}

	public function getNumSuratJalan($id_fppp)
	{
		$this->db->where('id_fppp', $id_fppp);
		return $this->db->get('data_surat_jalan')->num_rows();
	}
}

/* End of file m_fppp.php */
/* Location: ./application/models/klg/m_fppp.php */