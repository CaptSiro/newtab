<?php

require_once __DIR__ . "/../src/fs.php";

$backgrounds = backgrounds();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <script>
        function image (query) {
            const img = document.createElement("img");

            const source = new URL(`http://localhost/newtab/api/get-file.php?${query}`);

            img.src = source.href;
            return img;
        }

        function dark (col) {
            const dark = document.createElement("div");
            const span = document.createElement("span");
            // span.style.background = "rgb(" + col + ")";
            span.style.padding = "8px";
            span.innerHTML = "Dark theme";
            dark.classList.add("d");
            dark.append(span);
            return dark;
        }

        function light (col) {
            const light = document.createElement("div");
            const span = document.createElement("span");
            // span.style.background = "rgb(" + col + ")";
            span.style.padding = "8px";
            span.innerHTML = "Light theme";
            light.classList.add("l");
            light.append(span);
            return light;
        }

        function color (col) {
            const div = document.createElement("div");
            const p = document.createElement("p");
            p.innerText = col;

            div.style.background = "rgb(" + col + ")";
            div.append(p);
            return div;
        }
    </script>

    <style>
        body {
            min-height: 100vh;
            min-width: 100vw;
            background-color: #262A33;
            overflow-x: hidden;
        }

        * {
            padding: 0px;
            margin: 0px;
        }

        ul li {
            display: flex;
            list-style-type: none;
            margin-top: 10px;
            margin-left: 10px;
        }

        ul li > * {
            width: 160px;
            height: 100px;
            object-fit: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 15px;
            margin-right: 10px;
        }

        ul li > * p {
            /*background-color: #262A33;*/
            color: #eef;
            padding: 4px;
        }

        .l {
            background-color: #eef;
            color: #262A33;
        }

        .d {
            background-color: black;
            color: #eef;
        }
    </style>
</head>
<body>
    <ul style="display: none;">
        <?php foreach ($backgrounds as $bg) { ?>
            <li class="link"><?= $bg->index ?>/<?= $bg->file ?></li>
        <?php } ?>
    </ul>
    <ul class="append"></ul>
</body>

<script>
    const showcase = document.querySelector(".append");

    function load (index, file) {
        return new Promise(async resolve => {
            const res = await fetch("http://localhost/newtab/api/document.php?i=" + index + "&file=" + encodeURIComponent(file));
            res.json().then(object => {
                const li = document.createElement("li");
                console.log(object);

                const query = new URLSearchParams();
                query.set("dir", index);
                query.set("file", file);

                li.append(image(query), object.theme === "l"
                        ? light(object.avgColor)
                        : dark(object.avgColor),
                    color(object.color));

                if (showcase.children.length === 0) {
                    showcase.append(li);
                } else {
                    showcase.insertBefore(li, showcase.children[0]);
                }

                resolve();
            });
        });
    }

    const links = document.querySelectorAll(".link");

    (async () => {
        for (let i = 509; i < links.length; i++) {
            console.log("Loading: (" + (i + 1) + "/" + links.length + ") " + links[i].innerHTML.replaceAll("&amp;", "&"));
            console.time("image(" + (i + 1) + ")");
            await load (...links[i].innerHTML.replaceAll("&amp;", "&").split("/"));
            console.timeEnd("image(" + (i + 1) + ")");
        }
    })();
</script>
</html>