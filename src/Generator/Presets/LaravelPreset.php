<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Generator\Presets;

use Andriichuk\KeepEnv\Specification\Variable;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class LaravelPreset implements PresetInterface
{
    public function provide(): array
    {
        return [
            'APP_ENV' => new Variable(
                'APP_ENV',
                'Application environment.',
                false,
                false,
                [
                    'required' => true,
                    'enum' => ['local', 'production'],
                ],
                'local',
            ),
            'APP_KEY' => new Variable(
                'APP_KEY',
                'Application key.',
                false,
                false,
                [
                    'required' => true,
                ],
            ),
            'APP_DEBUG' => new Variable(
                'APP_DEBUG',
                'Application debug mode.',
                false,
                false,
                [
                    'required' => true,
                    'enum' => ['true', 'false'],
                ],
                'true',
            ),
            'LOG_CHANNEL' => new Variable(
                'LOG_CHANNEL',
                'Channel for logging.',
                false,
                false,
                [
                    'required' => true,
                    'enum' => ['single', 'daily', 'slack', 'syslog', 'errorlog', 'monolog', 'custom', 'stack'],
                ],
                'stack',
            ),
            'LOG_LEVEL' => new Variable(
                'LOG_LEVEL',
                'Logging level.',
                false,
                false,
                [
                    'required' => true,
                    'enum' => ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'],
                ],
                'debug',
            ),
            'DB_DATABASE' => new Variable(
                'DB_DATABASE',
                'Database name.',
                false,
                false,
                [
                    'required' => true,
                ],
            ),
            'DB_HOST' => new Variable(
                'DB_HOST',
                'Database host.',
                false,
                false,
                [
                    'required' => true,
                ],
                '127.0.0.1',
            ),
            'DB_PORT' => new Variable(
                'DB_PORT',
                'Database port.',
                false,
                false,
                [
                    'required' => true,
                    'numeric' => true,
                ],
                '3306',
            ),
            'QUEUE_CONNECTION' => new Variable(
                'QUEUE_CONNECTION',
                'Queue connection.',
                false,
                false,
                [
                    'required' => true,
                    'enum' => ['sync', 'database', 'beanstalkd', 'sqs', 'redis', 'null'],
                ],
                'sync',
            ),
            'MEMCACHED_HOST' => new Variable(
                'MEMCACHED_HOST',
                'Memcached host.',
                false,
                false,
                [
                    'required' => true,
                ],
                '127.0.0.1',
            ),
            'BROADCAST_DRIVER' => new Variable(
                'BROADCAST_DRIVER',
                'Broadcast driver.',
                false,
                false,
                [
                    'required' => true,
                    'enum' => ['pusher', 'ably', 'redis', 'log', 'null'],
                ],
                'log',
            ),
            'CACHE_DRIVER' => new Variable(
                'CACHE_DRIVER',
                'Cache driver.',
                false,
                false,
                [
                    'required' => true,
                    'enum' => ['apc', 'array', 'database', 'file', 'memcached', 'redis', 'dynamodb', 'octane', 'null'],
                ],
                'file',
            ),
            'FILESYSTEM_DRIVER' => new Variable(
                'FILESYSTEM_DRIVER',
                'File system driver.',
                false,
                false,
                [
                    'required' => true,
                    'enum' => ['local', 'ftp', 'sftp', 's3'],
                ],
                'local',
            ),
            'SESSION_DRIVER' => new Variable(
                'SESSION_DRIVER',
                'Session driver.',
                false,
                false,
                [
                    'required' => true,
                    'enum' => ['file', 'cookie', 'database', 'apc', 'memcached', 'redis', 'dynamodb', 'array'],
                ],
                'file',
            ),
            'SESSION_LIFETIME' => new Variable(
                'SESSION_LIFETIME',
                'Session lifetime.',
                false,
                false,
                [
                    'required' => true,
                    'numeric' => true,
                ],
                120,
            ),
            'REDIS_HOST' => new Variable(
                'REDIS_HOST',
                'Redis connection host.',
                false,
                false,
                [
                    'required' => true,
                ],
                '127.0.0.1',
            ),
            'REDIS_PORT' => new Variable(
                'REDIS_PORT',
                'Redis connection port.',
                false,
                false,
                [
                    'required' => true,
                    'numeric' => true,
                ],
                6379,
            ),
            'MAIL_MAILER' => new Variable(
                'MAIL_MAILER',
                'Mailer driver.',
                false,
                false,
                [
                    'required' => true,
                    'enum' => ['smtp', 'sendmail', 'mailgun', 'ses', 'postmark', 'log', 'array', 'failover'],
                ],
                'smtp',
            ),
        ];
    }
}
