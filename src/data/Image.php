<?php

use ProminentColor\PixelCount\WidthPixelCount;
use ProminentColor\ProminentColor;

require_once __DIR__ . "/ImageState.php";
require_once __DIR__ . "/Color.php";
require_once __DIR__ . "/Theme.php";
require_once __DIR__ . "/../session/SessionViewType.php";
require_once __DIR__ . "/../bezier.php";

require_once __DIR__ . "/Database.php";

import("prominent-color");



class Image implements JsonSerializable {
    public static function from(string $str): self {
        [$dir, $file, $theme, $color_str, $view_type] = explode("/", trim($str));
        $color = Color::from($color_str);

        return new Image(
            is_null($color)
                ? ImageState::UNDOCUMENTED
                : ImageState::DOCUMENTED,
            intval($dir),
            $file,
            Theme::tryFrom($theme),
            $color,
            SessionViewType::tryFrom($view_type)
        );
    }



    public function __construct(
        public ImageState $state,
        public int $dir,
        public string $file,
        public ?Theme $theme,
        public ?Color $color,
        public ?SessionViewType $view_type,
    ) {}



    public function __toString(): string {
        $type = $this->view_type?->value ?? '';
        $theme = $this->theme?->value ?? '';

        return "$this->dir/$this->file/$theme/$this->color/$type";
    }

    public function stringify(): string {
        return $this->__toString();
    }

    public function key(): string {
        return "$this->dir/$this->file";
    }

    public function path(): string {
        return FILE_DIRECTORIES[$this->dir]. "/$this->file";
    }



    /**
     * @throws Exception
     */
    public function document(): void {
        if ($this->state === ImageState::DOCUMENTED) {
            return;
        }

        ob_start(); // ignore warnings
        $src = FILE_DIRECTORIES[$this->dir] . "/" . $this->file;
        $pixels = new WidthPixelCount(64);
        $image = \ProminentColor\Image::create($src, $pixels);

        $extracted = ProminentColor::extract($image, 8);

        $imageLightness = 0;
        $count = 0;
        $max_score = 0;

        foreach ($extracted as $centroid) {
            $n = count($centroid->connections);
            $count += $n;

            $color = Color::from($centroid->point);
            [Color::SATURATION => $s, Color::LIGHTNESS => $l] = $color->sl();

            $imageLightness += $l * $n;
            $score = self::pixel_score($s, $l) * $n;

            if ($score >= $max_score) {
                $max_score = $score;
                $this->color = $color;
            }
        }

        $avg = $imageLightness / $count;
        $this->theme = $avg > 0.5
            ? Theme::LIGHT
            : Theme::DARK;

        Database::insert(DATA_FILE, $this);
        ob_end_clean();
    }

    private static function pixel_score(float $saturation, float $lightness): float {
        return bezier(($lightness > 0.5 ? 1 - $lightness : $lightness) * 2)
            * bezier($saturation);
    }



    public function jsonSerialize(): array {
        return [
            "dir" => $this->dir,
            "file" => $this->file,
            "theme" => $this->theme?->value,
            "color" => "$this->color",
        ];
    }
}
