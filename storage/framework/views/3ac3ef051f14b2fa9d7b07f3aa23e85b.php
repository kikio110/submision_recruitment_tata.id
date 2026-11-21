<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label', 'value', 'tone' => 'neutral', 'hint' => null, 'href' => null]));

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

foreach (array_filter((['label', 'value', 'tone' => 'neutral', 'hint' => null, 'href' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php $bar = ['crit' => 'bg-crit-fg', 'warn' => 'bg-warn-fg', 'good' => 'bg-good-fg', 'pend' => 'bg-pend-fg', 'unv' => 'bg-unv-fg', 'neutral' => 'bg-slate-300'][$tone]; ?>
<<?php echo e($href ? 'a href='.$href : 'div'); ?> class="card card-pad relative overflow-hidden block <?php echo e($href ? 'hover:border-slate-300' : ''); ?>">
    <span class="absolute left-0 top-0 bottom-0 w-1 <?php echo e($bar); ?>"></span>
    <div class="label-sm"><?php echo e($label); ?></div>
    <div class="mt-1 text-[28px] font-bold leading-8 tabular-nums"><?php echo e($value); ?></div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hint): ?><div class="text-[12px] text-ink-500 mt-0.5"><?php echo e($hint); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</<?php echo e($href ? 'a' : 'div'); ?>>
<?php /**PATH D:\program\laragon\www\ontrack-project-intelligence\resources\views/components/kpi.blade.php ENDPATH**/ ?>