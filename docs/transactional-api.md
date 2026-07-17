# Transactional Email API

API gửi email lẻ (transactional/notifications/recruitment) với tracking đầy đủ và webhook callback. Mỗi request trả về một `transactional_hash` để client polling status hoặc nhận push qua webhook.

Base URL: `{APP_URL}/api/v1`

---

## Authentication

`POST /transactional/send` yêu cầu **Transactional API key** — loại key riêng do **super-admin cấp per-workspace** (Super Admin → Workspace → "Transactional API Key"). Key general dùng cho subscribers/campaigns API **không** gọi được endpoint send (trả `403`).

```
Authorization: Bearer YOUR_TRANSACTIONAL_API_KEY
Content-Type: application/json
Accept: application/json
```

- Key gắn với 1 workspace; mỗi workspace tối đa 1 transactional key.
- Thiếu/sai token → `401`. Dùng key sai loại (general) cho `/transactional/send` → `403 { "error": "This endpoint requires a transactional API key." }`.

---

## Rate Limiting & Quota

| Layer | Cấu hình | Response khi vượt |
|---|---|---|
| Per-IP throttle | `SENDPORTAL_THROTTLE_MIDDLEWARE=60,1` (60 req/phút) | `429 Too Many Requests` |
| Monthly email limit | `workspaces.monthly_email_limit` | `429` với body bên dưới |

```json
{
  "error": "Monthly email limit reached",
  "limit": 10000,
  "used": 10000,
  "reset_date": "2026-06-01"
}
```

`check-email-limit` middleware chỉ áp dụng cho `POST /transactional/send`; endpoints `GET` không bị chặn.

---

## Endpoints

### `POST /api/v1/transactional/send`

Queue 1 email cho 1 hoặc nhiều recipients. Mỗi recipient trong `to` tạo 1 Message record riêng nhưng share chung `transactional_hash`.

#### Request body

