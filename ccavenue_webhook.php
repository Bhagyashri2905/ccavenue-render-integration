<?php
$working_key = '5359E7A74922E31E22D5EF4DC0545518';
$encResp = $_POST['encResp'] ?? '';
file_put_contents("webhook_log.json", json_encode($_POST));

function hextobin($hexString) {
    $bin = "";
    for ($i = 0; $i < strlen($hexString); $i += 2) {
        $bin .= pack("H*", substr($hexString, $i, 2));
    }
    return $bin;
}

function decrypt($encryptedText, $key) {
    $key = hextobin(md5($key));
    $initVector = pack("C*", ...range(0, 15));
    $encryptedText = hextobin($encryptedText);
    return openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
}

$decrypted = decrypt($encResp, $working_key);
parse_str($decrypted, $parsed);
file_put_contents("decrypted_log.json", json_encode($parsed));

$refNo = $parsed['reference_no'] ?? 'NA';
$status = strtolower($parsed['order_status'] ?? 'Unknown');
$status = $status === 'success' ? 'success' : 'failed';

echo json_encode(["status" => "received", "reference_no" => $refNo, "order_status" => $status]);
?>
