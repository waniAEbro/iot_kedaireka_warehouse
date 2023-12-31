<link href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" media="all" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css" media="all" rel="stylesheet" type="text/css" />
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>

<h1>Cetak Monitoring <?= $jenis_barang ?> HRB</h1>
<table id="example" class="display nowrap" style="width:100%">
    <thead>
        <tr bgcolor="#ffe357">
            <th width="5%">No</th>
            <th>Item Code</th>
            <th>Section ATA</th>
            <th>Section Allure</th>
            <th>Temper</th>
            <th>Kode Warna</th>
            <th>Ukuran</th>

            <th>Deskripsi Warna</th>
            <th>Satuan</th>
            <th>Lead Time</th>
            <th>Gudang</th>
            <th>Keranjang</th>
            <th>Stock Awal Bulan</th>
            <th>Total In</th>
            <th>Total Out</th>
            <th>Qty out proses</th>
            <th>Rata-rata</th>
            <th>Qty</th>
            <?php if (from_session('id') == 2) { ?>
                <th>Qty Counter</th>
                <th>beda</th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($aksesoris->result() as $row) {


            $stock_awal_bulan_now = @$s_awal_bulan[$row->id_item][$row->id_gudang][$row->keranjang];
            // $total_in_lalu = @$s_total_in_lalu[$row->id_item][$row->id_gudang][$row->keranjang];
            // $total_out_lalu = @$s_total_out_lalu[$row->id_item][$row->id_gudang][$row->keranjang];
            $stock_awal_bulan = $stock_awal_bulan_now;
            $total_in = @$s_total_in[$row->id_item][$row->id_gudang][$row->keranjang];
            $total_out = @$s_total_out[$row->id_item][$row->id_gudang][$row->keranjang];

            $qty_out_proses = @$s_total_out_proses[$row->id_item][$row->id_gudang][$row->keranjang];

            $total_akhir = $stock_awal_bulan + $total_in - $total_out;

            // if ($total_akhir < 0) {
            //     $this->db->where('id_item', $row->id_item);
            //     $this->db->where('id_gudang', $row->id_gudang);
            //     $this->db->where('keranjang', $row->keranjang);
            //     $this->db->where('awal_bulan', 1);
            //     $this->db->where('DATE_FORMAT(created,"%Y")', '2022');
            //     $this->db->where('DATE_FORMAT(created,"%m")', '09');
            //     $qtyx = $this->db->get('data_stock_10092022')->row()->qty_in;

            //     $object = array('qty_in' => $qtyx,);
            //     $this->db->where('id_item', $row->id_item);
            //     $this->db->where('id_gudang', $row->id_gudang);
            //     $this->db->where('keranjang', $row->keranjang);
            //     $this->db->where('awal_bulan', 1);
            //     $this->db->where('DATE_FORMAT(created,"%Y")', '2022');
            //     $this->db->where('DATE_FORMAT(created,"%m")', '09');
            //     $this->db->update('data_stock', $object);

            // }else{
            //     $qtyx = 'ok';
            // }



            if ($total_akhir != $row->qty) {
                $beda = 1;
            } else {
                $beda = 0;
            }
            if ($beda == 1) {
                $this->m_aluminium->updateDataCounter($row->id_item,  $row->id_gudang, $row->keranjang, $total_akhir);
            }

            if($is_update!=''){
                $this->m_aksesoris->ceksemuadanupdate($row->id_item, $row->id_gudang, $row->keranjang, $is_update);
                
            }

            if ($total_akhir > 0 || $stock_awal_bulan > 0) {
        ?>
                <tr>
                    <td align="center"><?= $i++ ?></td>
                    <td><?= $row->item_code ?></td>
                    <td><?= $row->section_ata ?></td>
                    <td><?= $row->section_allure ?></td>
                    <td><?= $row->temper ?></td>
                    <td><?= $row->kode_warna ?></td>
                    <td><?= $row->ukuran ?></td>

                    <td><?= $row->warna ?></td>
                    <td><?= $row->satuan ?></td>
                    <td><?= $row->lead_time ?></td>
                    <td><?= $row->gudang ?></td>
                    <td><?= $row->keranjang ?></td>
                    <td><?= $stock_awal_bulan ?></td>
                    <td><?= $total_in ?></td>
                    <td><?= $total_out ?></td>
                    <td align="center"><?= $qty_out_proses ?></td>
                    <td><?= $row->rata_pemakaian ?></td>
                    <td align="center"><?= $total_akhir ?></td>
                    <?php if (from_session('id') == 2) { ?>
                        <td align="center"><?= $row->qty ?></td>
                        <td align="center"><?= $beda ?></td>
                    <?php } ?>
                </tr>
        <?php }
        } ?>
    </tbody>
</table>


<script>
    $(document).ready(function() {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>