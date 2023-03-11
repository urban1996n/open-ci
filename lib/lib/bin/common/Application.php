<?php

use Symfony\Bundle\FrameworkBundle\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct(new Runtime($_SERVER['APP_ENV'], true));
    }

    public static function getRootDirectory(): string
    {
        return \dirname(__DIR__ . '/../../../');
    }
}
