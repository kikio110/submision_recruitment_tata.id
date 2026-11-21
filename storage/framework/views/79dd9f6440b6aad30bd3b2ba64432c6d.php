<div class="card card-pad" x-data="{ over: false }">
    <h2 class="mb-1">Unggah data sumber</h2>
    <p class="text-[13px] text-ink-500 mb-3">Project Master (CSV), Project Updates (CSV), dan Production Data (JSON). Jenis file dikenali dari struktur, bukan nama file. Maksimal <?php echo e(config('ontrack.upload.max_kb')); ?> KB per file.</p>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! \App\Support\CurrentUser::get()?->isDirector()): ?>
        <?php if (isset($component)) { $__componentOriginal5194778a3a7b899dcee5619d0610f5cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert','data' => ['type' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'warning']); ?>Unggah data hanya tersedia untuk Director. Pilih demo user Director pada header. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $attributes = $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $component = $__componentOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <label @dragover.prevent="over = true" @dragleave.prevent="over = false" @drop.prevent="over = false; $refs.input.files = $event.dataTransfer.files; $refs.input.dispatchEvent(new Event('change', { bubbles: true }))"
        :class="over ? 'border-accent-600 bg-pend-bg' : 'border-slate-300 bg-canvas'" class="block border-2 border-dashed rounded-lg px-4 py-8 text-center cursor-pointer transition-colors">
        <div class="text-[14px] font-medium">Seret &amp; lepas file di sini</div>
        <div class="text-[12px] text-ink-500">atau klik untuk memilih (bisa lebih dari satu)</div>
        <input x-ref="input" type="file" multiple wire:model="files" accept=".csv,.json,.txt" class="sr-only" aria-label="Pilih file">
    </label>
    <div wire:loading wire:target="files" class="text-[12px] text-ink-500 mt-2">Mengunggah…</div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['files'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['files.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($error): ?><div class="field-error"><?php echo e($error); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($files): ?>
        <ul class="mt-3 divide-y divide-well border border-line rounded">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex items-center justify-between px-3 py-2 text-[13px]"><span>📄 <?php echo e($f->getClientOriginalName()); ?> <span class="text-ink-500">· <?php echo e(number_format($f->getSize() / 1024, 1)); ?> KB</span></span>
                    <button type="button" wire:click="removeFile(<?php echo e($i); ?>)" class="text-ink-500 hover:text-crit-fg" aria-label="Hapus">✕</button></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mt-3 flex flex-wrap items-end gap-3">
        <div><label class="label-sm block mb-1" for="asof">Tanggal acuan (as-of)</label><input id="asof" type="datetime-local" wire:model="asOf" class="input !w-56"></div>
        <button type="button" class="btn-accent" wire:click="import" wire:confirm="Proses file yang dipilih? Pipeline akan memvalidasi, menyimpan, dan menghitung ulang temuan serta attention." wire:loading.attr="disabled" wire:target="import" <?php if(! $files): echo 'disabled'; endif; ?>>
            <span wire:loading.remove wire:target="import">Proses data</span><span wire:loading wire:target="import">Memproses…</span></button>
    </div>

    <div class="mt-4 text-[12px] text-ink-500">Alur pemrosesan: Unggah → Validasi → Normalisasi → Simpan → Susun konteks proyek → Olah evidence → Interpretasi → Hitung attention → Verifikasi ulang → Dashboard diperbarui.</div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results): ?>
        <div class="mt-5 space-y-3">
            <h2>Ringkasan proses</h2>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-line rounded-lg p-3">
                    <div class="flex items-center justify-between gap-2"><div class="font-medium"><?php echo e($r['file_name']); ?> <span class="text-ink-500 font-normal text-[12px]"><?php echo e(str_replace('_', ' ', $r['source_kind'] ?? 'tidak dikenali')); ?></span></div><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['value' => $r['status'],'kind' => 'import']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r['status']),'kind' => 'import']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?></div>
                    <div class="grid grid-cols-3 md:grid-cols-7 gap-2 mt-2 text-center">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['total_records' => 'Total', 'valid_records' => 'Valid', 'invalid_records' => 'Invalid', 'missing_field_count' => 'Field kosong', 'duplicate_records' => 'Duplikat', 'conflicting_records' => 'Konflik/revisi', 'new_records' => 'Baru']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-canvas rounded p-2"><div class="text-[18px] font-bold tabular-nums"><?php echo e($r[$k]); ?></div><div class="label-sm"><?php echo e($l); ?></div></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <p class="text-[12px] text-ink-700 mt-2"><?php echo e($r['message']); ?></p>
                    <a href="<?php echo e(route('data.imports.show', $r['id'])); ?>" class="text-[12px] text-accent-600 font-medium">Lihat detail &amp; kesalahan validasi →</a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH D:\program\laragon\www\ontrack-project-intelligence\resources\views/livewire/data-upload.blade.php ENDPATH**/ ?>