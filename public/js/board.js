(function(){
  'use strict';

  // ---- Helpers -------------------------------------------------------------
  const Vars = (window.ExamBoardVars||{});
  const Log  = (type, payload)=>{ try{ window.ExamBoardLog && window.ExamBoardLog(type, payload||{}); }catch(e){} };
  const qs   = (sel,root=document)=>root.querySelector(sel);
  const ce   = (tag, props={})=>Object.assign(document.createElement(tag), props);
  const on   = (el,ev,fn)=>el&&el.addEventListener(ev,fn);

  const t    = (s)=>s; // i18n placeholder; texts passed via PHP if needed

  // ---- State machine (prevents TTS & Record overlap) -----------------------
  const State = {
    mode: 'idle', // idle | tts | recording | playback
    set(next){
      this.mode = next;
      Log('state', { mode: next });
      updateControls();
    }
  };

  // ---- Library (listening bank) -------------------------------------------
  // Will try to fetch /public/library/listening.json; else use fallback demo.
  async function loadLibrary(){
    const fallback = [
      { id:'s1', title:'Airport – Check-in', lang:'en', text:'Can I see your passport, please?', url:'' },
      { id:'s2', title:'Hotel – Booking', lang:'en', text:'I would like to reserve a double room.', url:'' },
      { id:'s3', title:'IELTS – Part 1', lang:'en', text:'What do you do in your free time?', url:'' }
    ];
    try{
      const base = Vars.pluginUrl || '';
      if(!base) return fallback;
      const res = await fetch(base + 'public/library/listening.json', { cache:'no-store' });
      if(!res.ok) return fallback;
      const data = await res.json();
      // expected shape: [{id,title,lang,text,url}]
      return Array.isArray(data) ? data : fallback;
    }catch(e){ return fallback; }
  }

  // ---- Audio: TTS + File playback + Recording -----------------------------
  let synth = window.speechSynthesis || null;
  let mediaRecorder = null;
  let chunks = [];
  let playback = new Audio(); // used for library file playback
  let currentBlobUrl = null;

  function speak(text, lang){
    if(!synth){ alert('Speech Synthesis not supported'); return; }
    try{
      // stop any playback/recording
      stopRecording(true);
      stopPlayback();
      synth.cancel();
      const u = new SpeechSynthesisUtterance(text);
      if(lang) u.lang = lang;
      State.set('tts');
      u.onend = ()=> State.set('idle');
      synth.speak(u);
      Log('tts_play', { len: (text||'').length, lang });
    }catch(e){
      State.set('idle');
    }
  }

  async function startRecording(){
    try{
      if(!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia){
        alert('Recording not supported'); return;
      }
      // stop TTS / playback
      if(synth){ synth.cancel(); }
      stopPlayback();

      const stream = await navigator.mediaDevices.getUserMedia({ audio:true });
      mediaRecorder = new MediaRecorder(stream);
      chunks = [];
      mediaRecorder.ondataavailable = (e)=>{ if(e.data.size>0) chunks.push(e.data); };
      mediaRecorder.onstop = ()=>{
        const blob = new Blob(chunks, { type:'audio/webm' });
        currentBlobUrl && URL.revokeObjectURL(currentBlobUrl);
        currentBlobUrl = URL.createObjectURL(blob);
        State.set('playback');
        Log('record_stop', { bytes: blob.size });
        // auto show playback panel button state
        const playBtn = qs('[data-eb="playback-play"]');
        if(playBtn){ playBtn.disabled = false; }
      };
      mediaRecorder.start();
      State.set('recording');
      Log('record_start');
    }catch(e){
      alert('Mic permission denied or error.');
      State.set('idle');
    }
  }
  function stopRecording(silent){
    if(mediaRecorder && mediaRecorder.state!=='inactive'){
      mediaRecorder.stop();
      mediaRecorder.stream.getTracks().forEach(tr=>tr.stop());
      mediaRecorder = null;
      if(!silent) State.set('idle');
    }
  }

  function startPlayback(url){
    try{
      if(synth){ synth.cancel(); }
      stopRecording(true);
      playback.pause();
      playback.src = url;
      playback.currentTime = 0;
      playback.onended = ()=> State.set('idle');
      playback.play();
      State.set('playback');
      Log('playback_play', { url });
    }catch(e){
      State.set('idle');
    }
  }
  function stopPlayback(){
    try{
      playback.pause();
      playback.currentTime = 0;
    }catch(_){}
  }

  // ---- Build UI ------------------------------------------------------------
  function buildUI(root){
    root.innerHTML = '';
    root.classList.add('eb-root');

    const dim = ce('div', { className:'eb-dim' });

    // Drawer
    const drawer = ce('aside', { className:'eb-drawer', id:'eb-drawer', 'aria-expanded':'false' });
    const dHead  = ce('div', { className:'eb-drawer__header' });
    const dTgl   = ce('button', { className:'eb-drawer__toggle', type:'button', title:t('Toggle') });
    dTgl.textContent = '⟷';
    const dTitle = ce('div', { className:'eb-drawer__title', textContent:'Tools' });
    dHead.append(dTitle, dTgl);

    const tools  = ce('nav', { className:'eb-tools' });
    const toolList = [
      { id:'typing',   icon:'⌨', label:'Typing' },
      { id:'listen',   icon:'🎧', label:'Listening' },
      { id:'speak',    icon:'🎤', label:'Speaking' },
      { id:'image',    icon:'🖼', label:'Describe' },
      { id:'timer',    icon:'⏱', label:'Timer' }
    ];
    toolList.forEach(item=>{
      const li = ce('div', { className:'eb-tool', tabIndex:0, role:'button', 'data-tool':item.id });
      li.innerHTML = `<div class="eb-tool__icon">${item.icon}</div><div class="eb-tool__label">${item.label}</div>`;
      on(li,'click',()=> activateTool(item.id));
      tools.append(li);
    });

    drawer.append(dHead, tools);

    // Stage
    const stage = ce('section', { className:'eb-stage' });

    const sHead = ce('div', { className:'eb-stage__header' });
    const title = ce('div', { className:'eb-title', textContent:'ExamBoard — IELTS & PTE' });
    const actions = ce('div', { className:'eb-actions' });
    const btnOpenDrawer = ce('button', { className:'eb-btn', textContent:'Menu', type:'button' });
    on(btnOpenDrawer,'click',()=> toggleDrawer(true));
    actions.append(btnOpenDrawer);

    sHead.append(title, actions);

    const main  = ce('div', { className:'eb-stage__main' });
    const work  = ce('div', { className:'eb-work' });
    const ta    = ce('textarea', { className:'eb-textarea', placeholder:'Type here…', id:'eb-text' });

    // work actions
    const workActions = ce('div', { className:'eb-actions' });
    const btnTTS = ce('button', { className:'eb-btn eb-btn--primary', textContent:'Play (TTS)', type:'button', 'data-eb':'tts' });
    const btnRec = ce('button', { className:'eb-btn eb-btn--danger',  textContent:'Record', type:'button', 'data-eb':'rec' });
    const btnStop= ce('button', { className:'eb-btn', textContent:'Stop', type:'button', 'data-eb':'stop' });
    const btnSave= ce('button', { className:'eb-btn eb-btn--success', textContent:'Save Progress', type:'button', 'data-eb':'save' });
    workActions.append(btnTTS, btnRec, btnStop, btnSave);

    const metrics = ce('div', { className:'eb-metrics' });
    metrics.innerHTML = `
      <span class="eb-badge" data-m="wpm">WPM: 0</span>
      <span class="eb-badge" data-m="acc">Accuracy: 100%</span>
      <span class="eb-badge" data-m="err">Errors: 0</span>
    `;

    work.append(ta, workActions, metrics);

    // side panel: Listening Library
    const panel = ce('aside', { className:'eb-panel' });
    const pTitle= ce('div', { className:'eb-panel__title', textContent:'Listening Library' });
    const pList = ce('div', { className:'eb-panel__list', id:'eb-lib' });
    const pBar  = ce('div', { className:'eb-actions' });

    const fileInp = ce('input', { type:'file', accept:'audio/*', style:'display:none', id:'eb-file' });
    const btnAdd  = ce('button', { className:'eb-btn', type:'button', textContent:'Add audio (local)' });
    on(btnAdd, 'click', ()=> fileInp.click());
    on(fileInp, 'change', (e)=>{
      const f = e.target.files[0]; if(!f) return;
      const url = URL.createObjectURL(f);
      addLibraryItem({ id:'local-'+Date.now(), title:f.name, lang: Vars.lang||'en', text:'', url }, pList, ta);
    });

    pBar.append(btnAdd, fileInp);

    panel.append(pTitle, pList, pBar);

    main.append(work, panel);

    const sFoot = ce('div', { className:'eb-actions' });
    const info  = ce('div', { className:'eb-badge', textContent:'Ready' });
    sFoot.append(info);

    stage.append(sHead, main, sFoot);

    // Shell
    const shell = ce('div', { className:'eb-shell' });
    shell.append(drawer, stage, dim);
    root.append(shell);

    // Events
    on(dTgl, 'click', ()=> toggleDrawer());
    on(dim, 'click', ()=> toggleDrawer(false));
    on(btnTTS, 'click', ()=> speak(ta.value, Vars.lang||'en'));
    on(btnRec, 'click', ()=> startRecording());
    on(btnStop,'click', ()=> { if(synth) synth.cancel(); stopRecording(); stopPlayback(); State.set('idle'); });
    on(btnSave,'click', ()=> saveProgress(ta.value, metrics));

    // Keyboard focus on editor
    ta.focus();

    // Initial load library
    loadLibrary().then(items=>{
      items.forEach(it=> addLibraryItem(it, pList, ta));
    });

    // expose for other scripts (optional)
    window.ExamBoardUI = { toggleDrawer, activateTool, speak, startRecording, stopRecording, startPlayback };
  }

  function toggleDrawer(force){
    const d = qs('#eb-drawer');
    const dim = qs('.eb-dim');
    const open = typeof force==='boolean' ? force : !d.classList.contains('is-open');
    d.classList.toggle('is-open', open);
    d.setAttribute('aria-expanded', open?'true':'false');
    const isMobile = matchMedia('(max-width: 768px)').matches;
    if(isMobile){ dim && dim.classList.toggle('is-visible', open); }
  }

  function activateTool(id){
    document.querySelectorAll('.eb-tool').forEach(el=>{
      el.classList.toggle('is-active', el.getAttribute('data-tool')===id);
    });
    // future: switch small helper widgets per tool
    Log('tool_switch', { id });
  }

  function addLibraryItem(item, listEl, editor){
    const row = ce('div', { className:'eb-item' });
    const icon= ce('div', { className:'eb-tool__icon', innerHTML:'🎵' });
    const meta= ce('div', { className:'eb-item__meta' });
    const title=ce('div', { className:'eb-item__title', textContent:item.title||'Audio' });
    const sub  =ce('div', { textContent: (item.lang||'') });
    meta.append(title, sub);

    const btns = ce('div', { className:'eb-item__btns' });
    const bPlay= ce('button', { className:'eb-btn', textContent:'Play', type:'button' });
    const bUse = ce('button', { className:'eb-btn eb-btn--primary', textContent:'Use', type:'button' });

    on(bPlay,'click', ()=>{
      if(item.url){ startPlayback(item.url); }
      else if(item.text){ speak(item.text, item.lang||Vars.lang||'en'); } // fallback to TTS
    });
    on(bUse,'click', ()=>{
      if(item.text){ editor.value = item.text; editor.focus(); }
      if(item.url){ Log('lib_select', { id:item.id }); }
    });

    row.append(icon, meta, btns);
    btns.append(bPlay, bUse);
    listEl.append(row);
  }

  function updateControls(){
    const mode = State.mode;
    const bTTS = qs('[data-eb="tts"]');
    const bRec = qs('[data-eb="rec"]');
    const bStop= qs('[data-eb="s]()
