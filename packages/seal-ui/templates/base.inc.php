<?php

function escape(string $value): string
{
    return \htmlspecialchars($value, \ENT_QUOTES);
}

return static function(callable $contentBlock) { ?>
<!doctype html>
<html lang="en" style="min-height: 100vh;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo ($title ?? 'SEAL'); ?> | SEAL</title>
    <style>
        * {
            box-sizing: border-box;
        }
    </style>
</head>
<body style="margin: 0; position: relative; min-height: 100vh; font-family: monospace;">
    <?php echo $contentBlock(); ?>
</body>
</html>
<?php
};
