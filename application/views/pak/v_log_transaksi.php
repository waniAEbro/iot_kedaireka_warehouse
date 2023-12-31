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

<h1>Cetak History Transaksi</h1>

<table id="example" class="display nowrap" style="width:100%">
    <thead>
        <tr bgcolor="#ffe357">
            <th width="5%">No</th>
            <th>Awal Bulan</th>
            <th>Item Code</th>
            <th>ID Gudang</th>
            <th>Keranjang</th>
            <th>Qty_IN</th>
            <th>Qty Out</th>
            <th>Keterangan</th>
            <th>Tgl Aktual</th>
            <th>Tgl Sistem</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($list->result() as $row) {

        ?>
            <tr>
                <td align="center"><?= $i++ ?></td>
                <td><?= $row->awal_bulan ?></td>
                <td><?= $row->item_code ?></td>
                <td><?= $row->id_gudang ?></td>
                <td><?= $row->keranjang ?></td>
                <td><?= $row->qty_in ?></td>
                <td><?= $row->qty_out ?></td>
                <td><?= $row->keterangan ?></td>
                <td><?= $row->aktual ?></td>
                <td><?= $row->created ?></td>
            </tr>
        <?php
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