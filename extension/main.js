const theme = {
    l: {
        "--theme": "#eef",
        "--theme-transparent": "rgba(238, 238, 255, 0.5)",
        "--theme-opposite": "#191E24",
        "--theme-opposite-2": "#202a31"
    },
    d: {
        "--theme": "#191E24",
        "--theme-transparent": "rgba(13, 20, 30, 0.502)",
        "--theme-opposite": "#eef",
        "--theme-opposite-2": "#ccd"
    },
    contrast: {
        light: "black",
        dark: "white"
    }
};



const clockMain = document.querySelector("div.container.main");
const clockDateCon = document.querySelector(".clockDate");
const linksCon = document.querySelector(".containerLinks");
const grid = document.querySelector(".links");
const bgImage = document.querySelector("img.bgImage");
const contextMenus = {
    edit: document.querySelector(".edit-link"),
    body: document.querySelector("body > .add-background")
};
contextMenus.edit.reset = function () {
    this.classList.remove("display");
    bcmInput.value = "";
};
contextMenus.body.reset = function () {
    this.classList.remove("display");
};
const bcmInput = contextMenus.body.querySelector("input");
const bcmSubmit = contextMenus.body.querySelector(".submit");
const genshinLogin = document.querySelector(".genshin-daily-login");
const genshinDone = genshinLogin.querySelector(".done");
const genshinTimerCon = genshinLogin.querySelector(".timer > h1");
const genshinTimerLink = genshinLogin.querySelector("h3");
let eventTarget, height, currentBackgroundIndex;
const addBtn = document.querySelector("button.add");

const getFromLocalStorage = (key, _default) => JSON.parse(localStorage.getItem(key)) ?? _default;
const array = getFromLocalStorage("array", []);
const urls = getFromLocalStorage("backgrounds", ["https://user-images.githubusercontent.com/79382911/152241675-e619ae23-f3ef-40fd-a003-0b56393683fb.jpg"]);

let genshinTimer = new Date(localStorage.getItem("genshinTimer") ?? Date.now());

const weekDay = ["SUN", "MON", "TUE", "WEN", "THU", "FRI", "SAT"];
const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];

// add bookmark variables
const con = document.querySelector("div.form");
const select = document.querySelector("#position");
const inputs = {
    name: con.querySelector("input.name"),
    url: con.querySelector("input.url"),
    color: con.querySelector("input.color")
};

const cancelBtn = con.querySelector("button.cancel");
const confirmBtn = con.querySelector("button.confirm");
const preview = con.querySelector("div.preview");
const previewText = preview.querySelector("h2");

function back() {
    con.classList.remove("display");

    con.querySelectorAll("input").forEach(e => {
        e.value = "";
    });

    preview.style.backgroundColor = "#191E24";
    preview.querySelector("h2").innerText = "Preview";
}



function parseDate(date) {
    const dayInMonth = date.getDate();
    let end;

    if (dayInMonth % 10 == 1) {
        end = "st";
    } else if (dayInMonth % 10 == 2) {
        end = "nd";
    } else if (dayInMonth % 10 == 3) {
        end = "rd";
    } else {
        end = "th";
    }

    return `<span class="weekDay day">${weekDay[date.getDay()]}</span><span class="day">${dayInMonth}${end}</span><span class="month">${months[date.getMonth()]}</span>`;
}

function parseTime(date) {
    let hours = date.getHours();
    let end = "AM";

    if (hours > 12) {
        hours -= 12;
        end = "PM";
    }
    if (hours == 0) {
        hours = 12;
        end = "AM";
    }

    let minutes = date.getMinutes();
    if (minutes / 10 < 1) {
        minutes = "0" + minutes;
    }

    return `<p class="clock"><span class="hours">${hours}</span>${minutes} ${end}</p>`;

}

function insertAllBookmarks() {
    const _grid = document.querySelector(".links");
    const _addBtn = document.querySelector("button.add");
    _addBtn.classList.remove("show");
    _grid.innerHTML = "";
    _grid.appendChild(_addBtn);
    (function loop(i) {
        setTimeout(() => {
            const e = array[i];

            if (e != undefined) {
                const a = document.createElement("a");
                a.href = e.url;
                a.style.backgroundColor = e.color;
                a.addEventListener("contextmenu", e => {
                    contextMenus.edit.classList.add("display");
                    contextMenus.edit.style.left = (e.clientX - 10) + "px";
                    contextMenus.edit.style.top = (e.clientY - 10) + "px";
                    eventTarget = e.target;
                    back();
                    e.preventDefault();
                });

                const h2 = document.createElement("h2");
                h2.innerHTML = e.name;

                a.appendChild(h2);
                grid.insertBefore(a, _addBtn);
                setTimeout(() => {
                    a.classList.add("show");
                }, 10);
            }


            // evaluation for next iteration
            i++;
            if (i < array.length) {
                loop(i);
            } else {
                setTimeout(() => {
                    _addBtn.classList.add("show");
                }, 10);
            }
        }, 100);
    })(0);
}

