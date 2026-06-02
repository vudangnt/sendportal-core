<div class="modal fade" id="sendTestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="send-test-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send test — {{ $template->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-left">
                    <div class="form-group">
                        <label>To email *</label>
                        <input name="to_email" type="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>From email *</label>
                        <input name="from_email" type="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Variables (JSON)</label>
                        <textarea name="variables" class="form-control" rows="5"></textarea>
                    </div>
                    <div id="test-result" class="alert alert-info d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Send Test</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('send-test-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    const result = document.getElementById('test-result');
    result.classList.remove('d-none', 'alert-danger', 'alert-success');
    result.classList.add('alert-info');
    result.textContent = 'Sending...';

    const fd = new FormData(this);
    const body = {
        to_email:   fd.get('to_email'),
        from_email: fd.get('from_email'),
        variables:  {},
    };
    try { body.variables = JSON.parse(fd.get('variables') || '{}'); } catch (_) {}

    const resp = await fetch(@json($route), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                || document.querySelector('input[name=_token]')?.value
        },
        body: JSON.stringify(body),
    });
    const data = await resp.json().catch(() => ({}));

    if (resp.ok) {
        result.classList.remove('alert-info');
        result.classList.add('alert-success');
        result.innerHTML = 'Sent. Tracking: <code>' + (data.tracking_url || '') + '</code><br>' +
                           'Subject: ' + (data.rendered?.subject || '');
    } else {
        result.classList.remove('alert-info');
        result.classList.add('alert-danger');
        result.textContent = data.error || ('Failed (' + resp.status + ')');
    }
});
</script>
