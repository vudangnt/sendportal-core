{{-- Reusable split editor + preview. Expects $template (nullable). --}}

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label>Subject</label>
            <input type="text" name="subject" id="tpl-subject"
                   class="form-control"
                   value="{{ old('subject', $template->subject ?? '') }}"
                   placeholder="Subject — supports @{{ var }}">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <label>Content (HTML)</label>
        <textarea name="content" id="tpl-content" class="form-control"
                  rows="22" style="font-family: monospace; font-size: 13px;"
                  placeholder="<p>Hi @{{ candidate_name }},</p>">{{ old('content', $template->content ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label>Live preview</label>
        <div style="border:1px solid #dee2e6; border-radius:4px; padding:8px;">
            <div class="text-muted small mb-1">Subject preview:</div>
            <div id="tpl-subject-preview" style="font-weight:600; margin-bottom:8px;"></div>
            <iframe id="tpl-preview-frame" sandbox="allow-same-origin"
                    style="width:100%; min-height:420px; border:1px solid #eee;"></iframe>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <label>Sample variables (JSON) — used only for preview</label>
        <textarea id="tpl-variables" class="form-control" rows="4"
                  style="font-family: monospace; font-size: 13px;"
                  placeholder='{ "candidate_name": "Anh", "job_title": "PHP Dev", "company": "Digisource" }'></textarea>
        <small id="tpl-vars-error" class="text-danger d-none">Invalid JSON — preview using {}</small>
        <div class="mt-1">
            <span class="text-muted small">Detected variables:</span>
            <span id="tpl-detected" class="text-monospace"></span>
        </div>
    </div>
</div>

<script>
(function () {
    var subjectEl = document.getElementById('tpl-subject');
    var contentEl = document.getElementById('tpl-content');
    var varsEl    = document.getElementById('tpl-variables');
    var errorEl   = document.getElementById('tpl-vars-error');
    var previewSub = document.getElementById('tpl-subject-preview');
    var frame      = document.getElementById('tpl-preview-frame');
    var detectedEl = document.getElementById('tpl-detected');

    var storageKey = 'tpl-vars-' + window.location.pathname;
    try { var saved = localStorage.getItem(storageKey); if (saved) varsEl.value = saved; } catch (e) {}

    var VAR_REGEX = /\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\}/g;

    function render(str, vars) {
        return (str || '').replace(VAR_REGEX, function (_, k) {
            return Object.prototype.hasOwnProperty.call(vars, k) && vars[k] != null
                ? String(vars[k]) : '';
        });
    }

    function detect(s) {
        var set = {};
        var m;
        VAR_REGEX.lastIndex = 0;
        while ((m = VAR_REGEX.exec(s)) !== null) set[m[1]] = true;
        return Object.keys(set);
    }

    var timer = null;
    function update() {
        clearTimeout(timer);
        timer = setTimeout(function () {
            var vars = {};
            var raw = varsEl.value.trim();
            errorEl.classList.add('d-none');
            if (raw.length) {
                try { vars = JSON.parse(raw); }
                catch (e) { errorEl.classList.remove('d-none'); }
            }
            try { localStorage.setItem(storageKey, varsEl.value); } catch (e) {}

            previewSub.textContent = render(subjectEl.value, vars);

            var html = render(contentEl.value, vars);
            frame.srcdoc = '<!doctype html><html><body style="font-family:Arial,sans-serif;">' +
                            html + '</body></html>';

            var allVars = detect((subjectEl.value || '') + '\n' + (contentEl.value || ''));
            var provided = {};
            Object.keys(vars).forEach(function (k) { provided[k] = true; });
            detectedEl.innerHTML = allVars.length
                ? allVars.map(function (v) {
                    return provided[v]
                        ? '<span class="badge badge-light mr-1">' + v + '</span>'
                        : '<span class="badge badge-warning mr-1" title="Missing in sample JSON">' + v + '</span>';
                  }).join('')
                : '<em class="text-muted">none</em>';
        }, 300);
    }

    subjectEl.addEventListener('input', update);
    contentEl.addEventListener('input', update);
    varsEl.addEventListener('input', update);
    update();
})();
</script>
