<?php



readonly class BackgroundImage {
    public function __construct(
        public int $index,
        public string $file,
    ) {}



    public function path(): string {
        return FILE_DIRECTORIES[$this->index]. "/$this->file";
    }

    public function undocumented(): Image {
        return new Image(
            ImageState::UNDOCUMENTED,
            $this->index,
            $this->file,
            null,
            null,
            SessionViewType::ALLOW_NSFW
        );
    }
}