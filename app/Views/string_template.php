<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>String View</title>
</head>
<body>
    <h1>Template - String View</h1>
    <?php
    for ($i = 0; $i <= $size; $i++) { 
        echo "<h3>Hello, $name - n° $i</h3>\r\n    ";
    }
    ?>
</body>
</html>
