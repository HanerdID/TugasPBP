<?php
session_start();
require_once("../db_login.php");

if (isset($_GET['idloker'])) {
    $idloker = $_GET['idloker'];

    // Retrieve job applicants who passed the administrative stage
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

    if (!$resultTahap2) {
        die("Could not query the database.");
    }
?>

    <?php include('../header.html') ?>
    <div class="card mt-5">
        <div class="card-header text-center" style="font-size: 24px; font-weight: bold;">Seleksi Wawancara</div>
        <div class="card-body">
            <form method="post">
                <table class="table">
                    <tr>
                        <th>Nomor KTP</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Email</th>
                        <th>Nomor Telepon</th>
                        <th>Tanggal Daftar</th>
                        <th>Seleksi Wawancara</th>
                    </tr>
                    <?php
                    while ($apply_row = $resultTahap2->fetch_object()) {
                        echo "<tr>";
                        echo "<td>{$apply_row->noktp}</td>";
                        echo "<td>{$apply_row->nama}</td>";
                        echo "<td>{$apply_row->jenis_kelamin}</td>";
                        echo "<td>{$apply_row->email}</td>";
                        echo "<td>{$apply_row->no_telp}</td>";
                        echo "<td>{$apply_row->tgl_daftar}</td>";
                        echo "<td>
                            <button type='submit' name='seleksi[{$apply_row->noktp}]' value='lolos' class='btn btn-success'>Lolos Wawancara</button>
                            <button type='submit' name='seleksi[{$apply_row->noktp}]' value='tidak_lolos' class='btn btn-danger'>Tidak Lolos</button>
                        </td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="../CRUD_loker/view_loker.php?idloker=<?php echo $idloker; ?>" class="btn btn-primary">Selesaikan Seleksi</a>
                </div>
                <input type="hidden" name="idloker" value="<?php echo $idloker; ?>">
            </form>
        </div>
    </div>
    </body>

    </html>

<?php
    include('../footer.html');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $seleksi = $_POST['seleksi'];
        $idloker = $_POST['idloker'];

        foreach ($seleksi as $noktp => $status) {
            // Update the status of the applier (lolos or tidak lolos) in tahapan_apply
            if ($status == 'lolos') {
                $updateQuery = "UPDATE tahapan_apply
                                SET idtahapan = '" . ($status == 'lolos' ? 't3' : 'tidak_lolos') . "'
                                WHERE idapply = (SELECT idapply FROM apply_loker WHERE noktp = '$noktp' AND idloker = $idloker)";
                $db->query($updateQuery);
            }
            // If not lolos, remove the applier from apply_loker
            elseif ($status == 'tidak_lolos') {
                $deleteQuery = "DELETE FROM tahapan_apply WHERE idapply = (SELECT idapply FROM apply_loker WHERE noktp = '$noktp')";
                $db->query($deleteQuery);
            }
        }

        // Redirect back to the same page after processing the form
        header("Location: seleksi_wawancara.php?idloker=$idloker");
    }
} else {
    echo "Invalid Loker ID.";
}
?>