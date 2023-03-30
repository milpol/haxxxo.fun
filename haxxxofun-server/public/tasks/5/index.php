<?php
session_start();
if (!isset($_SESSION['logged'])) {
    $_SESSION['logged'] = false;
}
$communicate = '';
$action = isset($_GET['action']) ? $_GET['action'] : '';
if ($action === 'logout') {
    session_destroy();
    header('Location: index.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'login' && $_POST['login'] != '' && $_POST['password'] != '') {
        $_SESSION['logged'] = $_POST['login'] === 'guest' && $_POST['password'] === 'guest';
        $communicate = $_SESSION['logged'] ? '' : 'Account not found';
        $_SESSION['login'] = 'guest';
        $_SESSION['username'] = 'Guest account';
    }
    if ($_SESSION['logged'] && $action === 'account') {
        $_SESSION['login'] = trim($_POST['login']);
        $_SESSION['username'] = trim($_POST['username']);
        $communicate = 'Account changes applied';
    }
}
if ($action === 'secret' && $_SESSION['logged']) {
    $configs = include('../../../config/tasks.php');
}
?>
<!doctype html>
<html lang="pl">
<head>
    <title>haxxxo.fun - task 5</title>
</head>
<body>
<?php if ($_SESSION['logged']): ?>
    <ul>
        <li><a href="?action=">Main page</a></li>
        <li><a href="?action=account">Manage account</a></li>
        <li><a href="?action=secret">Secret page</a></li>
        <li><a href="?action=logout">Logout</a></li>
    </ul>
    <?php if ($action === 'account'): ?>
        <p><?php echo $communicate; ?></p>
        <form method="post" action="?action=account">
            <fieldset>
                <label for="username">User name:</label>
                <input type="text" id="username" name="username" value="<?php echo $_SESSION['username'] ?>"/>
                <input type="hidden" name="login" value="<?php echo $_SESSION['login'] ?>"/>
                <br/>
                <input type="submit" value="Save!"/>
            </fieldset>
        </form>
    <?php elseif ($action === 'secret'): ?>
        <?php if ($_SESSION['login'] !== 'captain'): ?>
            <p>You have to be <b>captain</b> to see this page.</p>
        <?php else: ?>
            <p>Password is: <b><?php echo $configs['codes']['task5']; ?></b></p>
        <?php endif; ?>
    <?php else: ?>
        <p>Welcome <b><?php echo $_SESSION['username'] ?></b></p>
    <?php endif; ?>
<?php else: ?>
    <p><?php echo $communicate; ?></p>
    <p>Check our system with Guest account!<br/> Login: <b>guest</b> Password: <b>guest</b></p>
    <form method="post" action="?action=login">
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
