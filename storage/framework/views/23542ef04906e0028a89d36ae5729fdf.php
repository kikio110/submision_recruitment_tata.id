<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['e', 'role' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['e', 'role' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php $op = $e->evidence_class === 'OPERATIONAL'; ?>
<div class="border rounded-lg p-3 <?php echo e($op ? 'border-line bg-white' : 'border-dashed border-slate-300 bg-canvas'); ?> <?php echo e($e->is_current ? '' : 'opacity-75'); ?>" id="evidence-<?php echo e($e->id); ?>">
    <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($op): ?>
            <span class="badge bg-good-bg text-good-fg border-good-bd">🛡 Operational evidence</span>
        <?php else: ?>
            <span class="badge bg-well text-ink-700 border-dashed border-slate-400">👤 Reported</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <span class="badge bg-white text-ink-700 border-line"><?php echo e($e->category); ?></span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($role === 'CONTRAST'): ?><span class="badge bg-warn-bg text-warn-fg border-warn-bd">Pembanding</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $e->is_current): ?><span class="badge bg-well text-ink-500 border-line">Riwayat (superseded)</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($e->is_stale): ?><span class="badge bg-warn-bg text-warn-fg border-warn-bd">Stale</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($e->is_duplicate): ?><span class="badge bg-well text-ink-700 border-line">Duplikat sumber</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($e->conflict_group): ?><span class="badge bg-crit-bg text-crit-fg border-crit-bd">⚠ Conflicting</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($e->revision > 1): ?><span class="badge bg-pend-bg text-pend-fg border-pend-bd">Rev <?php echo e($e->revision); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="text-[13px] <?php echo e($e->metric_key === 'update_message' ? '' : 'font-medium'); ?>"><?php echo e($e->summary); ?></div>
    <dl class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-1 text-[12px]">
        <div><dt class="label-sm">Sumber</dt><dd class="text-ink-700"><?php echo e($e->sourceText()); ?></dd></div>
        <div><dt class="label-sm">Referensi</dt><dd class="font-mono text-[11px] text-ink-700"><?php echo e($e->source_ref); ?></dd></div>
        <div><dt class="label-sm">Timestamp</dt><dd class="text-ink-700"><?php echo e($e->observed_at->format('d M Y H:i')); ?></dd></div>
        <div><dt class="label-sm">Nilai teramati</dt><dd class="text-ink-700"><?php echo e($e->metric_key === 'update_message' ? 'pesan teks' : ($e->observed_value ?? 'null') . ($e->unit ? ' ' . $e->unit : '')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($e->subject_key && $e->metric_key !== 'update_message'): ?> <span class="text-ink-500">(<?php echo e($e->subject_key); ?>)</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></dd></div>
    </dl>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($e->signals): ?><div class="mt-1.5 flex gap-1 flex-wrap"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $e->signalList(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="badge bg-white text-ink-500 border-line"><?php echo e($s); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH D:\program\laragon\www\ontrack-project-intelligence\resources\views/components/evidence-card.blade.php ENDPATH**/ ?>