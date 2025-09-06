window.IELTSBoard = window.IELTSBoard || {};

IELTSBoard.dictation = function (container, itemId) {
    var apiRoot = (window.wpApiSettings && wpApiSettings.root) ? wpApiSettings.root : '/wp-json/';
    var nonce = (window.wpApiSettings && wpApiSettings.nonce) ? wpApiSettings.nonce : '';
    var state = { text: '' };

    container.innerHTML = '' +
        '<div class="ielts-audio">' +
            '<button class="ielts-play" type="button">Play</button>' +
            '<button class="ielts-rewind" type="button">Rewind 5s</button>' +
            '<audio class="ielts-audio-el" preload="auto" style="display:none"></audio>' +
        '</div>' +
        '<textarea class="ielts-input" rows="6"></textarea>' +
        '<div class="ielts-word-count">0 words</div>' +
        '<button class="ielts-check" type="button">Check</button>' +
        '<div class="ielts-result"></div>';

    var audio = container.querySelector('.ielts-audio-el');
    var playBtn = container.querySelector('.ielts-play');
    var rewindBtn = container.querySelector('.ielts-rewind');
    var textarea = container.querySelector('.ielts-input');
    var wordCount = container.querySelector('.ielts-word-count');
    var checkBtn = container.querySelector('.ielts-check');
    var resultEl = container.querySelector('.ielts-result');

    // Fetch payload to start session
    fetch(apiRoot + 'ielts/v1/session/start', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': nonce
        },
        body: JSON.stringify({ id: itemId })
    })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.ok && res.data) {
                state.text = res.data.text || '';
                if (res.data.audio) {
                    audio.src = res.data.audio;
                }
            } else if (res.error) {
                resultEl.textContent = res.error;
            }
        });

    playBtn.addEventListener('click', function () {
        if (audio.paused) {
            audio.play();
            playBtn.textContent = 'Pause';
        } else {
            audio.pause();
            playBtn.textContent = 'Play';
        }
    });

    rewindBtn.addEventListener('click', function () {
        audio.currentTime = Math.max(0, audio.currentTime - 5);
    });

    textarea.addEventListener('input', function () {
        var words = textarea.value.trim().split(/\s+/).filter(Boolean);
        wordCount.textContent = words.length + ' words';
    });

    checkBtn.addEventListener('click', function () {
        var answer = textarea.value;
        fetch(apiRoot + 'ielts/v1/session/finish', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': nonce
            },
            body: JSON.stringify({ id: itemId, answer: answer })
        })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.ok && res.data) {
                    var wer = res.data.wer ? (res.data.wer * 100).toFixed(1) : '0';
                    resultEl.innerHTML = '<p>WER: ' + wer + '%</p><p>' + state.text + '</p>';
                } else if (res.error) {
                    resultEl.textContent = res.error;
                }
            });
    });
};