const isLight = (rgb) => {
    const brightness = ((rgb[0] * 299) + (rgb[1] * 587) + (rgb[2] * 114)) / 1000;
    return brightness > 130;
};
const setRandomBackground = async (attempt = 1) => {
    const MAX_TRIES = 10;

    const d = new Date();

    const randomImage = new URL("http://localhost/newtab/api/get-random.php");
    randomImage.searchParams.set("theme", d.getHours() > 7 && d.getHours() < 19 ? "l" : "d");
    randomImage.searchParams.set("ret", "json");

    const response = await fetch(randomImage);

    if (response.ok) {
        const text = await response.clone().text();

        try {
            const json = await response.json();
            console.log(json); // todo make globally accessible

            for (const variableName in theme[json.theme]) {
                const value = theme[json.theme][variableName];
                document.documentElement.style.setProperty(variableName, value);
            }

            document.documentElement.style.setProperty("--primary-color", `rgb(${json.color})`);
            document.documentElement.style.setProperty(
                "--primary-color-contrast",
                (isLight(json.color.split(",").map(s => +s)))
                    ? theme.contrast.light
                    : theme.contrast.dark
            );

            const source = new URL("http://localhost/newtab/api/get-file.php");
            source.searchParams.set("dir", json.dir);
            source.searchParams.set("file", json.file);

            bgImage.src = source.href;
            bgImage.classList.add("show");
        } catch (error) {
            console.log(error);
            console.log(text);
        }
    } else {
        if (attempt <= MAX_TRIES) {
            setRandomBackground(++attempt);
        }
    }

};
const clamp = (min, max, number) => {
    if (number < min) return min;
    return (number > max) ? max : number;
};
const parseGenshinTimer = () => {
    const result = genshinTimer - Date.now();

    if (genshinTimer > 0) {
        const obj = {
            hours: "",
            minutes: "",
            seconds: ""
        };
        obj.hours = clamp(0, 48, Math.floor(result / 1000 / 60 / 60) % 24);
        obj.minutes = clamp(0, 59, Math.floor(result / 1000 / 60) % 60);
        obj.seconds = clamp(0, 59, Math.floor(result / 1000) % 60);

        for (const k in obj) {
            if (obj[k] < 10) {
                obj[k] = "0" + obj[k];
            }
        }

        genshinTimerLink.style.display = "none";
        return `<span class="hours">${obj.hours}</span>:<span class="minutes">${obj.minutes}</span>:<span class="seconds">${obj.seconds}</span>`;
    }

    genshinTimerLink.style.display = "block";
    return `<span class="hours">00</span>:<span class="minutes">00</span>:<span class="seconds">00</span>`;
};



function getContextMenu(element) {
    if (element === document.body) return null;

    if (Boolean(element?.getAttribute("context-menu") ?? false)) {
        return element;
    }

    return getContextMenu(element.parentElement);
}

window.addEventListener("mousedown", evt => {
    const cxtMenu = getContextMenu(evt.target);

    if (cxtMenu) {
        for (const k in contextMenus) {
            if (contextMenus[k] !== cxtMenu) {
                contextMenus[k].reset();
            }
        }
    } else {
        for (const k in contextMenus) {
            contextMenus[k].reset();
        }
    }
});
window.addEventListener("load", e => {
    const date = new Date();

    clockMain.innerHTML = `${parseTime(date)}
      <p class="date">
        ${parseDate(date)}
      </p>`;

    setRandomBackground().then();

    setTimeout(() => {
        height = ((133 * Math.ceil((array.length + 1) / 3)) + 3);
        height = (height > 535) ? 535 : height;
        linksCon.style.height = height + "px";
        clockDateCon.classList.add("show");

        genshinTimerCon.innerHTML = parseGenshinTimer();
        genshinLogin.classList.add("show");
        document.querySelector(".line").classList.add("show");

        insertAllBookmarks();
    }, 100);
});



