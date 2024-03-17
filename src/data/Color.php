<?php



readonly class Color {
    /**
     * @param string|array<float> $x
     * @return ?self
     */
    public static function from(string|array $x): ?self {
        $values = [];

        if (is_array($x)) {
            $values = array_map(fn($float) => intval($float), $x);
        }

        if (is_string($x)) {
            if ($x === "") {
                return null;
            }

            $values = self::parse($x);
        }

        return new Color(...$values);
    }

    /**
     * @param string $template
     * @return array<int>
     */
    private static function parse(string $template): array {
        return array_map(
            fn($x) => intval($x),
            explode(",", $template)
        );
    }



    public function __construct(
        public int $red,
        public int $green,
        public int $blue
    ) {}



    public function __toString(): string {
        return "$this->red,$this->green,$this->blue";
    }

    public function lightness(): float {
        return (0.21 * $this->red
            + 0.72 * $this->green
            + 0.07 * $this->blue) / 255;
    }



    const SATURATION = 0;
    const LIGHTNESS = 1;

    public function sl(): array {
        $max = max($this->red, $this->green, $this->blue);
        $min = min($this->red, $this->green, $this->blue);
        $x = ($max - $min) / 255;

        $lightness = ($max + $min) / 510;
        $saturation = $lightness > 0 && $lightness < 1
            ? ($x / (1 - abs(2 * $lightness - 1)))
            : $lightness;

        return [
            Color::SATURATION => $saturation,
            Color::LIGHTNESS => $lightness,
        ];
    }
}