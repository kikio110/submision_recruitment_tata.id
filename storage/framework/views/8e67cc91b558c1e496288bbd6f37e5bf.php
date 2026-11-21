<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['assessment']));

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

foreach (array_filter((['assessment']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="space-y-3">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $assessment->criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $unknown = $c['status'] === 'UNKNOWN'; $s = $c['score'] ?? 0;
             $color = $unknown ? 'bg-slate-300' : ($s >= 70 ? 'bg-crit-fg' : ($s >= 35 ? 'bg-warn-fg' : 'bg-good-fg')); ?>
        <div>
            <div class="flex items-center justify-between text-[13px]">
                <div class="font-medium"><?php echo e($c['label']); ?> <span class="text-ink-500 font-normal">· bobot <?php echo e(round($c['weight'] * 100)); ?>%</span></div>
                <div class="tabular-nums font-semibold"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unknown): ?><span class="badge bg-unv-bg text-unv-fg border-unv-bd">UNKNOWN</span><?php else: ?> <?php echo e(number_format($s, 1)); ?><span class="text-ink-500 font-normal"> / 100 → +<?php echo e($c['contribution']); ?> poin</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
            </div>
            <div class="h-1.5 bg-well rounded mt-1.5 overflow-hidden"><div class="h-full <?php echo e($color); ?>" style="width: <?php echo e($unknown ? 100 : $s); ?>%; <?php echo e($unknown ? 'opacity:.35' : ''); ?>"></div></div>
            <div class="text-[12px] text-ink-500 mt-1"><?php echo e($c['reason']); ?></div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH D:\program\laragon\www\ontrack-project-intelligence\resources\views/components/score-breakdown.blade.php ENDPATH**/ ?>