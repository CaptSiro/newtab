<?php

require_once __DIR__ . "/SessionViewType.php";
require_once __DIR__ . "/../constants.php";



class Session implements JsonSerializable {
    public static function load(): Session {
        if (!file_exists(SESSION_FILE)) {
            return self::default();
        }

        return self::parse(file_get_contents(SESSION_FILE));
    }

    public static function default(): Session {
        return new Session("0", SessionViewType::SFW);
    }

    public static function parse(string $stringify): Session {
        $array = json_decode($stringify, true);

        return new self(
            $array["id"],
            SessionViewType::from($array["view-type"])
        );
    }



    public string $id;
    public SessionViewType $view_type;



    public function __construct(string $id, SessionViewType $view_type) {
        $this->id = $id;
        $this->view_type = $view_type;
    }



    public function save(): void {
        file_put_contents(SESSION_FILE, json_encode($this));
    }



    public function jsonSerialize(): array {
        return [
            "id" => $this->id,
            "view-type" => $this->view_type->value
        ];
    }
}
