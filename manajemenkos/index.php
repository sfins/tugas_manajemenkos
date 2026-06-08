<?php
include 'koneksi.php';

// Proses data kamar
if (isset($_POST['tambah_kamar'])) {
    $no_kamar = $_POST['no_kamar']; $tipe = $_POST['tipe']; $harga = $_POST['harga']; $status = $_POST['status'];
    mysqli_query($koneksi, "INSERT INTO kamar (no_kamar, tipe, harga, status) VALUES ('$no_kamar', '$tipe', '$harga', '$status')");
    header("Location: index.php?tab=kamar");
}
if (isset($_POST['ubah_kamar'])) {
    $id = $_POST['idkamar']; $tipe = $_POST['tipe']; $harga = $_POST['harga']; $status = $_POST['status'];
    mysqli_query($koneksi, "UPDATE kamar SET tipe='$tipe', harga='$harga', status='$status' WHERE idkamar=$id");
    header("Location: index.php?tab=kamar");
}
if (isset($_GET['hapus_kamar'])) {
    $id = $_GET['hapus_kamar'];
    mysqli_query($koneksi, "DELETE FROM kamar WHERE idkamar=$id");
    header("Location: index.php?tab=kamar");
}

// Penghuni
if (isset($_POST['tambah_penghuni'])) {
    $nama = $_POST['nama']; 
    $no_hp = $_POST['no_hp']; 
    $asal = $_POST['asal']; 
    $gender = $_POST['gender']; 
    $idkamar = $_POST['idkamar'];
    
    //tambah penghuni
    mysqli_query($koneksi, "INSERT INTO penghuni (nama, no_hp, asal, gender, idkamar) VALUES ('$nama', '$no_hp', '$asal', '$gender', '$idkamar')");
    
    // update penghuni
    mysqli_query($koneksi, "UPDATE kamar SET status='Terisi' WHERE idkamar=$idkamar");
    
    header("Location: index.php?tab=penghuni");
}

// hapus penghuni
if (isset($_GET['hapus_penghuni'])) {
    $id = $_GET['hapus_penghuni'];
    
    $cari_kamar = mysqli_query($koneksi, "SELECT idkamar FROM penghuni WHERE idpenghuni=$id");
    $data_kamar = mysqli_fetch_array($cari_kamar);
    $idkamar_yang_ditinggal = $data_kamar['idkamar'];
    
    mysqli_query($koneksi, "DELETE FROM penghuni WHERE idpenghuni=$id");
    
    if ($idkamar_yang_ditinggal) {
        mysqli_query($koneksi, "UPDATE kamar SET status='Kosong' WHERE idkamar=$idkamar_yang_ditinggal");
    }
    
    header("Location: index.php?tab=penghuni");
}

// edit penghuni
if (isset($_POST['ubah_penghuni'])) {
    $id = $_POST['idpenghuni']; 
    $nama = $_POST['nama']; 
    $no_hp = $_POST['no_hp']; 
    $asal = $_POST['asal']; 
    $gender = $_POST['gender']; 
    $idkamar_baru = $_POST['idkamar'];

    $cari_kamar_lama = mysqli_query($koneksi, "SELECT idkamar FROM penghuni WHERE idpenghuni=$id");
    $data_kamar_lama = mysqli_fetch_array($cari_kamar_lama);
    $idkamar_lama = $data_kamar_lama['idkamar'];

    mysqli_query($koneksi, "UPDATE penghuni SET nama='$nama', no_hp='$no_hp', asal='$asal', gender='$gender', idkamar='$idkamar_baru' WHERE idpenghuni=$id");

    if ($idkamar_lama != $idkamar_baru) {

        if ($idkamar_lama) {
            mysqli_query($koneksi, "UPDATE kamar SET status='Kosong' WHERE idkamar=$idkamar_lama");
        }
 
        mysqli_query($koneksi, "UPDATE kamar SET status='Terisi' WHERE idkamar=$idkamar_baru");
    }

    header("Location: index.php?tab=penghuni");
}

