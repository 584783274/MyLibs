<?php
/*
* Copyright 2014 Baidu, Inc.
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with
* the License. You may obtain a copy of the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on
* an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the
* specific language governing permissions and limitations under the License.
*/

use BaiduBce\BceClientConfigOptions;

error_reporting(-1);
date_default_timezone_set('UTC');

define('__BOS_CLIENT_ROOT', dirname(__DIR__));

$BOS_TEST_CONFIG =
    array(
        'credentials' => array(
            'accessKeyId' => 'your ak',
            'secretAccessKey' => 'your sk',

            'sessionToken' => 'your session token'
        ),
        'endpoint' => 'host',
        'stsEndpoint' => 'sts host',
    );

$CUSTOM_BOS_TEST_CONFIG =
    array(
        'credentials' => array(
            'accessKeyId' => 'your ak',
            'secretAccessKey' => 'your sk',

            'sessionToken' => 'your session token'
        ),
        'endpoint' => 'customized host',
        'custom' => true,
        'stsEndpoint' => 'sts host',
    );

// this in your YourConf.php
$SMS_TEST_CONFIG =
    array(
        BceClientConfigOptions::PROTOCOL => 'http',
        BceClientConfigOptions::REGION => 'bj',
        'credentials' => array(
            'ak' => 'your ak',
            'sk' => 'your sk'
        ),
        'endpoint' => 'smsv3.bj.baidubce.com',
    );



$STDERR = fopen('php://stderr', 'w+');
$__handler = new \Monolog\Handler\StreamHandler($STDERR, \Monolog\Logger::DEBUG);
$__handler->setFormatter(
    new \Monolog\Formatter\LineFormatter(null, null, false, true)
);
\BaiduBce\Log\LogFactory::setInstance(
    new \BaiduBce\Log\MonoLogFactory(array($__handler))
);
\BaiduBce\Log\LogFactory::setLogLevel(\Psr\Log\LogLevel::DEBUG);
