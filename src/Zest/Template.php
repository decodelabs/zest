<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest;

use DecodeLabs\Atlas\File;
use DecodeLabs\Hatch\FileTemplate;

class Template extends FileTemplate
{
    protected const string File = 'file.template';

    public function __construct(
        string|File|null $file = null
    ) {
        parent::__construct($file ?? static::File);
    }



    /**
     * @param array<mixed> $data
     */
    protected function exportArray(
        array $data,
        int $indent = 0
    ): string {
        $output = '[';
        $indent++;

        if ($isAssoc = !array_is_list($data)) {
            $output .= "\n";
        }

        foreach ($data as $key => $value) {
            if ($isAssoc) {
                $output .= str_repeat('    ', $indent);
                $output .= var_export($key, true) . ' => ';
            }

            if (is_array($value)) {
                $output .= $this->exportArray($value, $indent);
            } elseif (is_bool($value)) {
                $output .= $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $output .= 'null';
            } else {
                $output .= var_export($value, true);
            }

            $output .= ',';

            if ($isAssoc) {
                $output .= "\n";
            }
        }

        if ($isAssoc) {
            $output .= str_repeat('    ', $indent - 1);
        } else {
            $output = rtrim($output, ',');
        }

        $output .= ']';
        return $output;
    }


    protected function exportJson(
        mixed $data,
        int $indent = 0
    ): string {
        $output = (string)str_replace(
            "\n",
            "\n" . str_repeat('    ', $indent),
            (string)json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $output = (string)preg_replace('/"([^"]+)"\:/', '$1:', $output);
        $output = (string)str_replace('"', "'", $output);
        $output = (string)str_replace(["'`", "`'"], '`', $output);

        return $output;
    }
}
