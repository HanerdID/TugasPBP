<?php
session_start();
require_once("../db_login.php");

if (isset($_GET['idloker'])) {
    $idloker = $_GET['idloker'];

    // Retrieve job listing information, including the company name
    $query = "SELECT loker.*, perusahaan.nama AS nama_perusahaan
              FROM loker
              JOIN perusahaan ON loker.idperusahaan = perusahaan.idperusahaan
              WHERE loker.idloker = '$idloker'";
    $result = $db->query($query);

    if (!$result) {
        die("Could not query the database: <br />" . $db->error . "<br>Query:" . $query);
    }

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
    } else {
        echo "Loker tidak ditemukan.";
    }

    // Retrieve job applicants at different stages

    // Tahap 1 (Administrasi)
    $queryTahap1 = "SELECT
                    apply_loker.noktp,
                    pencaker.nama,
                    pencaker.jenis_kelamin,
                    pencaker.email,
                    pencaker.no_telp,
                    pencaker.tgl_daftar
                    FROM apply_loker
                    JOIN pencaker ON apply_loker.noktp = pencaker.noktp
                    LEFT JOIN tahapan_apply ON apply_loker.idapply = tahapan_apply.idapply
                    WHERE apply_loker.idloker = $idloker AND tahapan_apply.idtahapan = 't1'";

    $resultTahap1 = $db->query($queryTahap1);

    // Tahap 2 (Wawancara)
    $queryTahap2 = "SELECT
                    apply_loker.noktp,
                    pencaker.nama,
                    pencaker.jenis_kelamin,
                    pencaker.email,
                    pencaker.no_telp,
                    pencaker.tgl_daftar
                    FROM apply_loker
                    JOIN pencaker ON apply_loker.noktp = pencaker.noktp
                    LEFT JOIN tahapan_apply ON apply_loker.idapply = tahapan_apply.idapply
                    WHERE apply_loker.idloker = $idloker AND tahapan_apply.idtahapan = 't2'";

    $resultTahap2 = $db->query($queryTahap2);

    // Tahap 3 (Lolos Wawancara)
    $queryTahap3 = "SELECT
                    apply_loker.noktp,
                    pencaker.nama,
                    pencaker.jenis_kelamin,
                    pencaker.email,
                    pencaker.no_telp,
                    pencaker.tgl_daftar
                    FROM apply_loker
                    JOIN pencaker ON apply_loker.noktp = pencaker.noktp
                    LEFT JOIN tahapan_apply ON apply_loker.idapply = tahapan_apply.idapply
                    WHERE apply_loker.idloker = $idloker AND tahapan_apply.idtahapan = 't3'";

    $resultTahap3 = $db->query($queryTahap3);

    if (!$resultTahap1 || !$resultTahap2 || !$resultTahap3) {
        die("Could not query the database.");
    }
