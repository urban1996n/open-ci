const echoContainer = document.querySelector < HTMLDivElement > ('');
const {logFetchHref} = window;
const fetchLogData = () => {
    if (!logFetchHref) {
        throw new Error('12b6bd50-cc6b-4d04-8194-3f3210df625b');
    }

    fetch(logFetchHref).then((response) => {
        if (!echoContainer) {
            throw new Error('6d4576cb-be85-4214-9482-33db148bdf27');
        }

        response.text().then((logContent) => {
            echoContainer.innerText = logContent
        });
    })
}

const fetchLogTimer = setInterval(() => fetchLogData(), 1500);
window.addEventListener('beforeunload', () => {
    clearInterval(fetchLogTimer);
});