// pembyaran
if (isset($_POST['tambah_pembayaran'])) {
    $idpenghuni = $_POST['idpenghuni']; $tanggal = $_POST['tanggal_bayar']; $jumlah = $_POST['jumlah_bayar']; $ket = $_POST['keterangan'];
    mysqli_query($koneksi, "INSERT INTO pembayaran (idpenghuni, tanggal_bayar, jumlah_bayar, keterangan) VALUES ('$idpenghuni', '$tanggal', '$jumlah', '$ket')");
    header("Location: index.php?tab=pembayaran");
}
if (isset($_POST['ubah_pembayaran'])) {
    $id = $_POST['idpembayaran']; $jumlah = $_POST['jumlah_bayar']; $ket = $_POST['keterangan'];
    mysqli_query($koneksi, "UPDATE pembayaran SET jumlah_bayar='$jumlah', keterangan='$ket' WHERE idpembayaran=$id");
    header("Location: index.php?tab=pembayaran");
}
if (isset($_GET['hapus_pembayaran'])) {
    $id = $_GET['hapus_pembayaran'];
    mysqli_query($koneksi, "DELETE FROM pembayaran WHERE idpembayaran=$id");
    header("Location: index.php?tab=pembayaran");
}

//mode edit
$edit_kamar = $edit_penghuni = $edit_pembayaran = false;
if (isset($_GET['edit_k'])) {
    $edit_kamar = true; $res = mysqli_query($koneksi, "SELECT * FROM kamar WHERE idkamar=".$_GET['edit_k']); $dk = mysqli_fetch_array($res);
}
if (isset($_GET['edit_p'])) {
    $edit_penghuni = true; $res = mysqli_query($koneksi, "SELECT * FROM penghuni WHERE idpenghuni=".$_GET['edit_p']); $dp = mysqli_fetch_array($res);
}
if (isset($_GET['edit_b'])) {
    $edit_pembayaran = true; $res = mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE idpembayaran=".$_GET['edit_b']); $db = mysqli_fetch_array($res);
}