const TOAST_SHOW_TIME = 5_000;
const toast = document.querySelector(".toast");
const toastMessage = toast.querySelector(".message");
let toastHideTimeout = undefined;
const KEY_VIEW_TYPE_MAP = {
    "o": "only-nsfw",
    "n": "allow-nsfw",
    "s": "sfw"
};

function showToast(message) {
    toastMessage.textContent = message;
    toast.classList.add("show");

    if (toastHideTimeout !== undefined) {
        clearTimeout(toastHideTimeout);
    }

    toastHideTimeout = setTimeout(() => {
        toast.classList.remove("show");
        toastHideTimeout = undefined;
    }, TOAST_SHOW_TIME);
}

window.addEventListener("keydown", async evt => {
    const key = evt.key.toLowerCase();

    if (evt.shiftKey || evt.altKey || evt.ctrlKey || KEY_VIEW_TYPE_MAP[key] === undefined) {
        return;
    }

    const request = new URL("http://localhost/newtab/api/session.php");
    request.searchParams.set("m", "svt"); // mode: "set-view-type"
    request.searchParams.set("svt", KEY_VIEW_TYPE_MAP[key]);

    const response = await fetch(request);
    showToast("Switched to: " + await response.text());

    await setRandomBackground();
});



genshinDone.addEventListener("click", evt => {
    let d = new Date(Date.now());
    d.setDate(d.getDate() + 1);
    genshinTimer = d;
    localStorage.setItem("genshinTimer", d);

    genshinTimerCon.innerHTML = parseGenshinTimer();

    window.open("https://webstatic-sea.mihoyo.com/ys/event/signin-sea/index.html?act_id=e202102251931481", "_blank");
});



// update clock
setInterval(() => {
    const date = new Date();
    genshinTimerCon.innerHTML = parseGenshinTimer();

    if (date.getSeconds() == 0) {
        clockMain.classList.add("blink");
        setTimeout(() => {
            const date = new Date();
            clockMain.innerHTML = `${parseTime(date)}
      <p class="date">
      ${parseDate(date)}
      </p>`;
        }, 500);
    }

    if (date.setSeconds() == 4) {
        clockMain.classList.remove("blink");
    }
}, 1000);



// contextMenus.edit
const deletebtn = contextMenus.edit.querySelector(".delete");
const editbtn = contextMenus.edit.querySelector(".edit");

function findIndex(string) {
    for (let i = 0; i < array.length; i++) {
        if (array[i].name == string) {
            return i;
        }
    }

    return -1;
}

deletebtn.addEventListener("click", e => {
    if (eventTarget != null) {
        eventTarget.classList.remove("show");
        eventTarget.classList.add("delete");

        setTimeout(() => {
            grid.removeChild(eventTarget);
            const name = eventTarget.querySelector("h2").innerText;

            array.splice(findIndex(name), 1);

            height = ((133 * Math.ceil((array.length + 1) / 3)) + 3);
            height = (height > 535) ? 535 : height;

            linksCon.style.height = height + "px";

            localStorage.setItem("array", JSON.stringify(array));

        }, 500);

    }

    contextMenus.edit.reset();
});

let editIndex;
editbtn.addEventListener("click", e => {
    if (eventTarget != null) {
        con.querySelector("h3").innerText = "Edit bookmark";

        con.classList.add("display");

        select.innerHTML = "";

        array.forEach((e, i) => {
            const option = document.createElement("option");
            option.value = i;
            option.innerText = formatPosition(i);
            select.appendChild(option);
        });

        editIndex = findIndex(eventTarget.querySelector("h2").innerText);
        select.selectedIndex = editIndex;

        inputs.name.value = array[editIndex].name;
        previewText.innerText = array[editIndex].name;

        inputs.url.value = array[editIndex].url;

        inputs.color.value = array[editIndex].color;
        preview.style.backgroundColor = array[editIndex].color;

        confirmBtn.removeEventListener("click", add);
        confirmBtn.addEventListener("click", edit);
    }

    contextMenus.edit.reset();
});



// add a bookmark
function formatPosition(i) {
    let end;

    if (i % 10 == 1) {
        end = "st";
    } else if (i % 10 == 2) {
        end = "nd";
    } else if (i % 10 == 3) {
        end = "rd";
    } else {
        end = "th";
    }

    return (i + 1) + "" + end;
}