```json
{
  "from": {
    "email": "noreply@yourdomain.com",
    "name": "Your Company"
  },
  "to": [
    { "email": "user@example.com", "name": "User Name" }
  ],
  "cc": [
    { "email": "manager@example.com" }
  ],
  "bcc": [
    { "email": "audit@example.com" }
  ],
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

MIME mode (raw RFC 5322 message, bỏ qua `subject`/`html`/`text` rendering):

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

#### Template mode (`template_code`)

Thay vì gửi `subject` + `content.html` trực tiếp, client có thể tham chiếu một **transactional template** đã lưu bằng `template_code`, kèm `variables` để render các placeholder `{{ var }}`.

```json
{
  "from": { "email": "hr@yourdomain.com" },
  "to":   [{ "email": "candidate@example.com" }],
  "template_code": "shortlist",
  "variables": {
    "candidate_name": "Anh",
    "job_title": "PHP Developer",
    "company": "Digisource"
  }
}
```

Khi có `template_code`:

- `subject` và `content` trở thành **optional** (server lấy từ template).
- Server render placeholder `{{ var }}` trên **cả** subject và content. Cú pháp Mustache: `{{ ten_bien }}` (cho phép khoảng trắng `{{ ten_bien }}`). Tên biến: `[a-zA-Z_][a-zA-Z0-9_]*`.
- Biến **thiếu hoặc null** → render thành chuỗi rỗng (không leak `{{ var }}` ra recipient).
- Không HTML-escape: giá trị biến được chèn nguyên văn. Client tự đảm bảo nội dung an toàn.

**Resolver — thứ tự ưu tiên theo `template_code`:**

1. Template của workspace hiện tại (`workspace_id = current`, `code = X`, `kind = transactional`) → workspace override.
2. Fallback default global (`workspace_id = NULL`, `is_default = true`, `code = X`).
3. Không tìm thấy → `422 { "message": "Template not found for code: X" }`.

**Override per-field:** Nếu client **vẫn** gửi `subject` hoặc `content.html` cùng với `template_code`, giá trị client gửi **ghi đè** field tương ứng của template (sau khi render). Dùng để override một phần (vd. chỉ đổi subject, giữ body của template).

**Default codes** (seed sẵn cho mọi workspace — job application status):

| Code | Mục đích |
|---|---|
| `applied` | Đã nhận hồ sơ ứng tuyển |
| `shortlist` | Vào shortlist |
| `interviewed` | Xác nhận phỏng vấn |
| `offered` | Gửi offer |
| `onboard_probation` | Onboarding & thử việc |
| `fail` | Kết quả không trúng tuyển |

Default templates dùng các biến: `candidate_name`, `job_title`, `company`. Workspace có thể tạo template cùng `code` để override default, hoặc tạo code riêng.

> Mỗi template lưu 2 cột: `content` (HTML dùng để gửi mail, render `{{ var }}` + branding) và `data_json` (design Unlayer mà visual editor đọc). Default templates được seed với **design Unlayer đa block** (`TransactionalTemplateDesignBuilder` + dataset `TransactionalTemplateSeedData`): header/footer là block HTML chứa các composite branding (`{{ brand_header_html }}`, `{{ brand_contact_html }}`, `{{ brand_social_html }}`), còn **tiêu đề, đoạn nội dung và nút CTA là block Unlayer chuẩn nên sửa trực quan được** (không còn là một khối HTML tĩnh). Branding nằm trong block HTML để vòng edit→Save trên editor không làm mất placeholder.

#### Field reference

| Field | Type | Required | Constraint |
|---|---|---|---|
| `from.email` | string | yes | valid email |
| `from.name` | string | no | max 255 |
| `to` | array | yes | min 1 item |
| `to[].email` | string | yes | valid email |
| `to[].name` | string | no | max 255 |
| `cc` / `bcc` | array | no | same shape as `to` — **được nhận nhưng CHƯA gửi** (chỉ `to` được gửi) |
| `subject` | string | yes¹ | max 998 chars (RFC 5322) |
| `content.type` | enum | yes¹ | `html` \| `mime` |
| `content.html` | string | required if `type=html`¹ | — |
| `content.text` | string | no | plain-text alternative |
| `content.mime` | string | required if `type=mime` | full MIME message — **chưa được relay** (dùng `type=html`) |
| `template_code` | string | no | `^[a-z0-9 _-]+$`, max 64 (dấu cách hợp lệ, vd `send to client`, `pass probation`). Khi có → render template thay cho `subject`/`content` |
| `variables` | object | no | key-value chèn vào placeholder `{{ key }}` của template |
| `tracking.open` | bool | no | default true tùy provider |
| `tracking.click` | bool | no | default true tùy provider |
| `metadata` | object | no | echo lại trong webhook callback |

> ¹ `subject` + `content` chỉ **bắt buộc khi KHÔNG có `template_code`**. Có `template_code` thì chúng optional (lấy từ template, nhưng vẫn override được nếu client gửi kèm).

#### Response `201 Created`

```json
{
  "transactional_hash": "9b1f6c2e-4f5d-4f3e-9d8a-1234567890ab",
  "template_code": "shortlist",
  "messages": [
    {
      "message_hash": "a1b2c3d4-...",
      "recipient": "user@example.com"
    }
  ]
}
```

`template_code` echo lại code đã resolve (hoặc `null` nếu request không dùng template).

#### Errors

| Status | Khi nào | Body |
|---|---|---|
| `401` | thiếu/sai token | Laravel default |
| `403` | dùng key general (không phải transactional key) | `{ "error": "This endpoint requires a transactional API key." }` |
| `422` | validation fail | Laravel validation errors |
| `422` | `from.email` domain không nằm trong `sender_domains` (không fallback) | `{ "error": "Sender domain not allowed for this workspace", "from_email": "...", "hint": "..." }` |
| `422` | `template_code` không tồn tại (cả workspace lẫn default) | `{ "message": "Template not found for code: <code>" }` |
| `422` | thiếu subject/content và không có template_code hợp lệ | `{ "error": "Missing subject or content (provide them or supply a template_code)." }` |
| `429` | rate limit hoặc monthly cap | xem mục Rate Limiting |
| `500` | DB transaction fail khi queue message | `{ "error": "Failed to queue transactional email", "message": "..." }` |

---

### `GET /api/v1/transactional/{hash}`

Trả về tracking chi tiết của một transactional send.

#### Response `200 OK`

```json
{
  "transactional_hash": "9b1f6c2e-...",
  "created_at": "2026-05-05T10:00:00+07:00",
  "messages": [
    {
      "message_hash": "a1b2c3d4-...",
      "recipient": "user@example.com",
      "subject": "Interview Invitation",
      "queued_at": "2026-05-05T10:00:00+07:00",
      "sent_at": "2026-05-05T10:00:05+07:00",
      "delivered_at": "2026-05-05T10:00:10+07:00",
      "opened_at": "2026-05-05T10:05:00+07:00",
      "clicked_at": "2026-05-05T10:06:00+07:00",
      "bounced_at": null,
      "complained_at": null,
      "open_count": 2,
      "click_count": 1
    }
  ]
}
```

Tất cả trường `*_at` là ISO-8601 hoặc `null` nếu event chưa xảy ra.

#### Errors

| Status | Body |
|---|---|
| `404` | `{ "error": "Transactional source not found" }` |

---

### `GET /api/v1/transactional`

List paginated transactional sources cho workspace hiện tại, sort `created_at desc`.

#### Query params

| Param | Default | Mô tả |
|---|---|---|
| `per_page` | 25 | items per page |
| `page` | 1 | page index |

#### Response `200 OK`

Laravel resource collection (chuẩn pagination):

```json
{
  "data": [
    {
      "transactional_hash": "9b1f6c2e-...",
      "created_at": "2026-05-05T10:00:00+07:00",
      "messages": [ /* same shape as show endpoint */ ]
    }
  ],
  "links": {
    "first": "...?page=1",
    "last": "...?page=10",
    "prev": null,
    "next": "...?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 25,
    "to": 25,
    "total": 250
  }
}
```

---

## Email Service Routing (Sender Domain)

Khi nhận `POST /transactional/send`, `TransactionalEmailServiceResolver` chọn EmailService theo **strict whitelist**:

1. EmailService trong workspace có `sender_domains` JSON chứa domain của `from.email` → dùng service đó.
2. Không khớp → `422` (**KHÔNG** fallback `is_default`).

> Domain của `from.email` **bắt buộc** nằm trong `sender_domains` của một email service đã cấu hình trong workspace. Đây là giới hạn chủ đích: transactional key chỉ gửi được từ domain đã setup. `is_default` không còn là fallback cho transactional send (vẫn dùng cho mục đích khác).

#### Cấu hình mapping

```php
EmailService::create([
    'workspace_id'    => $workspaceId,
    'name'            => 'Postmark Production',
    'type_id'         => 4, // Postmark
    'settings'        => ['server_token' => env('POSTMARK_TOKEN')],
    'sender_domains'  => ['yourdomain.com', 'mail.yourdomain.com'],
    'is_default'      => true,
]);
```

---

## Webhook Callback

Mỗi tracking event của message có `source_type = TransactionalSource` đều dispatch một POST về workspace callback URL.

### Configuration (per workspace)

```php
$workspace->update([
    'transactional_callback_url'    => 'https://yourapp.com/sendportal-webhook',
    'transactional_callback_secret' => 'YOUR_RANDOM_32_CHAR_SECRET',
]);
```

Nếu `transactional_callback_url` rỗng → callback được skip silently.

### Events dispatched

| Event | Khi nào | `data` field |
|---|---|---|
| `sent` | Provider đã accept (sau khi gọi API) | `{}` |
| `delivered` | Recipient inbox accept | `{}` |
| `opened` | Tracking pixel mở | `{ "open_count": <int> }` |
| `clicked` | Tracking link click | `{ "click_count": <int>, "click_url": "..." }` |
| `bounced` | Hard/soft bounce | `{}` |
| `complained` | Spam complaint | `{}` |
| `failed` | Provider/queue lỗi | `{ "failure_reason": "..." }` |

### Callback payload

```json
{
  "event": "opened",
  "transactional_hash": "9b1f6c2e-...",
  "message_hash": "a1b2c3d4-...",
  "recipient": "user@example.com",
  "timestamp": "2026-05-05T10:05:00+07:00",
  "data": {
    "open_count": 2
  },
  "metadata": {
    "external_ref": "interview-123",
    "user_id": 456
  }
}
```

`metadata` echo nguyên `metadata` từ original send request.

### Headers

| Header | Giá trị |
|---|---|
| `Content-Type` | `application/json` |
| `X-Sendportal-Event` | tên event (sent/delivered/opened/clicked/bounced/complained/failed) |
| `X-Sendportal-Timestamp` | unix seconds, đã include trong HMAC để chống replay |
| `X-Sendportal-Signature` | `sha256=<hex_hmac>` của `{timestamp}.{body}` ký bằng `transactional_callback_secret` |

### Verify signature

PHP:

```php
$timestamp = $request->header('X-Sendportal-Timestamp');
$signature = $request->header('X-Sendportal-Signature');
$body      = $request->getContent();
$secret    = 'YOUR_SECRET';

