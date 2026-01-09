<?php

namespace App\Services\LetsPeppol\Traits;

use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    /**
     * Log an info message with automatic class context
     */
    protected function logInfo(string $message, array $context = []): void
    {
        Log::info($this->formatLogMessage($message), $context);
    }

    /**
     * Log an error message with automatic class context
     */
    protected function logError(string $message, array $context = []): void
    {
        Log::error($this->formatLogMessage($message), $context);
    }

    /**
     * Log a warning message with automatic class context
     */
    protected function logWarning(string $message, array $context = []): void
    {
        Log::warning($this->formatLogMessage($message), $context);
    }

    /**
     * Format log message with class context
     */
    private function formatLogMessage(string $message): string
    {
        $class = static::class;
        $shortClass = substr($class, strrpos($class, '\\') + 1);
        
        return "[{$shortClass}] {$message}";
    }
}
