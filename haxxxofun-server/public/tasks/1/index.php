<?php
$communicate = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['login'] != '' && $_POST['password'] != '') {
        if ($_POST['login'] == 'admin' && $_POST['password'] == 'hohohohohoyoullneverguess&&&') {
            setcookie('logged', 1);
        } else {
            $communicate = 'Account not found!';
            setcookie('logged', 0);
        }
    }
}
$logged = $_COOKIE['logged'] == 1;
if ($logged) {
    $configs = include('../../../config/tasks.php');
}
?>
<!doctype html>
<html lang="pl">
<head>
    <title>haxxxo.fun - task 1</title>
</head>
<body>
<?php if ($logged): ?>
    <p>Password is: <b><?php echo $configs['codes']['task1']; ?></b></p>
<?php else: ?>
    <p><?php echo $communicate; ?></p>
    <form method="post" action="">
        <fieldset>
            <label for="login">Login</label>
            <input type="text" id="login" name="login"/>
            <br/>
            <label for="password">Password</label>
            <input type="password" id="password" name="password"/>
            <br/>
            <input type="submit" value="LogIn"/>
        </fieldset>
    </form>
    <br/><br/>
    <button id="showHint" onclick="showHint()">Show hint</button>
    <div id="hint" style="display: none;">
        <img src="hint.png" alt="hint"/>
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
