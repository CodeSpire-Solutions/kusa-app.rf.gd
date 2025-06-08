<?php
session_start();
$history = $_SESSION['history'] ?? [];
$input = "";
$redirect = null;

function logDebug($message) {
    error_log("[DEBUG] " . $message);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = trim($_POST['command'] ?? '');
    logDebug("Next Command: $input");
    $output = "";

    if (!empty($_SESSION['awaiting_password'])) {
        logDebug("Awaiting Passowrd.");
        if ($input === '???') {
            logDebug("Password right.");
            unset($_SESSION['awaiting_password']);
            $redirect = '/game/index.html';
        } else {
            logDebug("Entered False Password.");
            $output = "False Password.";
        }
    } elseif ($input === 'start') {
        logDebug("Got start command. Need to enter Password. ");
        $_SESSION['awaiting_password'] = true;
        $output = "Enter Password:";
    } elseif ($input === 'date') {
        logDebug("Got Date-Command.");
        $output = date("Y-m-d H:i:s");
    } elseif ($input === 'help') {
        logDebug("Got Help-Command.");
        $output = "Available Commands: start, date, help, about";
    } elseif ($input === 'about') {
        logDebug("About-Befehl erhalten.");
        $output = "PHP V8 running on https://kusa-app.rf.gd";
    } else {
        logDebug("Unknown Command: $input");
        $output = "Cannot find command: $input";
    }

    if ($output !== null && $output !== '') {
        $history[] = "> $input";
        $history[] = $output;
        $_SESSION['history'] = $history;
    }

    if ($redirect) {
        logDebug("Redirecting to: $redirect");
        header("Location: $redirect");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <title>Terminal</title>
    <style>
        body {
            background-color: black;
            color: #00FF00;
            font-family: monospace;
            padding: 1em;
        }
        input[type="text"] {
            background: black;
            border: none;
            color: #00FF00;
            width: 100%;
            outline: none;
        }
    </style>
</head>
<body>
    <div>
        <?php foreach ($history as $line): ?>
            <div><?= htmlspecialchars($line) ?></div>
        <?php endforeach; ?>
        <form method="POST">
            <label>> <input type="text" name="command" autofocus autocomplete="off" /></label>
        </form>
    </div>
</body>
</html>
