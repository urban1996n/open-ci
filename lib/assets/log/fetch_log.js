const {logFetchHref} = window;
const fetchLogData = () => {
    const echoContainer = document.querySelector('.main__log_space');

    if (!logFetchHref) {
        throw new Error('12b6bd50-cc6b-4d04-8194-3f3210df625b');
    }

    if (!echoContainer) {
        throw new Error('6d4576cb-be85-4214-9482-33db148bdf27');
    }

    fetch(logFetchHref).then((response) => {
        if (response.status !== 200) {
            throw new Error('f0e2b5e0-5b18-4ec9-a1f0-2576f344151f');
        }

        response.text().then((text) => {
            echoContainer.innerHTML = text;
        })
    }).catch((error) => {
        throw new Error(error + ' abda2da2-5b18-4ec9-a1f0-2576f344151f');
    });
}

const fetchLogTimer = setInterval(() => fetchLogData(), 1500);
window.addEventListener('beforeunload', () => {
    clearInterval(fetchLogTimer);
});
