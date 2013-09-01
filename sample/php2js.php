<?php
require('../shttp.php');

// Set URL of SHTTP CA.
define('SHTTP_CA_URL', 'https://shttp.herokuapp.com/server.api');

$shttp = SHTTP::init(SHTTP_CA_URL);

session_start();

$_SESSION['SHTTP.key'] = $shttp->key;
$_SESSION['SHTTP.url'] = $shttp->url;

// SessionID verification code must change each request.(like a CSRF token)
$_SESSION['SHTTP.code'] = SHTTP::getRandomString();
$shttpCodeField = '<input type="hidden" name="shttp_code" value="'.$_SESSION['SHTTP.code'].'">';

$plaintext = <<<EOT
<h1>Test From</h1>

<div class="form">
<form id="frm1" action="./js2php.php" method="post" onSubmit='SHTTP.prepare()'>
    <p>
		<span class="title">Name</span>
        <input name="SettingForm[name]" id="SettingForm_name" type="text" value="sklab" />
    </p><p>
		<span class="title">Tel</span>
        <input name="SettingForm[tel]" id="SettingForm_tel" type="text" />
    </p><p>
		<span class="title">Address</span>
        <input name="SettingForm[address]" id="SettingForm_address" type="text" />
    </p><p>
		<span class="title">Password</span>
        <input name="SettingForm[password]" id="SettingForm_password" type="password" />	
    </p><p>
		<span class="title">Comment</span>
        <textarea name="SettingForm[comment]" id="SettingForm_comment"/>write something</textarea>
    </p><p>
		<input type="submit" value="Next" />
    </p>
	$shttpCodeField
</form>
</div><!-- form -->
EOT;

    $encrypted = SHTTP::encrypt($shttp->key, $plaintext);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="-1"> 

<style type="text/css">
    .title {
        float: left;
        width: 100px;
    }
    #SettingForm_comment {
        width: 500px;
        height: 100px
    }
</style>

<script type="text/javascript" src="../js/shttp.js"></script>
<script type="text/javascript" src="<?= $shttp->url ?>"></script>

</head>
<body>
<script type="text/javascript">
document.write(SHTTP.decrypt('<?= $encrypted ?>'));

// Write your JavaScrypt code into this area.

</script>
</body>
</html>
