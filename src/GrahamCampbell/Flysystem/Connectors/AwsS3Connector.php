<?php

/**
 * This file is part of Laravel Flysystem by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Flysystem\Connectors;

use Aws\S3\S3Client;
use Flysystem\Adapter\AwsS3;

/**
 * This is the awss3 connector class.
 *
 * @package    Laravel-Flysystem
 * @author     Graham Campbell
 * @copyright  Copyright 2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Flysystem/blob/develop/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Flysystem
 */
class AwsS3Connector implements ConnectorInterface
{
    /**
     * Establish an adapter connection.
     *
     * @param  array  $config
     * @return \Flysystem\Adapter\AwsS3
     */
    public function connect(array $config)
    {
        $auth = $this->getAuth($config);
        $client = $this->getClient($auth);
        $config = $this->getConfig($config);
        return $this->getAdapter($client, $config);
    }

    protected function getAuth(array $config)
    {
        if (!array_key_exists('key', $config) || !array_key_exists('secret', $config)) {
            throw new \InvalidArgumentException('The awss3 connector requires authentication.');
        }

        return array('key' => $config['key'], 'secret' => $config['secret']);
    }

    protected function getClient(array $auth)
    {
        return S3Client::factory($auth);
    }

    protected function getConfig(array $config)
    {
        if (!array_key_exists('bucket', $config)) {
            throw new \InvalidArgumentException('The awss3 connector requires a bucket.');
        }

        $prefix = null;

        if (array_key_exists('prefix', $config)) {
            $prefix = $config['prefix'];
        }

        return array('bucket' => $bucket, 'prefix' => $prefix);
    }

    protected function getAdapter($client, array $config)
    {
        return new AwsS3($client, $config['bucket'], $config['prefix']);
    }
}