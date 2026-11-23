# Fileserver

## Context

Submitted application images use an external LGU fileserver because shared Hostinger storage is limited. The local/testing mock is a development facility, not production document storage.

## Required behavior

1. Use the existing image/fileserver service and configured remote endpoints for production document storage. Do not introduce permanent local copies of submitted documents. Temporary request uploads and the isolated local/testing mock are distinct from permanent production storage.
2. Authorize access to the associated application before returning documents. Resolve document requirements and storage locations server-side; keep fileserver tokens and credentials out of browser responses.
3. Use the existing date-prefixed token contract (`YYYYMMDD` followed by the capability secret) and configured application timezone. Read settings through configuration, not direct environment access in application services.
4. Allow only supported image uploads, enforce the configured KB size limit in submission flows, and preserve image type/dimension/pixel validation. Supported application inputs are JPEG, PNG, and WebP; the mock accepting another type does not authorize that type in the application.
5. Preserve the confirmed current transformation: convert uploads to JPEG, encrypt the bytes, and use generated `.jpg.enc` filenames. Original user filenames/extensions are not required to survive this transformation.
6. Keep encryption and decryption in the application. Preserve the fileserver encryption key and supported existing payload readers; do not add a plaintext fallback.
7. Record and reuse the fileserver's returned filename and folder. The remote server may rename files; do not reconstruct its generated names or overwrite saved references with guesses. Keep mock and remote references separate.
8. If any required document upload fails, preserve the citizen's draft and prevent application submission until all required uploads succeed. Do not create a submitted application with a warning as a substitute. A deliberately skipped upload is not proof of remote storage and cannot satisfy a required upload. Do not automatically retry uploads or silently overwrite documents.
9. Walk-in submission requires environment-safe storage. In `local`, primary uploads must be disabled and the configured loopback fileserver mock must respond to its health check; images are optional, and provided images use the mock even when primary uploads are disabled. Outside `local`, primary uploads must be enabled, the mock must be disabled and offline, and required document images remain mandatory. Enforce these conditions on the server as well as in the UI. Provided images always require normal validation and successful storage; do not create references for omitted images.

## Constraints and references

- Current submission paths are inconsistent with the intended failure rule: the controller can retain a submitted application after an upload failure, while the Livewire path uses a database transaction. A database rollback cannot undo a completed remote upload. Draft preservation and recovery from partial uploads need verification in a separately authorized implementation task.
- Real-server acceptance and byte preservation for encrypted `.jpg.enc` files remain unverified. Image re-encoding on the remote side could corrupt ciphertext. Mock success is not proof of compatibility.
- [Fileserver mock and application contract](../../docs/fileserver-mock.md) describes tokens, references, legacy lookup, response handling, and focused tests. Its mock-specific routes and file types do not establish the real provider's contract.
- [Deployment environment](../../docs/deployment-environment.md) describes remote configuration and capability limitations.
- Owning code: [ImageService](../../app/Services/ImageService.php), [ImagePayload](../../app/Services/ImagePayload.php), [ImageUploadValidator](../../app/Services/ImageUploadValidator.php), and [ImageController](../../app/Http/Controllers/ImageController.php).
