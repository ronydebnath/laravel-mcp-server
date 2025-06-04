<?php

namespace Ronydebnath\MCP;

use Illuminate\Support\ServiceProvider;
use Ronydebnath\MCP\Client\MCPClient;
use Ronydebnath\MCP\Client\StreamingMCPClient;
use Ronydebnath\MCP\Server\MCPServer;
use Ronydebnath\MCP\Server\Auth\AuthProvider;
use Ronydebnath\MCP\Server\Auth\AuthMiddleware;
use Ronydebnath\MCP\Shared\Memory;
use Ronydebnath\MCP\Shared\Progress;
use Ronydebnath\MCP\Shared\Context;

class MCPServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/mcp.php', 'mcp'
        );

        $this->app->singleton(MCPClient::class, function ($app) {
            $config = $app['config']['mcp.connections'][$app['config']['mcp.default']];
            
            return new MCPClient($config);
        });

        $this->app->singleton(StreamingMCPClient::class, function ($app) {
            $config = $app['config']['mcp.connections'][$app['config']['mcp.default']];
            
            return new StreamingMCPClient($config);
        });

        $this->app->singleton(MCPServer::class, function ($app) {
            $config = $app['config']['mcp.server'] ?? [];
            return new MCPServer($config);
        });

        $this->app->singleton(AuthProvider::class, function ($app) {
            $config = $app['config']['mcp.auth'] ?? [];
            return new AuthProvider($config);
        });

        $this->app->singleton(AuthMiddleware::class, function ($app) {
            return new AuthMiddleware($app->make(AuthProvider::class));
        });

        $this->app->singleton(Memory::class, function ($app) {
            $config = $app['config']['mcp.memory'] ?? [];
            return new Memory($config['max_size'] ?? 100);
        });

        $this->app->singleton(Progress::class, function () {
            return new Progress();
        });

        $this->app->singleton(Context::class, function ($app) {
            return new Context($app->make(Memory::class));
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/mcp.php' => config_path('mcp.php'),
        ], 'config');
    }
} 