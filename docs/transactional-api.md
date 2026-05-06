# Transactional Email API

API gửi email lẻ với tracking đầy đủ (open, click, sent, delivered, bounced, complained, failed). Mỗi request trả về một hash để client polling status hoặc nhận webhook callback push.

## Authentication

Tất cả endpoints đều scope theo workspace. Sử dụng Workspace API Token:

```
Authorization: Bearer YOUR_WORKSPACE_API_TOKEN
```

## Rate Limiting

Theo `SENDPORTAL_THROTTLE_MIDDLEWARE` env (mặc định `60,1` = 60 requests/phút).

## Endpoints

### POST `/api/v1/transactional/send`

Gửi email transactional/marketing/recruitment.

**Request body:**

```json
{
  "from": {
    "email": "noreply@yourdomain.com",
    "name": "Your Company"
  },
  "to": [
    { "email": "user@example.com", "name": "User Name" }
  ],
  "cc": [],
  "bcc": [],
  "subject": "Interview Invitation",
  "content": {
    "type": "html",
    "html": "<html><body><p>Hello</p></body></html>",
    "text": "plain text alternative (optional)"
  },
  "tracking": {
    "open": true,
    "click": true
  },
  "metadata": {
    "external_ref": "interview-123",
    "user_id": 456
  }
}
```

Hoặc với MIME mode:

```json
{
  "from": { "email": "noreply@yourdomain.com" },
  "to": [{ "email": "user@example.com" }],
  "subject": "MIME Test",
  "content": {
    "type": "mime",
    "mime": "MIME-Version: 1.0\r\nContent-Type: text/html\r\n\r\n<p>...</p>"
  }
}
```

**Response 201:**

```json
{
  "transactional_hash": "9b1f6c2e-4f5d-4f3e-9d8a-1234567890ab",
  "messages": [
    {
      "message_hash": "a1b2c3d4-...",
      "recipient": "user@example.com"
    }
  ]
}
```

**Lỗi:**

- `401`: Invalid/missing API token
- `422`: Validation failed hoặc domain không có email service
- `429`: Rate limited

### GET `/api/v1/transactional/{hash}`

Xem chi tiết tracking của 1 transactional send.

**Response 200:**

```json
{
  "transactional_hash": "9b1f6c2e-...",
  "created_at": "2026-05-05T10:00:00+00:00",
  "messages": [
    {
      "message_hash": "a1b2c3d4-...",
      "recipient": "user@example.com",
      "subject": "Interview Invitation",
      "queued_at": "2026-05-05T10:00:00+00:00",
      "sent_at": "2026-05-05T10:00:05+00:00",
      "delivered_at": "2026-05-05T10:00:10+00:00",
      "opened_at": "2026-05-05T10:05:00+00:00",
      "clicked_at": "2026-05-05T10:06:00+00:00",
      "bounced_at": null,
      "complained_at": null,
      "open_count": 2,
      "click_count": 1
    }
  ]
}
```

### GET `/api/v1/transactional`

List paginated transactional sources cho workspace.

**Query:** `?per_page=25&page=1`

## Email Service Routing (Sender Domain)

Khi nhận request, hệ thống tự chọn EmailService theo domain của `from.email`:

1. Tìm EmailService trong workspace có `sender_domains` array chứa domain
2. Nếu không tìm thấy, fallback về EmailService có `is_default=true`
3. Nếu không có cả hai, trả về 422 error

**Cấu hình domain mapping:**

```php
$emailService = EmailService::create([
    'workspace_id' => $workspaceId,
    'name' => 'Postmark Production',
    'type_id' => 4, // Postmark
    'settings' => ['server_token' => 'POSTMARK_TOKEN'],
    'sender_domains' => ['yourdomain.com', 'subdomain.yourdomain.com'],
    'is_default' => true,
]);
```

## Webhook Callback

Khi có tracking event (sent/delivered/opened/clicked/bounced/complained/failed), hệ thống POST về URL đã config trong workspace.

### Configuration

Set per-workspace:

```php
$workspace->update([
    'transactional_callback_url' => 'https://yourapp.com/sendportal-webhook',
    'transactional_callback_secret' => 'YOUR_RANDOM_32_CHAR_SECRET',
]);
```

### Callback payload

```json
{
  "event": "opened",
  "transactional_hash": "9b1f6c2e-...",
  "message_hash": "a1b2c3d4-...",
  "recipient": "user@example.com",
  "timestamp": "2026-05-05T10:05:00+00:00",
  "data": {
    "open_count": 2
  },
  "metadata": { "external_ref": "interview-123" }
}
```

### Headers

- `Content-Type: application/json`
- `X-Sendportal-Event`: event name (sent/delivered/opened/clicked/bounced/complained/failed)
- `X-Sendportal-Signature`: `sha256=<hex_hmac>` - HMAC-SHA256 của `{timestamp}.{body}` ký bằng secret
- `X-Sendportal-Timestamp`: unix seconds (đã include trong HMAC để chống replay)

### Verifying the signature

```php
$timestamp = $request->header('X-Sendportal-Timestamp');
$signature = $request->header('X-Sendportal-Signature');
$body = $request->getContent();
$secret = 'YOUR_SECRET';

$expected = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $body, $secret);

if (!hash_equals($expected, $signature)) {
    abort(401, 'Invalid signature');
}
```

```javascript
// Node.js
const crypto = require('crypto');

function verifyWebhook(timestamp, signature, body, secret) {
  const expected = 'sha256=' + crypto
    .createHmac('sha256', secret)
    .update(`${timestamp}.${body}`)
    .digest('hex');
  return crypto.timingSafeEqual(Buffer.from(expected), Buffer.from(signature));
}
```

### Retry behavior

- Timeout: 10 giây
- Retry: tối đa 3 lần
- Backoff: 10s, 60s, 300s
- Sau khi fail hết, log to MessageFailure (không ảnh hưởng status của message)

## Examples

### cURL - Send

```bash
curl -X POST http://localhost:8081/api/v1/transactional/send \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "from": {"email": "noreply@yourdomain.com", "name": "Test"},
    "to": [{"email": "user@example.com"}],
    "subject": "Test Email",
    "content": {"type": "html", "html": "<p>Hello</p>"},
    "tracking": {"open": true, "click": true},
    "metadata": {"test_id": "001"}
  }'
```

### cURL - Show

```bash
curl http://localhost:8081/api/v1/transactional/{hash} \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### cURL - List

```bash
curl "http://localhost:8081/api/v1/transactional?per_page=10" \
  -H "Authorization: Bearer YOUR_TOKEN"
```
