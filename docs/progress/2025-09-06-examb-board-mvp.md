# Progress Log — 2025-09-06

## Done (Today)
- ✅ **ExamBoard MVP**: شورتکد `[exam_board]` با تبهای Typing / Listen & Type / Shadowing / Describe Image / Timer.
- ✅ **Front Assets**: بارگذاری `public/css/board.css` و `public/js/board.js` + `wp_localize_script` برای nonce و i18n.
- ✅ **REST**: مسیرهای `POST /wp-json/examb/v1/progress` و `POST /wp-json/examb/v1/upload-audio` (ذخیره متریکها در user_meta آپلود صوت به Media Library).
- ✅ **Admin Menu/CPT**: منوی والد ExamBoard + ثبت CPTها زیر والد با `show_in_menu`.
- ✅ **Activate & Test Page**: افزونه فعال شد صفحه تست **ExamBoard Test** ساخته شد (ID=27) و در لوکال باز شد.
- ✅ **Branch & Push**: `feature/examb-board-mvp` ساخته و پوش شد.

## Known Issues / UX polish
- ◻ **ابعاد و ترازبندی**: textareaها و کارتها نیاز به scale و spacing بهتر (mobile/desktop).
- ◻ **ترکیب ابزارها**: همپوشانی منطق Listen & Type و Shadowing چینش دکمهها و حالتها نیاز به بازطراحی.
- ◻ **State Handling**: غیرفعال کردن دکمههای نادرست حین ضبط/پخش مدیریت stop/reset جلوگیری از تداخل TTS و Record.
- ◻ **Dup JS Path**: شاخهٔ اضافی `public/js/board/board.js` باید حذف و تأیید شود (در صورت باقیماندن).
- ◻ **RTL/I18N**: برچسبها و ترتیبها برای fa-IR/RTL بهبود یابد.
- ◻ **A11y**: فکوساستیتها aria-label برای کنترلها کنتراست.

## Plan (Tomorrow)
1) **UI Scale & Layout**
   - [ ] افزودن متغیرهای CSS (`--eb-gap --eb-pad --eb-radius --eb-font`) و `max-width` واکنشگرا (sm/md/lg).
   - [ ] افزایش مینارتفاع textareaها بر اساس viewport بهبود padding/spacing کارتها.
   - [ ] تمهای زمینه (روشن/تختهسیاه) برای بعد.

2) **Tool Composition**
   - [ ] ادغام منطق Listen & Type و Shadowing در یک پنل «Listening & Speaking» با بخشهای *Play / Record / Playback / Notes*.
   - [ ] دکمهها: Play/Stop (TTS) Record/Stop (Mic) Upload (بعد از stop) PlayBack (نمایش فقط وقتی blob حاضر است).

3) **State Machine (JS)**
   - [ ] قفلکردن کنترلها در حالت ضبط توقف خودکار TTS هنگام شروع ضبط و برعکس.
   - [ ] پاکسازی event listeners و tracks بعد از stop هندل کردن خطاهای میکروفن.

4) **Logging Hook (اتصال به سیستم گزارش خودت)**
   - [ ] افزودن hook سبک به `board.js`:
     - `if (window.ExamBoardLog) ExamBoardLog('tts_play', { textLen, lang });`
     - `ExamBoardLog('record_start' | 'record_stop', { ms });`
     - `ExamBoardLog('upload_success', { url, bytes });`
     - `ExamBoardLog('progress_saved', metrics);`
   - [ ] مستندسازی payloadها برای سیستم گزارش فعلی.

5) **RTL & i18n**
   - [ ] پشتیبانی `dir="rtl"` بر اساس lang=fa ترجمه برچسبها فونت مناسب فارسی.

6) **QA Checklist**
   - [ ] Chrome/Edge روی HTTPS و Localhost.
   - [ ] موبایل عرض < 420px.
   - [ ] کاربر «Subscriber»: اطمینان از محدودیت آپلود (یا تصمیمگیری cap).

## Acceptance
- UI روان و یکدست (spacing/typography) بدون تداخل TTS/Record آپلود بیخطا و ثبت متریکها با hook گزارش داخلی.
