<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['type' => 'info']));

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

foreach (array_filter((['type' => 'info']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php $c = ['success' => 'bg-good-bg border-good-bd text-good-fg', 'error' => 'bg-crit-bg border-crit-bd text-crit-fg', 'warning' => 'bg-warn-bg border-warn-bd text-warn-fg', 'info' => 'bg-pend-bg border-pend-bd text-pend-fg'][$type]; ?>
<div x-data="{ open: true }" x-show="open" role="alert" class="mb-4 flex items-start justify-between gap-3 border rounded px-3 py-2 text-[13px] <?php echo e($c); ?>">
    <div><?php echo e($slot); ?></div><button type="button" @click="open = false" class="opacity-60 hover:opacity-100" aria-label="Tutup">✕</button>
</div>
<?php /**PATH D:\program\laragon\www\ontrack-project-intelligence\resources\views/components/alert.blade.php ENDPATH**/ ?>