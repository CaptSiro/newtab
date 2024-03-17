<?php



enum SessionViewType: string {
    public static function image_view_test(Image $image, self $session_view_type): bool {
        if (is_null($image->view_type) && $session_view_type == SessionViewType::SFW) {
            return false;
        }

        $type = $image->view_type;

        if ($type === SessionViewType::SFW
            && $session_view_type === SessionViewType::ALLOW_NSFW) {
            return true;
        }

        return $type === $session_view_type;
    }



    case SFW = "sfw";
    case ALLOW_NSFW = "allow-nsfw";
    case ONLY_NSFW = "only-nsfw";



    public function humanReadable(): string {
        return match ($this) {
            SessionViewType::SFW => "SFW only",
            SessionViewType::ALLOW_NSFW => "NSFW allowed",
            SessionViewType::ONLY_NSFW => "Only NSFW",
        };
    }
}