$expected = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $body, $secret);

if (!hash_equals($expected, $signature)) {
    abort(401, 'Invalid signature');
}

// Optional: reject timestamps > 5 phút lệch để chống replay
if (abs(time() - (int) $timestamp) > 300) {
    abort(401, 'Stale timestamp');
}
```

Node.js:

```javascript
const crypto = require('crypto');

function verifyWebhook(timestamp, signature, body, secret) {
  const expected =
    'sha256=' +
    crypto.createHmac('sha256', secret).update(`${timestamp}.${body}`).digest('hex');

  return crypto.timingSafeEqual(Buffer.from(expected), Buffer.from(signature));
}
```

Python:

```python
import hmac, hashlib

def verify_webhook(timestamp: str, signature: str, body: str, secret: str) -> bool:
    expected = "sha256=" + hmac.new(
        secret.encode(),
        f"{timestamp}.{body}".encode(),
        hashlib.sha256,
    ).hexdigest()
    return hmac.compare_digest(expected, signature)
```

### Retry behavior

| Property | Giá trị |
|---|---|
| Timeout | 10s |
| Max attempts | 3 |
| Backoff | 10s → 60s → 300s |
| On final failure | log MessageFailure, không thay đổi message status |

Endpoint của bạn cần idempotent — Sendportal có thể retry với cùng `message_hash`.

---

## Examples

### Send (cURL)

```bash
curl -X POST {APP_URL}/api/v1/transactional/send \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "from": {"email": "noreply@yourdomain.com", "name": "Your Company"},
    "to": [{"email": "user@example.com", "name": "User"}],
    "subject": "Welcome",
    "content": {"type": "html", "html": "<p>Hello</p>"},
    "tracking": {"open": true, "click": true},
    "metadata": {"signup_id": "abc-123"}
  }'
