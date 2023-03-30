<?php
$logged = false;
$communicate = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $configs = include('../../../config/tasks.php');
    if ($_POST['login'] != '' && $_POST['password'] != '') {
        $query = "SELECT * FROM users WHERE username = '{$_POST['login']}' AND password = '{$_POST['password']}'";
        $connection = mysqli_connect(
            $configs['db']['host'],
            $configs['db']['username'],
            $configs['db']['password'],
            $configs['db']['name']);
        $queryResult = mysqli_query($connection, $query);
        if ($queryResult) {
            $logged = mysqli_num_rows($queryResult) > 0;
            $communicate = $logged ? '' : 'Account not found';
        } else {
            $communicate = mysqli_error($connection);
        }
    }
}
if ($logged) {
    $configs = include('../../../config/tasks.php');
}
?>
<!doctype html>
<html lang="pl">
<head>
    <title>haxxxo.fun - task 3</title>
</head>
<body>
<?php if ($logged): ?>
    <p>Password is: <b><?php echo $configs['codes']['task3']; ?></b></p>
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
