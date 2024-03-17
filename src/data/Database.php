<?php

require_once __DIR__ . "/Image.php";

class Database {
    /** @var array<array<string, Image>>|null $cache */
    private static ?array $cache;
    private static bool $has_changed = false;



    /**
     * @param string $file
     * @return array<Image>
     */
    static function load(string $file): array {
        if (isset(self::$cache[$file])) {
            return self::$cache[$file];
        }

        $ret = [];
        $fp = fopen($file, "r");

        while (true) {
            $line = fgets($fp);

            if ($line === false) {
                break;
            }

            $image = Image::from($line);
            $ret[$image->path()] = $image;
        }

        self::$cache[$file] = $ret;
        return $ret;
    }

    static function insert(string $file, Image $image): void {
        if (!isset(self::$cache[$file])) {
            self::load($file);
        }

        self::$cache[$file][$image->path()] = $image;
        self::$has_changed = true;
    }

    static function save(): void {
        if (!self::$has_changed) {
            return;
        }

        foreach (self::$cache as $file => $images) {
            $fp = fopen($file, "w");
            if ($fp === null) {
                continue;
            }

            foreach ($images as $image) {
                fwrite($fp, $image->stringify() . "\n");
            }

            fclose($fp);
        }

        self::$has_changed = false;
    }
}
