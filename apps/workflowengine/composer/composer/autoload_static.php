<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitWorkflowEngine
{
    public static $prefixLengthsPsr4 = array (
        'O' => 
        array (
            'OCA\\WorkflowEngine\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'OCA\\WorkflowEngine\\' => 
        array (
            0 => __DIR__ . '/..' . '/../lib',
        ),
    );

    public static $classMap = array (
        'OCA\\WorkflowEngine\\AppInfo\\Application' => __DIR__ . '/..' . '/../lib/AppInfo/Application.php',
        'OCA\\WorkflowEngine\\Check\\AbstractStringCheck' => __DIR__ . '/..' . '/../lib/Check/AbstractStringCheck.php',
        'OCA\\WorkflowEngine\\Check\\FileMimeType' => __DIR__ . '/..' . '/../lib/Check/FileMimeType.php',
        'OCA\\WorkflowEngine\\Check\\FileName' => __DIR__ . '/..' . '/../lib/Check/FileName.php',
        'OCA\\WorkflowEngine\\Check\\FileSize' => __DIR__ . '/..' . '/../lib/Check/FileSize.php',
        'OCA\\WorkflowEngine\\Check\\FileSystemTags' => __DIR__ . '/..' . '/../lib/Check/FileSystemTags.php',
        'OCA\\WorkflowEngine\\Check\\RequestRemoteAddress' => __DIR__ . '/..' . '/../lib/Check/RequestRemoteAddress.php',
        'OCA\\WorkflowEngine\\Check\\RequestTime' => __DIR__ . '/..' . '/../lib/Check/RequestTime.php',
        'OCA\\WorkflowEngine\\Check\\RequestURL' => __DIR__ . '/..' . '/../lib/Check/RequestURL.php',
        'OCA\\WorkflowEngine\\Check\\RequestUserAgent' => __DIR__ . '/..' . '/../lib/Check/RequestUserAgent.php',
        'OCA\\WorkflowEngine\\Check\\UserGroupMembership' => __DIR__ . '/..' . '/../lib/Check/UserGroupMembership.php',
        'OCA\\WorkflowEngine\\Controller\\FlowOperations' => __DIR__ . '/..' . '/../lib/Controller/FlowOperations.php',
        'OCA\\WorkflowEngine\\Controller\\RequestTime' => __DIR__ . '/..' . '/../lib/Controller/RequestTime.php',
        'OCA\\WorkflowEngine\\Manager' => __DIR__ . '/..' . '/../lib/Manager.php',
        'OCA\\WorkflowEngine\\Settings\\Section' => __DIR__ . '/..' . '/../lib/Settings/Section.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitWorkflowEngine::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitWorkflowEngine::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitWorkflowEngine::$classMap;

        }, null, ClassLoader::class);
    }
}
