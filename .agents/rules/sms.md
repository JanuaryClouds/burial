# SMS

## Context and current behavior

SMS is an external integration used by interview scheduling. It is separate from Portal notifications and email. `SmsService` currently sends synchronously during the request; interview scheduling tracks per-channel attempts and supports authorized manual retries without workers.

## Required behavior

1. Reuse `SmsService` and the `services.sms` configuration. Keep credentials in environment configuration, not application code, rule files, browser output, or logs.
2. Honor the configured POST enable flag. Disabled sending is deliberately skipped and safely logged; the method's successful return in that mode is not evidence that an SMS was delivered.
3. Preserve the existing interview notification purpose and use the intended client's contact number. Use synthetic or agreed test recipients during verification; do not send live messages as an incidental code check.
4. Missing enabled-service configuration or failed upstream requests must remain visible as failures. Do not introduce workers, automatic retries, or delivery guarantees without an agreed provider contract and business behavior.
5. Successful required interview notifications are a prerequisite for successful scheduling. If a required SMS or Portal notification fails, do not leave the interview treated as successfully scheduled or show scheduling success. Preserve staff input for correction or retry and clearly report the notification failure. A warning alongside a successfully scheduled interview does not satisfy this rule.
6. External messages already accepted by one provider cannot be rolled back when another notification fails. Do not assume a database transaction reverses external sends or automatically resend every channel after a partial failure. Success here means acceptance under the provider contract, not proof that the citizen read the message.
7. Local/testing must never call external SMS or Portal notification endpoints, regardless of enable flags, and must allow scheduling with internal notifications for linked citizen accounts. In production and other environments, linked citizens require both SMS and Portal enablement/configuration and acceptance; accountless walk-ins require SMS only. Internal notification recording is independent of external flags.

## References and uncertainty

- [SMS service](../../app/Services/SmsService.php), [interview scheduling](../../app/Http/Controllers/InterviewController.php), [service configuration](../../config/services.php), and [deployment environment](../../docs/deployment-environment.md).
- The service contains an unresolved TODO about the provider's URL and authentication/request format. Its current credential-bearing URL must not be treated as a verified specification. Obtain the provider contract before changing or claiming real-server compatibility.
- No separate delivery-receipt contract is established here. An accepted request does not establish handset delivery.
- The scheduling coordinator reserves requests, sends outside transactions, and finalizes an interview plus its internal notification only when all required channels are accepted. It records safe failed/uncertain outcomes, never automatically retries, and requires explicit duplicate-risk confirmation for uncertain retries.
- See [Environment-aware submission and scheduling](../../docs/environment-aware-submissions.md) for schema and operational limits.
