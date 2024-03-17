chrome.runtime.onStartup.addListener(async () => {
    const request = new URL("http://localhost/newtab/api/session.php");
    request.searchParams.set("m", "r"); // mode: "reset"
    request.searchParams.set("sid", String(performance.timeOrigin));

    await fetch(request);
});