addBtn.addEventListener("click", e => {
    back();
    con.querySelector("h3").innerText = "Add new bookmark";
    con.classList.add("display");

    select.innerHTML = "";

    for (let i = 0; i < (array.length + 1); i++) {
        const option = document.createElement("option");
        option.value = i;
        option.innerText = formatPosition(i);
        select.appendChild(option);
    }

    select.selectedIndex = array.length;

    confirmBtn.removeEventListener("click", edit);
    confirmBtn.addEventListener("click", add);
});
cancelBtn.addEventListener("click", back);



inputs.name.addEventListener("input", e => {
    previewText.innerText = inputs.name.value;
});
inputs.url.addEventListener("click", e => {
    inputs.url.setSelectionRange(0, inputs.url.value.length);
});
inputs.color.addEventListener("input", e => {
    preview.style.backgroundColor = inputs.color.value;
});



function edit() {
    let object = {
        name: array[editIndex].name,
        url: array[editIndex].url,
        color: array[editIndex].color
    };

    if (inputs.name.value != "") {
        object.name = inputs.name.value;
    }

    if (inputs.url.value != "") {
        object.url = inputs.url.value;
    }

    if (inputs.color.value != "") {
        object.color = inputs.color.value;
    }


    if (select.value == editIndex) {
        array[editIndex] = object;

        const element = grid.querySelectorAll("a")[editIndex];
        element.href = object.url;

        element.style.backgroundColor = object.color;

        element.querySelector("h2").innerText = object.name;

        localStorage.setItem("array", JSON.stringify(array));

        back();
        return;
    } else {
        array.splice(editIndex, 1);
        array.splice(select.value, 0, object);
    }

    localStorage.setItem("array", JSON.stringify(array));

    back();
    insertAllBookmarks();
}

function add() {
    let object = {
        name: "Name",
        url: "https://www.google.cz/?hl=en",
        color: "#191E24"
    };

    if (inputs.name.value != "") {
        object.name = inputs.name.value;
    }

    if (inputs.url.value != "") {
        object.url = inputs.url.value;
    } else {
        return;
    }

    if (inputs.color.value != "") {
        object.color = inputs.color.value;
    }

    if (select.value == array.length) {
        array.push(object);
    } else {
        array.splice(select.value, 0, object);
    }

    localStorage.setItem("array", JSON.stringify(array));

    back();

    height = ((133 * Math.ceil((array.length + 1) / 3)) + 3);
    height = (height > 535) ? 535 : height;
    linksCon.style.height = height + "px";

    insertAllBookmarks();
}



// add background
const evtPropDistributor = element => {
    //* attributes = "skip-event-propagation" and "pass-event-propagation"
    //                skips intirely               pass down the handler on childern nodes

    if (element.nodeType == Node.TEXT_NODE) {
        return;
    }

    if (element.getAttribute("skip-event-propagation") === "true") {
        return;
    }

    if (element.getAttribute("pass-event-propagation") === "true") {
        element.childNodes.forEach(evtPropDistributor);
        return;
    }

    element.addEventListener("contextmenu", evt => evt.stopPropagation());
    element.addEventListener("click", evt => evt.stopPropagation());
    element.addEventListener("mousedown", evt => evt.stopPropagation());

};
document.body.childNodes.forEach(e => {
    if (e instanceof HTMLElement) {
        evtPropDistributor(e);
    }
});

const urlRegex = RegExp("(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})");
contextMenus.body.querySelector(".submit").addEventListener("click", evt => {
    if (urlRegex.test(bcmInput.value)) {
        urls.push(bcmInput.value);
        localStorage.setItem("backgrounds", JSON.stringify(urls));
        contextMenus.body.reset();
    }
    evt.stopPropagation();
});
contextMenus.body.querySelector(".delete").addEventListener("click", evt => {
    if (urls.length > 1) {
        console.log(urls[currentBackgroundIndex]);
        urls.splice(currentBackgroundIndex, 1);
        localStorage.setItem("backgrounds", JSON.stringify(urls));
        setRandomBackground();
        contextMenus.body.reset();
    }
    evt.stopPropagation();
});
document.body.addEventListener("contextmenu", evt => {
    contextMenus.body.classList.add("display");
    contextMenus.body.style.left = (evt.clientX - 10) + "px";
    contextMenus.body.style.top = (evt.clientY - 10) + "px";
    back();
    evt.preventDefault();
});