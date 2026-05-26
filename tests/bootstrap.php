<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

define('_BEAR_APP_HOME', __DIR__);
BEAR::init(BEAR::loadConfig(__DIR__ . '/app.yml'));
