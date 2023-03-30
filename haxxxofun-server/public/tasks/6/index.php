<?php

function base64url_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function generate_jwt($headers, $payload, $secret = 'secret')
{
    $headers_encoded = base64url_encode(json_encode($headers));
    $payload_encoded = base64url_encode(json_encode($payload));
    $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
    $signature_encoded = base64url_encode($signature);
    $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";
    return $jwt;
}


//$jwt = generate_jwt(
//    array(
//        "alg" => "HS256",
//        "typ" => "JWT"),
//    array(
//        "login" => "guest",
//        "exp" => time() + 3600
//    ));
//var_dump(is_jwt_valid($jwt));
//die();

$token = '';
$communicate = 'Try guest:guest account for preview.';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['login'] != '' && $_POST['password'] != '') {
        if ($_POST['login'] == 'admin' && $_POST['password'] == 'hohohohohoyoullneverguess&&&&&&&&') {
            $token = generate_jwt(
                array(
                    "alg" => "HS256",
                    "typ" => "JWT"),
                array(
                    "login" => "admin",
                    "exp" => time() + 3600
                ), 'secret-sauce');
        } else if ($_POST['login'] == 'guest' && $_POST['password'] == 'guest') {
            $token = generate_jwt(
                array(
                    "alg" => "HS256",
                    "typ" => "JWT"),
                array(
                    "login" => "guest",
                    "exp" => time() + 3600
                ), 'secret-sauce');
        } else {
            $communicate = 'Account not found!';
        }
    }
}
?>
<!doctype html>
<html lang="pl">
<head>
    <title>haxxxo.fun - task 6</title>
</head>
<body>
<?php if ($token): ?>
    <iframe id="extFrame" src="api.php?token=<?php echo $token; ?>"></iframe>
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
            <!-- signature token test: secret-sauce -->
        </fieldset>
    </form>
<br/><br/>
    <button id="showHint" onclick="showHint()">Show hint</button>
    <div id="hint" style="display: none;">
        <img src="hint.png" alt="hint"/>
    </div>
    <script>
        function showHint() {
            document.getElementById("showHint").style.display = "none";
            document.getElementById("hint").style.display = "block";
        }
    </script>
<?php endif; ?>
</body>
</html>
