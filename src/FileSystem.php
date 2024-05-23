<?php

namespace Macocci7\PurephpValidation;

class FileSystem
{
    /**
     * copies a file
     *
     * @param   string  $from
     * @param   string  $to
     * @return  void
     * @thrown  \Exception
     */
    public static function copy(string $from, string $to)
    {
        if (!is_readable($from)) {
            throw new \Exception("Cannot read file {$from}.");
        }
        if (!is_writable($to)) {
            throw new \Exception("Cannot write to {$to}.");
        }
        if (!copy($from, $to)) {
            throw new \Exception("Cannot copy {$from} to {$to}.");
        }
        echo sprintf(
            "Copying%s- From: %s%s-   To: %s%sdone.%s",
            PHP_EOL,
            $from,
            PHP_EOL,
            $to,
            PHP_EOL,
            PHP_EOL,
        );
    }
}
