<?php
session_start();
if (!isset($_SESSION['hash'])) {
    $_SESSION['hash'] = md5(time());
}
$logged = false;
$communicate = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validToken = $_POST['token'] == $_SESSION['hash'];
    if ($validToken) {
        $logged = $_POST['password'] == '41253';
        $communicate = $logged ? '' : 'Account not found';
    } else {
        $communicate = 'CSFR token check failed';
    }
}
if ($logged) {
    $configs = include('../../../config/tasks.php');
}
?>
<!doctype html>
<html lang="pl">
<head>
    <title>haxxxo.fun - task 4</title>
</head>
<body>
<?php if ($logged): ?>
    <p>Password is: <b><?php echo $configs['codes']['task4']; ?></b></p>
<?php else: ?>
    <p><?php echo $communicate; ?></p>
    <form method="post" action="">
        <fieldset>
            <label for="password">Password</label>
            <input type="password" id="password" name="password"/>
            <br/>
            <input type="hidden" name="token" value="<?php echo $_SESSION['hash'] ?>"/>
            <input type="submit" value="LogIn"/>
        </fieldset>
    </form>
    <br/><br/>
    <button id="showHint" onclick="showHint()">Show hint</button>
    <div id="hint" style="display: none;">
        <img src="hint.png" alt="hint"/>
        <a href="brute.txt" target="_blank">DICTIONARY</a>
    </div>
<?php endif; ?>
<script>
    function showHint() {
        document.getElementById("showHint").style.display = "none";
        document.getElementById("hint").style.display = "block";
    }
</script>
</body>
</html>