// Mengatur tab menu yang aktif dibuka
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'kamar';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Informasi E-Kos Web</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 30px; background-color: #f4f7f6; color: #333; }
        h1 { color: #2c3e50; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; }
        .nav-tabs { margin-bottom: 20px; }
        .tab-link { padding: 10px 20px; background: #e0e0e0; text-decoration: none; color: #333; font-weight: bold; border-radius: 5px 5px 0 0; margin-right: 5px; display: inline-block; }
        .tab-link.active { background: #2c3e50; color: white; }
        .container { display: flex; gap: 30px; }
        .form-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 350px; height: fit-content; }
        .table-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); flex-grow: 1; }
        input, select { width: 100%; padding: 8px; margin: 8px 0 15px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #2c3e50; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .btn { padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 14px; color: white; display: inline-block; cursor: pointer; border: none; }
        .btn-submit { background-color: #27ae60; width: 100%; padding: 10px; font-weight: bold; }
        .btn-edit { background-color: #f39c12; margin-right: 5px; }
        .btn-delete { background-color: #c0392b; }
    </style>
</head>
<body>

    <h1>SISTEM MANAJEMEN E-KOSAN</h1>

    <div class="nav-tabs">
        <a href="index.php?tab=kamar" class="tab-link <?php echo $active_tab == 'kamar' ? 'active' : ''; ?>">Master Kamar</a>
        <a href="index.php?tab=penghuni" class="tab-link <?php echo $active_tab == 'penghuni' ? 'active' : ''; ?>">Master Penghuni</a>
        <a href="index.php?tab=pembayaran" class="tab-link <?php echo $active_tab == 'pembayaran' ? 'active' : ''; ?>">Transaksi Pembayaran</a>
    </div>

    <div class="container">
        
        <?php if ($active_tab == 'kamar'): ?>
            <div class="form-box">
                <h3><?php echo $edit_kamar ? "Ubah Data Kamar" : "Tambah Kamar Baru"; ?></h3>
                <form action="index.php" method="POST">
                    <input type="hidden" name="idkamar" value="<?php echo $edit_kamar ? $dk['idkamar'] : ''; ?>">
                    <label>Nomor Kamar:</label>
                    <input type="text" name="no_kamar" value="<?php echo $edit_kamar ? $dk['no_kamar'] : ''; ?>" <?php echo $edit_kamar ? 'readonly style="background:#eee;"' : 'required'; ?>>
                    
                    <label>Tipe Kamar:</label>
                    <input type="text" name="tipe" value="<?php echo $edit_kamar ? $dk['tipe'] : ''; ?>" required>
                    
                    <label>Harga Sewa Bulanan:</label>
                    <input type="number" name="harga" value="<?php echo $edit_kamar ? $dk['harga'] : ''; ?>" required>
                    
                    <label>Status Hunian:</label>
                    <select name="status">
                        <option value="Kosong" <?php echo ($edit_kamar && $dk['status'] == 'Kosong') ? 'selected' : ''; ?>>Kosong</option>
                        <option value="Terisi" <?php echo ($edit_kamar && $dk['status'] == 'Terisi') ? 'selected' : ''; ?>>Terisi</option>
                    </select>
                    
                    <button type="submit" name="<?php echo $edit_kamar ? 'ubah_kamar' : 'tambah_kamar'; ?>" class="btn btn-submit">
                        <?php echo $edit_kamar ? "Simpan Perubahan" : "Simpan Kamar"; ?>
                    </button>
                    <?php if($edit_kamar): ?> <a href="index.php?tab=kamar" style="display:block; text-align:center; margin-top:10px;">Batal</a> <?php endif; ?>
                </form>
            </div>

            <div class="table-box">
                <h3>Daftar Seluruh Kamar Kos</h3>
                <table>
                    <tr><th>ID</th><th>No Kamar</th><th>Tipe</th><th>Harga</th><th>Status</th><th>Aksi</th></tr>
                    <?php
                    $q = mysqli_query($koneksi, "SELECT * FROM kamar");
                    while($r = mysqli_fetch_array($q)){
                        echo "<tr>
                                <td>{$r['idkamar']}</td>
                                <td><b>{$r['no_kamar']}</b></td>
                                <td>{$r['tipe']}</td>
                                <td>Rp ".number_format($r['harga'],0,',','.')."</td>
                                <td><span style='color:".($r['status']=='Kosong'?'red':'green')."; font-weight:bold;'>{$r['status']}</span></td>
                                <td>
                                    <a href='index.php?tab=kamar&edit_k={$r['idkamar']}' class='btn btn-edit'>Edit</a>
                                    <a href='index.php?hapus_kamar={$r['idkamar']}' class='btn btn-delete' onclick=\"return confirm('Hapus kamar ini?')\">Hapus</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </table>
            </div>
        <?php endif; ?>


        <?php if ($active_tab == 'penghuni'): ?>
            <div class="form-box">
                <h3><?php echo $edit_penghuni ? "Ubah Data Penghuni" : "Tambah Penghuni Baru"; ?></h3>
                <form action="index.php" method="POST">
                    <input type="hidden" name="idpenghuni" value="<?php echo $edit_penghuni ? $dp['idpenghuni'] : ''; ?>">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="nama" value="<?php echo $edit_penghuni ? $dp['nama'] : ''; ?>" required>
                    
                    <label>No HP/WhatsApp:</label>
                    <input type="text" name="no_hp" value="<?php echo $edit_penghuni ? $dp['no_hp'] : ''; ?>" required>
                    
                    <label>Asal Daerah:</label>
                    <input type="text" name="asal" value="<?php echo $edit_penghuni ? $dp['asal'] : ''; ?>" required>
                    
                    <label>Gender:</label>
                    <select name="gender">
                        <option value="Laki-laki" <?php echo ($edit_penghuni && $dp['gender'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="Perempuan" <?php echo ($edit_penghuni && $dp['gender'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                    
                    <label>Pilih ID Kamar:</label>
                    <select name="idkamar" required>
                        <?php
                        $kamars = mysqli_query($koneksi, "SELECT * FROM kamar");
                        while($k = mysqli_fetch_array($kamars)){
                            $sel = ($edit_penghuni && $dp['idkamar'] == $k['idkamar']) ? 'selected' : '';
                            echo "<option value='{$k['idkamar']}' $sel>ID {$k['idkamar']} - Kamar {$k['no_kamar']} ({$k['status']})</option>";
                        }
                        ?>
                    </select>
                    
                    <button type="submit" name="<?php echo $edit_penghuni ? 'ubah_penghuni' : 'tambah_penghuni'; ?>" class="btn btn-submit">
                        <?php echo $edit_penghuni ? "Simpan Perubahan" : "Daftarkan Penghuni"; ?>
                    </button>
                    <?php if($edit_penghuni): ?> <a href="index.php?tab=penghuni" style="display:block; text-align:center; margin-top:10px;">Batal</a> <?php endif; ?>
                </form>
            </div>

            <div class="table-box">
                <h3>Daftar Penghuni Kos Aktif</h3>
                <table>
                    <tr><th>ID</th><th>Nama</th><th>No HP</th><th>Asal</th><th>Gender</th><th>Kamar Sewa</th><th>Aksi</th></tr>
                    <?php
                    // SQL JOIN untuk memunculkan teks Nomor Kamar fisik berdasarkan Relasi
                    $q = mysqli_query($koneksi, "SELECT p.*, k.no_kamar FROM penghuni p LEFT JOIN kamar k ON p.idkamar = k.idkamar");
                    while($r = mysqli_fetch_array($q)){
                        $nk = $r['no_kamar'] ? $r['no_kamar'] : '-';
                        echo "<tr>
                                <td>{$r['idpenghuni']}</td>
                                <td>{$r['nama']}</td>
                                <td>{$r['no_hp']}</td>
                                <td>{$r['asal']}</td>
                                <td>{$r['gender']}</td>
                                <td><b>Kamar $nk</b></td>
                                <td>
                                    <a href='index.php?tab=penghuni&edit_p={$r['idpenghuni']}' class='btn btn-edit'>Edit</a>
                                    <a href='index.php?hapus_penghuni={$r['idpenghuni']}' class='btn btn-delete' onclick=\"return confirm('Hapus data penghuni?')\">Hapus</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </table>
            </div>
        <?php endif; ?>


        <?php if ($active_tab == 'pembayaran'): ?>
            <div class="form-box">
                <h3><?php echo $edit_pembayaran ? "Ubah Transaksi" : "Catat Pembayaran"; ?></h3>
                <form action="index.php" method="POST">
                    <input type="hidden" name="idpembayaran" value="<?php echo $edit_pembayaran ? $db['idpembayaran'] : ''; ?>">
                    
                    <label>Nama Penghuni Kos:</label>
                    <?php if($edit_pembayaran): ?>
                        <input type="text" value="Ganti Nominal Saja" readonly style="background:#eee;">
                    <?php else: ?>
                        <select name="idpenghuni" required>
                            <?php
                            $penghunis = mysqli_query($koneksi, "SELECT * FROM penghuni");
                            while($p = mysqli_fetch_array($penghunis)){
                                echo "<option value='{$p['idpenghuni']}'>ID {$p['idpenghuni']} - {$p['nama']}</option>";
                            }
                            ?>
                        </select>
                    <?php endif; ?>
                    
                    <label>Tanggal Pembayaran:</label>
                    <input type="date" name="tanggal_bayar" value="<?php echo $edit_pembayaran ? $db['tanggal_bayar'] : date('Y-m-d'); ?>" <?php echo $edit_pembayaran ? 'readonly style="background:#eee;"' : 'required'; ?>>
                    
                    <label>Jumlah Uang (Rp):</label>
                    <input type="number" name="jumlah_bayar" value="<?php echo $edit_pembayaran ? $db['jumlah_bayar'] : ''; ?>" required>
                    
                    <label>Keterangan:</label>
                    <input type="text" name="keterangan" value="<?php echo $edit_pembayaran ? $db['keterangan'] : 'Lunas'; ?>" required>
                    
                    <button type="submit" name="<?php echo $edit_pembayaran ? 'ubah_pembayaran' : 'tambah_pembayaran'; ?>" class="btn btn-submit">
                        <?php echo $edit_pembayaran ? "Update Pembayaran" : "Simpan Pembayaran"; ?>
                    </button>
                    <?php if($edit_pembayaran): ?> <a href="index.php?tab=pembayaran" style="display:block; text-align:center; margin-top:10px;">Batal</a> <?php endif; ?>
                </form>
            </div>

            <div class="table-box">
                <h3>Riwayat Histori Transaksi Masuk</h3>
                <table>
                    <tr><th>ID Transaksi</th><th>Nama Pembayar</th><th>Tanggal Bayar</th><th>Jumlah</th><th>Keterangan</th><th>Aksi</th></tr>
                    <?php
                    // SQL JOIN Tiga tingkat untuk menarik nama penghuni ke transaksi keuangan
                    $q = mysqli_query($koneksi, "SELECT b.*, p.nama FROM pembayaran b JOIN penghuni p ON b.idpenghuni = p.idpenghuni ORDER BY b.idpembayaran DESC");
                    while($r = mysqli_fetch_array($q)){
                        $tgl = date('d-m-Y', strtotime($r['tanggal_bayar']));
                        echo "<tr>
                                <td>#{$r['idpembayaran']}</td>
                                <td>{$r['nama']}</td>
                                <td>{$tgl}</td>
                                <td>Rp ".number_format($r['jumlah_bayar'],0,',','.')."</td>
                                <td><i>{$r['keterangan']}</i></td>
                                <td>
                                    <a href='index.php?tab=pembayaran&edit_b={$r['idpembayaran']}' class='btn btn-edit'>Edit</a>
                                    <a href='index.php?hapus_pembayaran={$r['idpembayaran']}' class='btn btn-delete' onclick=\"return confirm('Hapus histori ini?')\">Hapus</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </table>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>