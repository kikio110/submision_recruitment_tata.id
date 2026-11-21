<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['id', 'title', 'action', 'method' => 'POST', 'confirm' => 'Ya, lanjutkan', 'danger' => false]));

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

foreach (array_filter((['id', 'title', 'action', 'method' => 'POST', 'confirm' => 'Ya, lanjutkan', 'danger' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-data="{ open: false }" x-on:open-modal.window="if ($event.detail === '<?php echo e($id); ?>') open = true" x-on:keydown.escape.window="open = false" x-show="open" x-cloak class="fixed inset-0 z-50 grid place-items-center p-4" style="display:none">
    <div class="absolute inset-0 bg-navy-900/50" @click="open = false"></div>
    <form method="POST" action="<?php echo e($action); ?>" class="relative bg-white rounded-lg border border-line shadow-float w-full max-w-md p-5">
        <?php echo csrf_field(); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($method !== 'POST'): ?> <?php echo method_field($method); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <h2 class="mb-2"><?php echo e($title); ?></h2>
        <div class="text-[13px] text-ink-700 mb-4"><?php echo e($slot); ?></div>
        <div class="flex justify-end gap-2"><button type="button" class="btn-secondary" @click="open = false">Batal</button>
            <button class="<?php echo e($danger ? 'btn-danger' : 'btn-accent'); ?>"><?php echo e($confirm); ?></button></div>
    </form>
</div>
<?php /**PATH D:\program\laragon\www\ontrack-project-intelligence\resources\views/components/modal-confirm.blade.php ENDPATH**/ ?>