?>

    <?php include('../header.html') ?>
    <div class="card mt-5" onload="updateStatus()">
        <div class="card-header text-center" style="font-size: 24px; font-weight: bold;">Detail Loker</div>
        <div class="card-body">
            <table class="table">
                <!-- Display job listing information -->
                <tr>
                    <th>Nama Perusahaan:</th>
                    <td><?php echo $row->nama_perusahaan; ?></td>
                </tr>
                <tr>
                    <th>Nama Pekerjaan:</th>
                    <td><?php echo $row->nama; ?></td>
                </tr>
                <tr>
                    <th>Usia Min:</th>
                    <td><?php echo $row->usia_min; ?></td>
                </tr>
                <tr>
                    <th>Usia Max:</th>
                    <td><?php echo $row->usia_max; ?></td>
                </tr>
                <tr>
                    <th>Gaji Min:</th>
                    <td><?php echo $row->gaji_min; ?></td>
                </tr>
                <tr>
                    <th>Gaji Max:</th>
                    <td><?php echo $row->gaji_max; ?></td>
                </tr>
                <tr>
                    <th>Tanggal Update:</th>
                    <td><?php echo $row->tgl_update; ?></td>
                </tr>
                <tr>
                    <th>Tanggal Aktif:</th>
                    <td><?php echo $row->tgl_aktif; ?></td>
                </tr>
                <tr>
                    <th>Tanggal Tutup:</th>
                    <td><?php echo $row->tgl_tutup; ?></td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td id="status"><?php echo $row->status;?></td>
                </tr>
                <tr>
                    <td>
                        <h5><br>Contact Person:</h5>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th>Nama:</th>
                    <td><?php echo $row->nama_cp; ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo $row->email_cp; ?></td>
                </tr>
                <tr>
                    <th>Nomor Telepon:</th>
                    <td><?php echo $row->no_telp_cp; ?></td>
                </tr>
            </table>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="../index_loker.php" class="btn btn-primary">Kembali</a>
            </div>
            <br>

            <!-- Tabel Pendaftar Tahap 1 (Administrasi) -->
            <table name="Tabel Pendaftar Tahap 1 (Administrasi)" class="table mt-3">
                <label for="">
                    <h4>Applier</h4>
                </label>
                <thead>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="../Seleksi/seleksi_administrasi.php?idloker=<?php echo $idloker; ?> " class="btn btn-success" onclick="selesaikanSeleksi()">Seleksi</a>
                    </div>
                    <tr>
                        <th>Nomor KTP</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Email</th>
                        <th>Nomor Telepon</th>
                        <th>Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($apply_row = $resultTahap1->fetch_object()) {
                        echo "<tr>";
                        echo "<td>{$apply_row->noktp}</td>";
                        echo "<td>{$apply_row->nama}</td>";
                        echo "<td>{$apply_row->jenis_kelamin}</td>";
                        echo "<td>{$apply_row->email}</td>";
                        echo "<td>{$apply_row->no_telp}</td>";
                        echo "<td>{$apply_row->tgl_daftar}</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <br>

            <!-- Tabel Pendaftar Tahap 2 (Wawancara) -->
            <table name="Tabel Pendaftar Tahap 2 (Wawancara)" class="table mt-3">
                <label for="">
                    <h4>Applier Lolos Tahap Administrasi</h4>
                </label>
                <thead>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="../Seleksi/seleksi_wawancara.php?idloker=<?php echo $idloker; ?>" class="btn btn-success" onclick="selesaikanSeleksi()">Seleksi</a>
                    </div>
                    <tr>
                        <th>Nomor KTP</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Email</th>
                        <th>Nomor Telepon</th>
                        <th>Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($apply_row = $resultTahap2->fetch_object()) {
                        echo "<tr>";
                        echo "<td>{$apply_row->noktp}</td>";
                        echo "<td>{$apply_row->nama}</td>";
                        echo "<td>{$apply_row->jenis_kelamin}</td>";
                        echo "<td>{$apply_row->email}</td>";
                        echo "<td>{$apply_row->no_telp}</td>";
                        echo "<td>{$apply_row->tgl_daftar}</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <br>

            <!-- Tabel Pendaftar Tahap 3 (Lolos Wawancara) -->
            <table name="Tabel Pendaftar Tahap 3 (Lolos Wawancara)" class="table mt-3">
                <label for="">
                    <h4>Applier Lolos Tahap Wawancara</h4>
                </label>
                <thead>
                    <tr>
                        <th>Nomor KTP</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Email</th>
                        <th>Nomor Telepon</th>
                        <th>Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($apply_row = $resultTahap3->fetch_object()) {
                        echo "<tr>";
                        echo "<td>{$apply_row->noktp}</td>";
                        echo "<td>{$apply_row->nama}</td>";
                        echo "<td>{$apply_row->jenis_kelamin}</td>";
                        echo "<td>{$apply_row->email}</td>";
                        echo "<td>{$apply_row->no_telp}</td>";
                        echo "<td>{$apply_row->tgl_daftar}</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        window.reload();
    </script>
    </body>

    </html>

<?php
    include('../footer.html');
} else {
    echo "Invalid Loker ID.";
}
?>