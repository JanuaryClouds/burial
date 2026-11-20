# Application mail

## Context

Mail runs during the request through `ImmediateMailService` to fit shared hosting. Browser-side asynchronous requests provide progress feedback; they do not make SMTP a background job.

## Required behavior

1. Use the existing immediate-mail service and registered message handlers for application mail. Persist delivery/attempt outcomes and perform SMTP outside the business transaction. Do not introduce queued mail, workers, scheduled retries, or a separate SMTP client.
2. Validate recipients and check every To/Cc/Bcc address against the shared suppression log before sending. Permanent bounces or complaints from any site block the whole message. Unavailable suppression logs must stop sending rather than fall back to another database.
3. Treat the external email-log database as read-only. Use its separate connection and credentials; do not migrate, seed, update, or delete its real tables.
4. Preserve the global SES site tag and existing validation of additional tags. Keep credentials, message payloads, recovery tokens, and raw transport diagnostics out of logs and public error responses.
5. Production requires configured SMTP with TLS and certificate verification. Local mock logs are only for local/testing; production must not use mock suppression checks or pretend mail was sent through a log fallback.
6. Keep recovery acknowledgements generic. Citizens recover through the Portal; eligible internal accounts use the existing expiring, single-use reset flow.
7. Retry only through the authorized manual-retry flow, rechecking eligibility and suppression. Do not retry suppressed or already successful deliveries. Preserve the warning that interrupted SMTP delivery may be uncertain and retry may duplicate a message.
8. Preserve the existing application encryption key. Delivery recipients and payloads depend on it.

## Interpretation and references

- A local `sent` outcome means SMTP acceptance, not inbox delivery or proof of reading.
- Exact token lifetimes, throttles, interruption grace periods, environment settings, handler extension points, and checks are maintained in [Email setup and operations](../../docs/email.md).
- Owning code: [ImmediateMailService](../../app/Services/Mail/ImmediateMailService.php), [OutgoingMailPolicy](../../app/Services/Mail/OutgoingMailPolicy.php), and [SharedEmailLogs](../../app/Services/Mail/SharedEmailLogs.php).
