<?php

declare(strict_types=1);

return [
    '@root' => dirname(__DIR__, 2),
    '@assets' => '@root/public/assets',
    '@assetsUrl' => '@baseUrl/assets',
    '@baseUrl' => '/',
    '@messages' => '@resources/messages',
    '@npm' => '@root/node_modules',
    '@public' => '@root/public',
    '@resources' => '@root/resources',
    '@runtime' => '@root/runtime',
    '@vendor' => '@root/vendor',
    '@layout' => '@resources/views/layout',
    '@views' => '@resources/views',
];
