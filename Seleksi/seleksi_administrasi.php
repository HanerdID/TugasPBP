<?php include('../header.html') ?>

<?php
session_start();
require_once("../db_login.php");

if (isset($_GET['idloker'])) {
    $idloker = $_GET['idloker']; // Mengambil ID Loker dari parameter URL

    // Query untuk mengambil data applier dengan tahapan "t1" (administrasi)
    $query = "SELECT
                        apply_loker.noktp,
                        pencaker.nama,
                        pencaker.jenis_kelamin,
                        pencaker.email,
                        pencaker.no_telp,
                        pencaker.tgl_daftar
                        FROM apply_loker
                        JOIN pencaker ON apply_loker.noktp = pencaker.noktp
                        LEFT JOIN tahapan_apply ON apply_loker.idapply = tahapan_apply.idapply
                        WHERE apply_loker.idloker = '$idloker' AND tahapan_apply.idtahapan = 't1'";

    $result = $db->query($query);

    if (!$result) {
        die("Could not query the database: <br />" . $db->error . "<br>Query:" . $query);
    }

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
    
    // Determine the status of the job listing
    $status_loker = "";
    if ($resultTahap1->num_rows >= 0 && $resultTahap2->num_rows == 0 && $resultTahap3->num_rows == 0) {
        $status_loker = "Aktif";
    }
    if ($resultTahap2->num_rows > 0) {
        $status_loker = "Proses Seleksi";
    }
    if ($resultTahap3->num_rows > 0 && $resultTahap2->num_rows == 0) {
        $status_loker = "Ditutup";
    }

    // Update the job listing status in the database
    $updateStatusQuery = "UPDATE loker SET status = '$status_loker', tgl_update = NOW() WHERE idloker = $idloker";
    $db->query($updateStatusQuery);
?>

    <div class="card mt-5">
        <div class="card-header">
            <h1 class="mb-4">Seleksi Tahap Administrasi</h1>
        </div>
        <div class="card-body">
            <table class="table mt-3">
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
                    while ($row = $result->fetch_object()) {
                        echo "<tr>";
                        echo "<td>{$row->noktp}</td>";
                        echo '<td><a class="btn btn-link" href="../view_applier.php?noktp=' . $row->noktp . '" role="button">' . $row->nama . '</a></td>';
                        echo "<td>{$row->jenis_kelamin}</td>";
                        echo "<td>{$row->email}</td>";
                        echo "<td>{$row->no_telp}</td>";
                        echo "<td>{$row->tgl_daftar}</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="../CRUD_loker/view_loker.php?idloker=<?php echo $idloker; ?>" class="btn btn-primary" onclick="selesaikanSeleksi()">Selesaikan Seleksi</a>
            </div>
        </div>
    </div>


    <?php include('../footer.html'); ?>
<?php
} else {
    echo "Invalid Loker ID.";
}
?>