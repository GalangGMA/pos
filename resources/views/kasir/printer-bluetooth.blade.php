<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Printer Bluetooth</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('kasir.partials.theme-head')
    @include('kasir.partials.theme-style')
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-950 text-slate-100">
    <div class="relative isolate overflow-hidden">
        <div id="page-background" class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(34,211,238,0.16),_transparent_35%),radial-gradient(circle_at_right,_rgba(59,130,246,0.16),_transparent_30%),linear-gradient(180deg,_#020617_0%,_#0f172a_45%,_#111827_100%)]"></div>

        <header class="border-b border-white/10 bg-slate-950/80 backdrop-blur">
            <div class="mx-auto flex w-full max-w-md items-center justify-between gap-3 px-4 py-5">
                <div class="min-w-0">
                    <p class="text-sm text-cyan-300">Lainnya</p>
                    <h1 class="text-2xl font-semibold tracking-tight text-white">Printer Bluetooth</h1>
                    <p class="mt-1 truncate text-sm text-slate-300">{{ $outlet->name }} • {{ $user->name }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @include('kasir.partials.theme-toggle')
                    <a href="{{ route('kasir.more') }}" class="inline-flex h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 text-sm font-semibold text-white transition hover:bg-white/10">
                        Kembali
                    </a>
                </div>
            </div>
        </header>

        <main id="printer-page" class="mx-auto w-full max-w-md space-y-6 px-4 py-6 pb-28">
            <section id="printer-support-card" class="rounded-[2rem] border border-cyan-400/20 bg-cyan-500/10 p-5 shadow-2xl shadow-slate-950/20 backdrop-blur-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-cyan-100/90">Status Browser</p>
                        <h2 class="mt-2 text-xl font-semibold text-white">Koneksi printer dari halaman kasir</h2>
                    </div>
                    <span id="support-badge" class="rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">Cek...</span>
                </div>
                <p id="support-text" class="mt-4 text-sm leading-6 text-cyan-50/80">
                    Mengecek dukungan Web Bluetooth di browser ini.
                </p>
            </section>

            <section id="printer-config-card" class="rounded-[2rem] border border-white/10 bg-white/[0.06] p-5 shadow-2xl shadow-slate-950/20 backdrop-blur-xl">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-300">Perangkat tersimpan</p>
                        <h2 class="mt-1 text-lg font-semibold text-white">Konfigurasi printer</h2>
                    </div>
                    <button id="clear-config-button" type="button" class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-white/10">
                        Reset
                    </button>
                </div>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="service-uuid" class="mb-2 block text-sm font-medium text-slate-300">Service UUID</label>
                        <input id="service-uuid" type="text" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none focus:border-cyan-300/60" placeholder="contoh: 0000ffe0-0000-1000-8000-00805f9b34fb">
                    </div>

                    <div>
                        <label for="characteristic-uuid" class="mb-2 block text-sm font-medium text-slate-300">Characteristic UUID</label>
                        <input id="characteristic-uuid" type="text" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none focus:border-cyan-300/60" placeholder="contoh: 0000ffe1-0000-1000-8000-00805f9b34fb">
                    </div>

                    <div>
                        <label for="printer-preset" class="mb-2 block text-sm font-medium text-slate-300">Preset Umum</label>
                        <select id="printer-preset" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none focus:border-cyan-300/60">
                            <option value="custom">Custom</option>
                            <option value="ble-ffe0">BLE thermal printer (FFE0 / FFE1)</option>
                            <option value="ble-18f0">BLE thermal printer (18F0 / 2AF1)</option>
                        </select>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    <button id="connect-button" type="button" class="inline-flex items-center justify-center rounded-2xl bg-cyan-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                        Hubungkan Printer
                    </button>
                    <button id="disconnect-button" type="button" class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Putuskan
                    </button>
                </div>

                <div class="mt-5 rounded-3xl border border-white/10 bg-slate-950/50 p-4 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-400">Printer</span>
                        <span id="device-name" class="font-semibold text-white">Belum terhubung</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between gap-3">
                        <span class="text-slate-400">Status</span>
                        <span id="device-status" class="font-semibold text-amber-300">Idle</span>
                    </div>
                </div>

                <p class="mt-4 text-xs leading-5 text-slate-400">
                    Web Bluetooth hanya berjalan di browser yang support dan biasanya butuh HTTPS atau localhost. Banyak printer thermal Bluetooth klasik tidak terbaca dari Web Bluetooth karena bukan BLE.
                </p>
            </section>

            <section id="printer-test-card" class="rounded-[2rem] border border-white/10 bg-white/[0.06] p-5 shadow-2xl shadow-slate-950/20 backdrop-blur-xl">
                <p class="text-sm text-slate-300">Test print</p>
                <h2 class="mt-1 text-lg font-semibold text-white">Kirim struk percobaan</h2>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="test-message" class="mb-2 block text-sm font-medium text-slate-300">Isi struk</label>
                        <textarea id="test-message" rows="8" class="w-full rounded-[1.5rem] border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none focus:border-cyan-300/60">POS Laravel
{{ $outlet->name }}

Test koneksi printer Bluetooth
Kasir: {{ $user->name }}
Waktu: {{ now()->format('d-m-Y H:i') }}

==============================
Item contoh            Rp 10.000
==============================
Total                  Rp 10.000

Printer siap dipakai.
</textarea>
                    </div>

                    <button id="test-print-button" type="button" class="inline-flex w-full items-center justify-center rounded-2xl bg-emerald-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300">
                        Test Print
                    </button>
                </div>

                <div id="message-box" class="mt-4 hidden rounded-3xl border px-4 py-3 text-sm"></div>
            </section>
        </main>

        @include('kasir.partials.mobile-nav', ['active' => 'more'])
    </div>

    <script>
        const presetField = document.getElementById('printer-preset');
        const serviceUuidField = document.getElementById('service-uuid');
        const characteristicUuidField = document.getElementById('characteristic-uuid');
        const connectButton = document.getElementById('connect-button');
        const disconnectButton = document.getElementById('disconnect-button');
        const clearConfigButton = document.getElementById('clear-config-button');
        const testPrintButton = document.getElementById('test-print-button');
        const supportBadge = document.getElementById('support-badge');
        const supportText = document.getElementById('support-text');
        const deviceName = document.getElementById('device-name');
        const deviceStatus = document.getElementById('device-status');
        const messageBox = document.getElementById('message-box');
        const testMessageField = document.getElementById('test-message');

        const storageKey = 'kasir.bluetoothPrinterConfig';
        const presetMap = {
            custom: { serviceUuid: '', characteristicUuid: '' },
            'ble-ffe0': {
                serviceUuid: '0000ffe0-0000-1000-8000-00805f9b34fb',
                characteristicUuid: '0000ffe1-0000-1000-8000-00805f9b34fb',
            },
            'ble-18f0': {
                serviceUuid: '000018f0-0000-1000-8000-00805f9b34fb',
                characteristicUuid: '00002af1-0000-1000-8000-00805f9b34fb',
            },
        };

        const state = {
            device: null,
            characteristic: null,
            config: null,
        };

        const supportsBluetooth = Boolean(navigator.bluetooth);

        const renderSupport = () => {
            if (supportsBluetooth) {
                supportBadge.textContent = 'Didukung';
                supportBadge.className = 'rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-100';
                supportText.textContent = 'Browser ini mendukung Web Bluetooth. Anda bisa pairing printer BLE dan simpan UUID agar bisa dipakai lagi.';
                return;
            }

            supportBadge.textContent = 'Tidak Support';
            supportBadge.className = 'rounded-full border border-rose-400/20 bg-rose-500/10 px-3 py-1 text-xs font-semibold text-rose-100';
            supportText.textContent = 'Browser ini tidak menyediakan Web Bluetooth. Gunakan Chrome/Edge di Android atau desktop yang mendukung fitur ini.';
        };

        const setStatus = (label, tone = 'amber') => {
            const toneMap = {
                amber: 'text-amber-300',
                emerald: 'text-emerald-300',
                rose: 'text-rose-300',
                slate: 'text-slate-200',
            };

            deviceStatus.textContent = label;
            deviceStatus.className = `font-semibold ${toneMap[tone] ?? toneMap.amber}`;
        };

        const showMessage = (text, tone = 'info') => {
            const toneMap = {
                info: 'border-cyan-400/20 bg-cyan-500/10 text-cyan-50',
                success: 'border-emerald-400/20 bg-emerald-500/10 text-emerald-50',
                error: 'border-rose-400/20 bg-rose-500/10 text-rose-50',
            };

            messageBox.textContent = text;
            messageBox.className = `mt-4 rounded-3xl border px-4 py-3 text-sm ${toneMap[tone] ?? toneMap.info}`;
            messageBox.classList.remove('hidden');
        };

        const hideMessage = () => {
            messageBox.classList.add('hidden');
        };

        const saveConfig = config => {
            localStorage.setItem(storageKey, JSON.stringify(config));
            state.config = config;
        };

        const loadConfig = () => {
            try {
                const saved = JSON.parse(localStorage.getItem(storageKey) ?? 'null');

                if (!saved) {
                    return;
                }

                state.config = saved;
                presetField.value = saved.preset && presetMap[saved.preset] ? saved.preset : 'custom';
                serviceUuidField.value = saved.serviceUuid ?? '';
                characteristicUuidField.value = saved.characteristicUuid ?? '';
                deviceName.textContent = saved.deviceName || 'Belum terhubung';
                setStatus('Konfigurasi tersimpan', 'slate');
            } catch (error) {
                localStorage.removeItem(storageKey);
            }
        };

        const applyPreset = preset => {
            const selectedPreset = presetMap[preset] ?? presetMap.custom;

            serviceUuidField.value = selectedPreset.serviceUuid;
            characteristicUuidField.value = selectedPreset.characteristicUuid;
        };

        const getConfigFromForm = () => ({
            preset: presetField.value,
            serviceUuid: serviceUuidField.value.trim().toLowerCase(),
            characteristicUuid: characteristicUuidField.value.trim().toLowerCase(),
        });

        const validateConfig = config => {
            if (!supportsBluetooth) {
                throw new Error('Browser ini belum mendukung Web Bluetooth.');
            }

            if (!config.serviceUuid || !config.characteristicUuid) {
                throw new Error('Service UUID dan Characteristic UUID wajib diisi.');
            }
        };

        const onDisconnected = () => {
            state.characteristic = null;
            deviceName.textContent = state.config?.deviceName || 'Belum terhubung';
            setStatus('Terputus', 'rose');
        };

        const connectPrinter = async () => {
            hideMessage();

            const config = getConfigFromForm();
            validateConfig(config);

            setStatus('Meminta akses printer...', 'amber');

            const device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: [config.serviceUuid],
            });

            const server = await device.gatt.connect();
            const service = await server.getPrimaryService(config.serviceUuid);
            const characteristic = await service.getCharacteristic(config.characteristicUuid);

            if (state.device) {
                state.device.removeEventListener('gattserverdisconnected', onDisconnected);
            }

            device.addEventListener('gattserverdisconnected', onDisconnected);

            state.device = device;
            state.characteristic = characteristic;

            saveConfig({
                ...config,
                deviceName: device.name || 'Printer Bluetooth',
            });

            deviceName.textContent = device.name || 'Printer Bluetooth';
            setStatus('Terhubung', 'emerald');
            showMessage('Printer berhasil dihubungkan. Anda bisa lanjut test print.', 'success');
        };

        const disconnectPrinter = () => {
            hideMessage();

            if (state.device?.gatt?.connected) {
                state.device.gatt.disconnect();
            }

            state.characteristic = null;
            setStatus('Terputus', 'rose');
            showMessage('Koneksi printer diputus dari halaman ini.', 'info');
        };

        const chunkBytes = (bytes, size = 180) => {
            const chunks = [];

            for (let index = 0; index < bytes.length; index += size) {
                chunks.push(bytes.slice(index, index + size));
            }

            return chunks;
        };

        const sleep = delay => new Promise(resolve => setTimeout(resolve, delay));

        const writeChunk = async chunk => {
            if (typeof state.characteristic.writeValueWithoutResponse === 'function') {
                await state.characteristic.writeValueWithoutResponse(chunk);
                return;
            }

            await state.characteristic.writeValue(chunk);
        };

        const printTestReceipt = async () => {
            hideMessage();

            if (!state.characteristic) {
                throw new Error('Printer belum terhubung. Hubungkan printer dulu.');
            }

            const encoder = new TextEncoder();
            const message = testMessageField.value.trim();
            const payload = new Uint8Array([
                0x1b, 0x40,
                0x1b, 0x61, 0x01,
                ...encoder.encode(message + '\n\n\n'),
                0x1d, 0x56, 0x00,
            ]);

            setStatus('Mengirim data print...', 'amber');

            for (const chunk of chunkBytes(payload)) {
                await writeChunk(chunk);
                await sleep(60);
            }

            setStatus('Print terkirim', 'emerald');
            showMessage('Data test print berhasil dikirim ke printer.', 'success');
        };

        presetField.addEventListener('change', event => {
            applyPreset(event.target.value);
        });

        connectButton.addEventListener('click', async () => {
            try {
                await connectPrinter();
            } catch (error) {
                setStatus('Gagal terhubung', 'rose');
                showMessage(error.message || 'Koneksi printer gagal.', 'error');
            }
        });

        disconnectButton.addEventListener('click', () => {
            disconnectPrinter();
        });

        clearConfigButton.addEventListener('click', () => {
            localStorage.removeItem(storageKey);
            state.config = null;
            presetField.value = 'custom';
            serviceUuidField.value = '';
            characteristicUuidField.value = '';
            deviceName.textContent = 'Belum terhubung';
            setStatus('Konfigurasi direset', 'slate');
            showMessage('Konfigurasi printer lokal sudah dihapus dari browser ini.', 'info');
        });

        testPrintButton.addEventListener('click', async () => {
            try {
                await printTestReceipt();
            } catch (error) {
                setStatus('Print gagal', 'rose');
                showMessage(error.message || 'Test print gagal dikirim.', 'error');
            }
        });

        renderSupport();
        loadConfig();
    </script>
    @include('kasir.partials.theme-script')
</body>
</html>
