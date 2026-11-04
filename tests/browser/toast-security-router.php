<?php

// Standalone loopback-only fixture; no configured database or external services.
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Support\Facades\Cache;

if (PHP_SAPI !== 'cli-server' || ! in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(404);
    exit;
}
$root = dirname(__DIR__, 2);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$assets = [
    '/plugins.js' => $root.'/public/metronic/plugins/global/plugins.bundle.js',
    '/toast.js' => $root.'/public/js/toast.js',
];
if (isset($assets[$path])) {
    header('Content-Type: application/javascript');
    readfile($assets[$path]);
    exit;
}
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->afterBootstrapping(LoadConfiguration::class, function ($app) {
    $app['config']->set([
        'database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
        'cache.default' => 'array', 'session.driver' => 'array', 'app.debug' => false,
        'mail.default' => 'array', 'queue.default' => 'sync',
    ]);
});
$app->make(Kernel::class)->bootstrap();
foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
    Cache::put($lookup, collect(), 3600);
}
$payload = '<img src=x onerror="window.toastInjected=true"> <script>window.toastInjected=true</script> & "quotes" \'apostrophe\'';
?>
<!doctype html><html><head><meta charset="utf-8"><title>Toast security fixture</title></head><body>
<pre id="result">PENDING</pre>
<script src="/plugins.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
<script>
window.toastInjected = false;
window.toastCallback = null;
window.Livewire = { on(name, callback) { window.toastCallback = callback; } };
window.toastExpected = <?= json_encode($payload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
</script>
<?php
foreach (['success', 'error', 'info'] as $kind) {
    session()->flush();
    session()->put($kind, $payload);
    echo view('components.notification.toast')->render();
}
?>
<script type="module">
import toast from '/toast.js';
toast();
toastr.options.timeOut = 0;
toastr.options.extendedTimeOut = 0;
toastr.options.preventDuplicates = false;
for (const type of ['success', 'error', 'warning', 'info']) {
    window.toastCallback([{ type, text: window.toastExpected, title: window.toastExpected }]);
}
setTimeout(() => {
    const messages = [...document.querySelectorAll('.toastr-message')];
    const titles = [...document.querySelectorAll('.toastr-title')];
    const sessions = [...document.querySelectorAll('.iziToast-message')];
    const nodes = [...messages, ...titles, ...sessions];
    const result = {
        livewireMessages: messages.length, livewireTitles: titles.length, sessionMessages: sessions.length,
        literalText: nodes.every(node => node.textContent === window.toastExpected),
        injectedElements: nodes.some(node => node.querySelector('img,script')),
        executed: window.toastInjected,
    };
    result.passed = result.livewireMessages === 4 && result.livewireTitles === 4 && result.sessionMessages === 3 && result.literalText && !result.injectedElements && !result.executed;
    document.getElementById('result').textContent = JSON.stringify(result);
}, 500);
</script></body></html>
