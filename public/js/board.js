(function(){
  const $ = (sel, ctx=document) => ctx.querySelector(sel);
  const $$ = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));
  const root = $('#exam-board'); if(!root) return;

  // Tabs
  $$('.eb-tab', root).forEach(btn=>{
    btn.addEventListener('click', ()=>{
      $$('.eb-tab', root).forEach(b=>b.classList.remove('is-active'));
      btn.classList.add('is-active');
      const target = btn.dataset.target;
      $$('.eb-panel', root).forEach(p=>p.classList.remove('is-active'));
      $('#eb-panel-'+target.replace(/_/g,'-'), root).classList.add('is-active');
    });
  });

  // Typing metrics
  const typingPanel = $('#eb-panel-typing', root);
  if (typingPanel){
    const target = $('.eb-target', typingPanel);
    const input  = $('.eb-input', typingPanel);
    const wpmEl = $('.wpm', typingPanel), accEl = $('.acc', typingPanel), errsEl = $('.errs', typingPanel);
    let startedAt = null, totalErrs = 0;

    const compute = ()=>{
      const t = target.value;
      const v = input.value;
      let errs = 0;
      const len = Math.min(t.length, v.length);
      for(let i=0;i<len;i++){ if(t[i]!==v[i]) errs++; }
      errs += Math.max(0, v.length - t.length);
      totalErrs = errs;
      const words = v.trim().length ? v.trim().split(/\s+/).length : 0;
      const mins = startedAt ? (Date.now()-startedAt)/60000 : 0.001;
      const wpm = Math.round(words / mins);
      const acc = t.length ? Math.max(0, Math.round(100*(1 - errs/Math.max(t.length,1)))) : 100;
      wpmEl.textContent = isFinite(wpm)? wpm : 0;
      accEl.textContent = acc+'%';
      errsEl.textContent = errs;
    };

    input.addEventListener('input', ()=>{
      if(!startedAt && input.value.length>0) startedAt = Date.now();
      compute();
    });
    target.addEventListener('input', compute);

    $('.eb-reset', typingPanel).addEventListener('click', ()=>{
      input.value=''; startedAt=null; totalErrs=0; compute();
    });

    $('.eb-save', typingPanel).addEventListener('click', ()=>{
      saveProgress({
        activity: 'typing',
        words: (input.value.trim().match(/\S+/g)||[]).length,
        errors: totalErrs,
        accuracy: parseInt(accEl.textContent),
        wpm: parseInt(wpmEl.textContent)
      });
    });
  }

  // TTS
  const speak = (text, lang=root.dataset.lang||'en')=>{
    if(!window.speechSynthesis) return alert(ExamBoard.i18n.unsupported);
    const utt = new SpeechSynthesisUtterance(text);
    utt.lang = lang;
    speechSynthesis.cancel();
    speechSynthesis.speak(utt);
  };
  $$('#eb-panel-listen-type, #eb-panel-shadowing', root).forEach(panel=>{
    const playBtn = $('.eb-tts-play', panel);
    const stopBtn = $('.eb-tts-stop', panel);
    const txt = $('.eb-tts-text', panel);
    if(playBtn) playBtn.addEventListener('click', ()=> speak(txt.value || ''));
    if(stopBtn) stopBtn.addEventListener('click', ()=> speechSynthesis.cancel());
  });

  // Recording (MediaRecorder)
  let mediaStream = null, mediaRecorder = null, chunks = [];
  const recToggles = $$('.eb-rec-toggle', root);
  recToggles.forEach(btn=>{
    btn.addEventListener('click', async ()=>{
      const panel = btn.closest('.eb-panel');
      const play = $('.eb-playback', panel);
      const uploadBtn = $('.eb-upload', panel);
      if(!mediaRecorder || mediaRecorder.state==='inactive'){
        try{
          mediaStream = await navigator.mediaDevices.getUserMedia({audio:true});
          mediaRecorder = new MediaRecorder(mediaStream);
          chunks = [];
          mediaRecorder.ondataavailable = e => { if(e.data.size>0) chunks.push(e.data); };
          mediaRecorder.onstop = ()=>{
            const blob = new Blob(chunks, {type: 'audio/webm'});
            play.src = URL.createObjectURL(blob);
            play.style.display='block';
            uploadBtn.disabled = false;
            btn.textContent = 'Record';
          };
          mediaRecorder.start();
          btn.textContent = ExamBoard.i18n.recording;
        } catch(e){
          alert(ExamBoard.i18n.unsupported);
        }
      }else{
        mediaRecorder.stop();
        mediaStream.getTracks().forEach(t=>t.stop());
      }
    });
  });

  // Upload audio
  $$('.eb-upload', root).forEach(up=>{
    up.addEventListener('click', async ()=>{
      const panel = up.closest('.eb-panel');
      const play = $('.eb-playback', panel);
      const res = await fetch(play.src);
      const blob = await res.blob();
      up.textContent = ExamBoard.i18n.uploading;
      up.disabled = true;
      try{
        const base64 = await blobToBase64(blob);
        const r = await api('upload-audio', { filename: 'recording.webm', mime: blob.type, data: base64 });
        up.textContent = 'Uploaded';
        if(r && r.url) play.dataset.uploadUrl = r.url;
      }catch(e){
        up.textContent = 'Retry Upload'; up.disabled = false;
      }
    });
  });

  function blobToBase64(blob){
    return new Promise((res,rej)=>{
      const r = new FileReader();
      r.onloadend = ()=> res((r.result||'').toString().split(',')[1]||'');
      r.onerror = rej;
      r.readAsDataURL(blob);
    });
  }

  // Image drop
  const descPanel = $('#eb-panel-describe-image', root);
  if(descPanel){
    const box = $('.eb-image-drop', descPanel);
    const input = $('.eb-image-input', descPanel);
    const preview = $('.eb-preview', descPanel);
    const open = ()=> input.click();
    const show = file=>{
      const url = URL.createObjectURL(file);
      preview.src = url; preview.style.display='block';
    };
    ;['click'].forEach(ev=> box.addEventListener(ev, open));
    input.addEventListener('change', e=> { const f=e.target.files[0]; if(f) show(f); });
    ;['dragover','dragenter'].forEach(ev=> box.addEventListener(ev, e=> { e.preventDefault(); box.classList.add('drag'); }));
    ;['dragleave','drop'].forEach(ev=> box.addEventListener(ev, e=> { e.preventDefault(); box.classList.remove('drag'); }));
    box.addEventListener('drop', e=> { const f=e.dataTransfer.files[0]; if(f && f.type.startsWith('image/')) show(f); });
  }

  // Timer
  const timerPanel = $('#eb-panel-timer', root);
  if(timerPanel){
    const minsInput=$('.eb-minutes',timerPanel), start=$('.eb-timer-start',timerPanel), stop=$('.eb-timer-stop',timerPanel), cd=$('.eb-countdown',timerPanel);
    let remain=0, int=null;
    const fmt=s=> String(Math.floor(s/60)).padStart(2,'0')+':'+String(s%60).padStart(2,'0');
    start.addEventListener('click', ()=>{
      remain = Math.max(1, parseInt(minsInput.value,10)) * 60;
      cd.textContent = fmt(remain);
      start.disabled=true; stop.disabled=false;
      int = setInterval(()=>{
        remain--; cd.textContent = fmt(Math.max(0,remain));
        if(remain<=0){ clearInterval(int); start.disabled=false; stop.disabled=true; }
      }, 1000);
    });
    stop.addEventListener('click', ()=>{ clearInterval(int); start.disabled=false; stop.disabled=true; });

    $('.eb-save-session',timerPanel).addEventListener('click', ()=>{
      saveProgress({
        activity:'session',
        words: parseInt($('.words',timerPanel).textContent)||0,
        errors: parseInt($('.errors',timerPanel).textContent)||0,
        listen_mins: parseInt($('.listen-mins',timerPanel).textContent)||0,
      });
    });
  }

  // API helpers
  async function api(endpoint, payload){
    const r = await fetch(ExamBoard.rest.url + endpoint, {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'X-WP-Nonce': ExamBoard.rest.nonce
      },
      body: JSON.stringify(payload||{})
    });
    if(!r.ok) throw new Error('Request failed');
    return await r.json();
  }

  async function saveProgress(metrics){
    try{
      await api('progress', { metrics });
      alert(ExamBoard.i18n.saved);
    }catch(e){ alert('Save failed'); }
  }
})();
