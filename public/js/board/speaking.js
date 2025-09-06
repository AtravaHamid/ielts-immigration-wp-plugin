window.IELTSBoard = window.IELTSBoard || {};

IELTSBoard.speaking = function (container, itemId) {
    var apiRoot = (window.wpApiSettings && wpApiSettings.root) ? wpApiSettings.root : '/wp-json/';
    var nonce = (window.wpApiSettings && wpApiSettings.nonce) ? wpApiSettings.nonce : '';
    var mediaRecorder;
    var chunks = [];
    var timer = null;
    var seconds = 0;

    container.innerHTML = '' +
        '<button class="ielts-record" type="button">Record</button>' +
        '<div class="ielts-timer">0s</div>' +
        '<audio class="ielts-playback" controls style="display:none"></audio>';

    var recordBtn = container.querySelector('.ielts-record');
    var timerEl = container.querySelector('.ielts-timer');
    var playback = container.querySelector('.ielts-playback');

    recordBtn.addEventListener('click', function () {
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
            recordBtn.disabled = true;
        } else {
            navigator.mediaDevices.getUserMedia({ audio: true }).then(function (stream) {
                mediaRecorder = new MediaRecorder(stream);
                chunks = [];
                mediaRecorder.ondataavailable = function (e) {
                    if (e.data.size > 0) {
                        chunks.push(e.data);
                    }
                };
                mediaRecorder.onstop = function () {
                    clearInterval(timer);
                    timer = null;
                    recordBtn.disabled = false;
                    recordBtn.textContent = 'Record';
                    seconds = 0;
                    timerEl.textContent = '0s';

                    var blob = new Blob(chunks, { type: 'audio/webm' });
                    var fd = new FormData();
                    fd.append('file', blob, 'speaking.webm');
                    fd.append('item', itemId);
                    fetch(apiRoot + 'ielts/v1/speaking', {
                        method: 'POST',
                        headers: { 'X-WP-Nonce': nonce },
                        body: fd
                    })
                        .then(function (r) { return r.json(); })
                        .then(function (res) {
                            if (res.ok && res.url) {
                                playback.src = res.url;
                                playback.style.display = 'block';
                            }
                        });
                };
                mediaRecorder.start();
                recordBtn.textContent = 'Stop';
                timer = setInterval(function () {
                    seconds++;
                    timerEl.textContent = seconds + 's';
                }, 1000);
            });
        }
    });
};
