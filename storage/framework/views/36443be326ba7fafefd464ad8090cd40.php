<?php $__env->startSection('crumb', 'Data Management'); ?>
<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginal8b54caccbdedc8030792c13949386bbd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8b54caccbdedc8030792c13949386bbd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-title','data' => ['title' => 'Data Management','sub' => 'Impor CSV/JSON, validasi, dan segarkan interpretasi tanpa kehilangan follow-up.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-title'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Data Management','sub' => 'Impor CSV/JSON, validasi, dan segarkan interpretasi tanpa kehilangan follow-up.']); ?>
    <a href="<?php echo e(route('data.imports')); ?>" class="btn-secondary">Riwayat import</a>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Support\CurrentUser::get()?->isDirector()): ?><a href="<?php echo e(route('projects.create')); ?>" class="btn-secondary">+ Tambah proyek manual</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Support\CurrentUser::get()?->isDirector()): ?>
        <button type="button" class="btn-secondary" x-data @click="$dispatch('open-modal', 'reprocess')">↻ Hitung ulang</button>
        <?php if (isset($component)) { $__componentOriginal7ef94aa801410a663a471c55b223c943 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ef94aa801410a663a471c55b223c943 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal-confirm','data' => ['id' => 'reprocess','title' => 'Hitung ulang semua proyek','action' => route('data.reprocess'),'confirm' => 'Hitung ulang']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal-confirm'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'reprocess','title' => 'Hitung ulang semua proyek','action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('data.reprocess')),'confirm' => 'Hitung ulang']); ?>Interpretasi, attention, dan verifikasi dihitung ulang dari data tersimpan. Follow-up tidak berubah. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7ef94aa801410a663a471c55b223c943)): ?>
<?php $attributes = $__attributesOriginal7ef94aa801410a663a471c55b223c943; ?>
<?php unset($__attributesOriginal7ef94aa801410a663a471c55b223c943); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7ef94aa801410a663a471c55b223c943)): ?>
<?php $component = $__componentOriginal7ef94aa801410a663a471c55b223c943; ?>
<?php unset($__componentOriginal7ef94aa801410a663a471c55b223c943); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-5">
    <?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Proyek','value' => $counts['projects']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Proyek','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($counts['projects'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?><?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Update aktif','value' => $counts['updates']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Update aktif','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($counts['updates'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?><?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Record produksi','value' => $counts['production']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Record produksi','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($counts['production'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?><?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Evidence','value' => $counts['evidence']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Evidence','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($counts['evidence'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?><?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'As-of','value' => $asOf->format('d M'),'hint' => $asOf->format('Y H:i'),'tone' => 'pend']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'As-of','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($asOf->format('d M')),'hint' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($asOf->format('Y H:i')),'tone' => 'pend']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
</div>
<div class="grid xl:grid-cols-3 gap-4">
    <div class="xl:col-span-2"><?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('data-upload', []);

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-960212000-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?></div>
    <div class="card card-pad"><h2 class="mb-3">Import terakhir</h2>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><a href="<?php echo e(route('data.imports.show', $b)); ?>" class="block py-2 border-b border-well last:border-0"><div class="flex items-center justify-between gap-2"><span class="font-medium text-[13px] truncate"><?php echo e($b->file_name); ?></span><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['value' => $b->status,'kind' => 'import']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($b->status),'kind' => 'import']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?></div><div class="text-[11px] text-ink-500"><?php echo e($b->created_at->format('d M Y H:i')); ?> · <?php echo e($b->message); ?></div></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['title' => 'Belum ada import']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Belum ada import']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4f22a152e0729cd34293e65bd200d933)): ?>
<?php $attributes = $__attributesOriginal4f22a152e0729cd34293e65bd200d933; ?>
<?php unset($__attributesOriginal4f22a152e0729cd34293e65bd200d933); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4f22a152e0729cd34293e65bd200d933)): ?>
<?php $component = $__componentOriginal4f22a152e0729cd34293e65bd200d933; ?>
<?php unset($__componentOriginal4f22a152e0729cd34293e65bd200d933); ?>
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\program\laragon\www\ontrack-project-intelligence\resources\views/pages/data/index.blade.php ENDPATH**/ ?>