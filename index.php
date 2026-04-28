<?php
function xorCipher($text, $key) {
    $output = '';
    $keyLength = strlen($key);

    for ($i = 0; $i < strlen($text); $i++) {
        $output .= chr(ord($text[$i]) ^ ord($key[$i % $keyLength]));
    }

    return $output;
}

function toHex($string) {
    return bin2hex($string);
}

function fromHex($hex) {
    return hex2bin($hex);
}

$result = "";

if (isset($_POST['proses'])) {
    $pesan = $_POST['pesan'];
    $key = $_POST['key'];
    $aksi = $_POST['aksi'];

    if ($aksi == "enkripsi") {
        $cipher = xorCipher($pesan, $key);
        $result = toHex($cipher);
    } else {
        $plain = xorCipher(fromHex($pesan), $key);
        $result = $plain;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Aplikasi XOR</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>🔐 XOR Encryption Tool</h1>

    <form method="post" class="card">
        <label>Masukkan Pesan</label>
        <input type="text" name="pesan" placeholder="Contoh: Rahasia Negara X" required>

        <label>Kata Kunci</label>
        <input type="text" name="key" placeholder="Contoh: UMPONTIANAK" required>

        <label>Pilih Aksi</label>
        <select name="aksi">
            <option value="enkripsi">Enkripsi (Plain → Cipher)</option>
            <option value="dekripsi">Dekripsi (Cipher → Plain)</option>
        </select>

        <button type="submit" name="proses">Proses</button>
    </form>

    <?php if ($result != ""): ?>
    <div class="result-card">
        <h3>Hasil Proses</h3>
        <p><?php echo $result; ?></p>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
