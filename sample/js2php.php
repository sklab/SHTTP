<?php
require('../shttp.php');

session_start();

if (!isset($_SESSION['SHTTP.key'])) {
    throw new Exception('Session attribute [\'SHTTP.key\'] required - not found in session');
}
$commonKey = $_SESSION['SHTTP.key'];
$jsUrl = $_SESSION['SHTTP.url'];

// Verify "SessionID" using verification code if you want more secure.
if (!isset($_REQUEST['shttp_code'])) {
    throw new Exception('Parameter [\'shttp_code\'] required - not found');
} else {
    $verificationCode = SHTTP::decrypt($commonKey, $_REQUEST['shttp_code']);
    if ($_SESSION['SHTTP.code'] != $verificationCode) {
        throw new Exception('Invalid session.');
    }
}

// SessionID verification code must change each request.(like a CSRF token)
$_SESSION['SHTTP.code'] = SHTTP::getRandomString();
$shttpCodeField = '<input type="hidden" name="shttp_code" value="'.$_SESSION['SHTTP.code'].'">';

if (isset($_REQUEST['logout'])) session_write_close();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="content-style-type"  content="text/css" />
<meta http-equiv="content-script-type" content="text/javascript" />

<script type="text/javascript" src="../js/shttp.js"></script>
<script type="text/javascript" src="<?= $jsUrl ?>"></script>

<style type="text/css">
    .title {
        float: left;
        width: 100px;
    }
    input {
        width: 500px;
    }
</style>

</head>
<body>
<?php 
if (array_key_exists('SettingForm', $_REQUEST)) {
    echo "<h1>Confirmation</h1>\n";
    
    $arr = $_REQUEST['SettingForm'];
    echo "[Original]\n<p>";
    foreach ($arr as $key => $value) {
        echo "Key: $key; Value: $value<br />\n";
    }

    echo "</p>[Decrypt]\n<p>";
    foreach ($arr as $key => $value) {
        $plaintext = SHTTP::decrypt($commonKey, $value);
        $arr[$key] = htmlentities($plaintext, ENT_QUOTES, 'UTF-8');
    }
    foreach ($arr as $key => $value) {
        echo "<span class=\"title\">$key</span>$value<br />\n";
    }
    echo "<form name='confirm_form' method='post' onSubmit='SHTTP.prepare()'>\n";
    echo "<input type='hidden' name='logout' value='true'>\n";
    echo "$shttpCodeField\n";
    echo "<input type='submit' value='Submit'>\n";
    echo "</form>\n";
} else {
    echo "<h1><a href='./'>Finish!</a></h1>\n";
}
?>

</body>
</html>
