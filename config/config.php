<?php

use Zend\ConfigAggregator\ArrayProvider;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;

// To enable or disable caching, set the `ConfigAggregator::ENABLE_CACHE` boolean in
// `config/autoload/local.php`.
$cacheConfig = [
    'config_cache_path' => 'data/config-cache.php',
];

$aggregator = new ConfigAggregator([
    \Zend\Expressive\Router\ConfigProvider::class,
    \Zend\Log\ConfigProvider::class,
    // Include cache configuration
    new ArrayProvider($cacheConfig),

    //Attempt
    Attempt\ConfigProvider::class,

    //Exam
    Exam\ConfigProvider::class,

    //Exclusive
    Exclusive\ConfigProvider::class,

    //Payment
    Payment\ConfigProvider::class,

    //Payment
    Certificate\ConfigProvider::class,

    // Default App module
    App\ConfigProvider::class,

    // Composite
    Shared\ConfigProvider::class,

    // Course
    Course\ConfigProvider::class,

    // Topic
    Topic\ConfigProvider::class,

    // Assignment
    Assignment\ConfigProvider::class,

    // Scorm
    Scorm\ConfigProvider::class,

    // File
    File\ConfigProvider::class,

    // User
    User\ConfigProvider::class,

    // User Student
    Student\ConfigProvider::class,

    // User Tutor
    Tutor\ConfigProvider::class,

    // User Admin
    Admin\ConfigProvider::class,

    // Rbac
    Rbac\ConfigProvider::class,

    // Uploader
    Uploader\ConfigProvider::class,

    // Message
    Message\ConfigProvider::class,

    // Options
    Options\ConfigProvider::class,

    // Mail
    Mail\ConfigProvider::class,

    // Amazon
    Amazon\ConfigProvider::class,

    // ErrorLogger
    ErrorLogger\ConfigProvider::class,

    // TwitterBootstrap
    TwitterBootstrap\ConfigProvider::class,

    // Zend Components
    Zend\InputFilter\ConfigProvider::class,
    Zend\Filter\ConfigProvider::class,
    Zend\Validator\ConfigProvider::class,
    Zend\Db\ConfigProvider::class,
    \Zend\Form\ConfigProvider::class,
    \Zend\Hydrator\ConfigProvider::class,
    \Zend\Session\ConfigProvider::class,
    \Zend\Mail\ConfigProvider::class,
    // \Zend\I18n\ConfigProvider::class,

    // Load application config in a pre-defined order in such a way that local settings
    // overwrite global settings. (Loaded as first to last):
    //   - `global.php`
    //   - `*.global.php`
    //   - `local.php`
    //   - `*.local.php`
    new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),

    // Load development config if it exists
    new PhpFileProvider('config/development.config.php'),
], $cacheConfig['config_cache_path']);

return $aggregator->getMergedConfig();
