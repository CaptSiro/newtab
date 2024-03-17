<?php

  class Path {
    /**
     * Translates path that is relative to project-manager directory to current working directory 
     */
    static function translate (string $p): string {
      $cwd = getcwd();
      $cwdCount = count(explode(
        (strpos($cwd, "/") !== false)
          ? "/"
          : "\\",
        $cwd
      ));
  
      // relative path instead of absolute
      $root = $_SERVER["DOCUMENT_ROOT"];
      $projectDirCount = count(explode("/", "$root/project-manager"));
  
      return str_repeat("../", $cwdCount - $projectDirCount) . $p;
    }

    static function breakdown (string $path): array {
      $separator = (strpos($path, "/") !== false)
        ? "/"
        : "\\";

      return explode($separator, $path);
    }
  }