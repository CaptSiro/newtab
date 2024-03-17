<?php

function is_available(Image $image, Request $req, Session $session): bool {
    return $image->theme->value === $req->body->theme
        && SessionViewType::image_view_test($image, $session->view_type);
}

/**
 * @param array<Image> $filtered
 * @param int $start
 * @param Request $req
 * @param Session $session
 * @return ?Image
 * @throws Exception
 */
function find_available(array $filtered, int $start, Request $req, Session $session): ?Image {
    $len = count($filtered);
    $i = $start;
    $try = 0;

    do {
        if ($i === $len) {
            $i = 0;
        }

        if ($filtered[$i]->state === ImageState::UNDOCUMENTED) {
            $filtered[$i]->document();
        }

        if (is_available($filtered[$i], $req, $session)) {
            return $filtered[$i];
        }

        $i++;

        if (++$try > MAX_IMAGE_TRIES) {
            break;
        }
    } while ($i !== $start);

    return null;
}
