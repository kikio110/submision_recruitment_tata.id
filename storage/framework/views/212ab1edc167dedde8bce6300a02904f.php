<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['value', 'kind' => 'attention']));

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

foreach (array_filter((['value', 'kind' => 'attention']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $v = strtoupper((string) $value);
    $map = [
        'crit' => 'bg-crit-bg text-crit-fg border-crit-bd', 'warn' => 'bg-warn-bg text-warn-fg border-warn-bd', 'good' => 'bg-good-bg text-good-fg border-good-bd',
        'pend' => 'bg-pend-bg text-pend-fg border-pend-bd', 'unv' => 'bg-unv-bg text-unv-fg border-unv-bd', 'neutral' => 'bg-well text-ink-700 border-line',
    ];
    $tone = match ($kind) {
        'attention', 'severity' => match ($v) { 'HIGH', 'CRITICAL' => 'crit', 'MEDIUM' => 'warn', 'LOW' => 'good', default => 'neutral' },
        'verification' => match ($v) { 'VERIFIED' => 'good', 'UNVERIFIED' => 'unv', 'PENDING_VERIFICATION' => 'pend', 'REOPENED' => 'crit', default => 'neutral' },
        'followup' => match ($v) { 'OPEN' => 'pend', 'IN_PROGRESS' => 'warn', 'RESOLVED' => 'good', default => 'neutral' },
        'priority' => match ($v) { 'CRITICAL', 'HIGH' => 'crit', 'MEDIUM' => 'warn', default => 'neutral' },
        'import' => match ($v) { 'COMPLETED' => 'good', 'COMPLETED_WITH_WARNINGS' => 'warn', 'FAILED', 'REJECTED' => 'crit', default => 'pend' },
        default => 'neutral',
    };
    $labels = ['PENDING_VERIFICATION' => 'Pending verification', 'IN_PROGRESS' => 'In progress', 'COMPLETED_WITH_WARNINGS' => 'Completed w/ warnings', 'OUT_OF_SERVICE' => 'Out of service'];
    $label = $labels[$v] ?? ucfirst(strtolower(str_replace('_', ' ', $v)));
?>
<span <?php echo e($attributes->merge(['class' => "badge {$map[$tone]}"])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($v === 'PENDING_VERIFICATION'): ?><span class="w-1.5 h-1.5 rounded-full bg-pend-fg animate-pulse"></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php echo e($label); ?></span>
<?php /**PATH D:\program\laragon\www\ontrack-project-intelligence\resources\views/components/badge.blade.php ENDPATH**/ ?>