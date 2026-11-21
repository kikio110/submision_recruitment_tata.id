<?php $__env->startSection('crumb', 'Settings'); ?>
<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginal8b54caccbdedc8030792c13949386bbd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8b54caccbdedc8030792c13949386bbd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-title','data' => ['title' => 'Settings','sub' => 'Tanggal acuan dan parameter interpretasi (keputusan desain, bukan fakta data — SRS A-04).']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-title'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Settings','sub' => 'Tanggal acuan dan parameter interpretasi (keputusan desain, bukan fakta data — SRS A-04).']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8b54caccbdedc8030792c13949386bbd)): ?>
<?php $attributes = $__attributesOriginal8b54caccbdedc8030792c13949386bbd; ?>
<?php unset($__attributesOriginal8b54caccbdedc8030792c13949386bbd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8b54caccbdedc8030792c13949386bbd)): ?>
<?php $component = $__componentOriginal8b54caccbdedc8030792c13949386bbd; ?>
<?php unset($__componentOriginal8b54caccbdedc8030792c13949386bbd); ?>
<?php endif; ?>
<div class="grid lg:grid-cols-2 gap-4">
    <form method="POST" action="<?php echo e(route('settings.update')); ?>" class="card card-pad"><?php echo csrf_field(); ?>
        <h2 class="mb-1">Tanggal acuan (as-of)</h2><p class="text-[13px] text-ink-500 mb-3">Interpretasi memakai tanggal ini, bukan jam sistem. Mengubahnya menghitung ulang semua temuan dan attention.</p>
        <input type="datetime-local" name="as_of" value="<?php echo e(old('as_of', $asOf->format('Y-m-d\TH:i'))); ?>" class="input !w-64" required><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['as_of'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="mt-3"><button class="btn-accent" onclick="return confirm('Ubah tanggal acuan dan hitung ulang semua interpretasi?')" <?php if(! \App\Support\CurrentUser::get()?->isDirector()): echo 'disabled'; endif; ?>>Simpan &amp; hitung ulang</button></div></form>
    <div class="card card-pad"><h2 class="mb-2">Bobot attention</h2><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $weights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="flex justify-between text-[13px] py-1 border-b border-well"><span><?php echo e(ucwords(str_replace('_', ' ', $k))); ?></span><b><?php echo e(round($w * 100)); ?>%</b></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <p class="text-[12px] text-ink-500 mt-2">Level: HIGH ≥ <?php echo e($levels['HIGH']); ?>, MEDIUM ≥ <?php echo e($levels['MEDIUM']); ?>; aturan lantai berlaku untuk temuan CRITICAL/HIGH/MEDIUM.</p></div>
    <div class="card card-pad lg:col-span-2"><h2 class="mb-2">Ambang interpretasi</h2><div class="grid md:grid-cols-2 gap-x-8"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $thresholds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php [$label, $unit] = $labels[$k] ?? [ucfirst(str_replace('_', ' ', $k)), ''];?><div class="flex justify-between gap-3 text-[13px] py-1.5 border-b border-well"><span class="text-ink-700"><?php echo e($label); ?><span class="block text-[11px] text-ink-500"><?php echo e($unit); ?></span></span><b class="tabular-nums"><?php echo e($v); ?></b></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></div>
    <div class="card card-pad lg:col-span-2"><h2 class="mb-1">Keputusan desain &amp; kondisi tinjau ulang</h2><p class="text-[13px] text-ink-500 mb-3">Aturan prioritas, model data, dan cara menjelaskan ketidakpastian ditetapkan oleh tim produk. Berikut alasan setiap keputusan dan kapan harus ditinjau ulang.</p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $decisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <details class="border border-line rounded-lg mb-2 group" <?php if($loop->first): ?> open <?php endif; ?>><summary class="cursor-pointer px-3 py-2.5 font-medium text-[13px] flex justify-between"><?php echo e($d['topic']); ?><span class="text-ink-500 group-open:rotate-180">⌄</span></summary>
                <dl class="px-3 pb-3 grid md:grid-cols-2 gap-x-6 gap-y-2 text-[13px]">
                    <div><dt class="label-sm">Keputusan</dt><dd><?php echo e($d['decision']); ?></dd></div><div><dt class="label-sm">Alasan</dt><dd><?php echo e($d['why']); ?></dd></div>
                    <div><dt class="label-sm">Penanganan ketidakpastian</dt><dd><?php echo e($d['uncertainty']); ?></dd></div><div><dt class="label-sm">Tinjau ulang bila</dt><dd><?php echo e($d['review']); ?></dd></div></dl></details>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\program\laragon\www\ontrack-project-intelligence\resources\views/pages/settings.blade.php ENDPATH**/ ?>