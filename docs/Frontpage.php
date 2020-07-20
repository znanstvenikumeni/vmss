<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to vmss!</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato&family=Source+Code+Pro&display=swap" rel="stylesheet">
    <style>
        body{
            margin: 30px;
            font-family: 'Lato', sans-serif;
        }
        code, h1{
            font-family: 'Source Code Pro', monospace;
        }
    </style>
</head>
<body>
    <h1>Welcome to vmss!</h1>
    <p>vmss seems to be up and running: or at least able to read its config. You're sporting vmss <code>v<?php echo vmss_version; ?></code> on API version <code><?php echo api_version; ?></code>.</p>
    <p>To check if everything's configured properly, use <a href="/selfcheck">the selfcheck API endpoint</a>. To start building apps that integrate with vmss, <a href="https://github.com/znanstvenikumeni/vmss/wiki">take a look at the documentation on GitHub</a>.</p>
    <p>When you're ready, you can prevent vmss from showing this page by setting the <code>showWelcome</code> setting in .config.json to false.</p>
    <p>Have fun building something great!</p>

</body>
</html>