```

### Send với template_code (cURL)

```bash
curl -X POST {APP_URL}/api/v1/transactional/send \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "from": {"email": "hr@yourdomain.com", "name": "HR Team"},
    "to": [{"email": "candidate@example.com"}],
    "template_code": "shortlist",
    "variables": {
      "candidate_name": "Anh",
      "job_title": "PHP Developer",
      "company": "Digisource"
    },
    "metadata": {"application_id": 789}
  }'
```

### Send (PHP / Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client(['base_uri' => env('SENDPORTAL_URL')]);

$response = $client->post('/api/v1/transactional/send', [
    'headers' => [
        'Authorization' => 'Bearer ' . env('SENDPORTAL_TOKEN'),
        'Accept'        => 'application/json',
    ],
    'json' => [
        'from'    => ['email' => 'noreply@yourdomain.com'],
        'to'      => [['email' => $user->email]],
        'subject' => 'Welcome',
        'content' => ['type' => 'html', 'html' => $html],
        'metadata'=> ['user_id' => $user->id],
    ],
]);

$hash = json_decode($response->getBody(), true)['transactional_hash'];
```

### Send (Node / fetch)

```javascript
const res = await fetch(`${SENDPORTAL_URL}/api/v1/transactional/send`, {
  method: 'POST',
  headers: {
    Authorization: `Bearer ${TOKEN}`,
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    from: { email: 'noreply@yourdomain.com' },
    to: [{ email: 'user@example.com' }],
    subject: 'Welcome',
    content: { type: 'html', html: '<p>Hello</p>' },
    metadata: { user_id: 42 },
  }),
});

const { transactional_hash } = await res.json();
```

### Show (cURL)

```bash
curl {APP_URL}/api/v1/transactional/{hash} \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### List (cURL)

```bash
curl "{APP_URL}/api/v1/transactional?per_page=10&page=1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Internal Architecture (tham khảo)

```
POST /transactional/send
  └─ TransactionalController::send
       ├─ TransactionalEmailServiceResolver::resolve(workspaceId, fromEmail)
       ├─ TransactionalSource::create(request_payload)
       ├─ Message::create(...) per recipient
       └─ SendTransactionalMessageJob::dispatch(messageId, emailServiceId)
                └─ DispatchTransactionalMessage → RelayMessage → provider API

Provider webhook
  └─ EmailWebhookService
       ├─ update Message tracking fields
       └─ fire MessageSent/Delivered/Opened/Clicked/Bounced/Complained/Failed Event
                └─ DispatchTransactionalCallbackListener
                       └─ DispatchTransactionalCallbackJob
                              └─ POST workspace.transactional_callback_url (HMAC signed)
```

Chi tiết source code:

- Controller: `lib/sendportal-core/src/Http/Controllers/Api/TransactionalController.php`
- Validation: `lib/sendportal-core/src/Http/Requests/Api/SendTransactionalEmailRequest.php`
- Resolver: `lib/sendportal-core/src/Services/Transactional/TransactionalEmailServiceResolver.php`
- HMAC: `lib/sendportal-core/src/Services/Transactional/HmacSigner.php`
- Job: `lib/sendportal-core/src/Jobs/SendTransactionalMessageJob.php`
- Callback: `lib/sendportal-core/src/Jobs/DispatchTransactionalCallbackJob.php`
- Routes: `lib/sendportal-core/src/Routes/ApiRoutes.php`
