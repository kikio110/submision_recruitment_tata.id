<?php $__env->startSection('crumb', 'Tinjauan bukti'); ?>
<?php $__env->startSection('content'); ?>
<?php $isDir = \App\Support\CurrentUser::get()?->isDirector(); ?>
<?php if (isset($component)) { $__componentOriginal8b54caccbdedc8030792c13949386bbd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8b54caccbdedc8030792c13949386bbd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-title','data' => ['title' => 'Tinjauan bukti operasional','sub' => $isDir ? 'Bukti yang diajukan PIC menunggu persetujuan Anda. Hanya yang disetujui dihitung sebagai evidence.' : 'Status pengajuan bukti untuk proyek Anda.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-title'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Tinjauan bukti operasional','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isDir ? 'Bukti yang diajukan PIC menunggu persetujuan Anda. Hanya yang disetujui dihitung sebagai evidence.' : 'Status pengajuan bukti untuk proyek Anda.')]); ?>
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
<div class="flex gap-2 mb-3">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['PENDING' => 'Menunggu', 'APPROVED' => 'Disetujui', 'REJECTED' => 'Ditolak']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('evidence.index', ['status' => $k])); ?>" class="btn <?php echo e($status === $k ? 'bg-navy-900 text-white border-navy-900' : 'btn-secondary'); ?>"><?php echo e($l); ?> <span class="opacity-70 tabular-nums"><?php echo e($counts[$k] ?? 0); ?></span></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <a href="<?php echo e(route('evidence.index', ['status' => 'ALL'])); ?>" class="btn-ghost">Semua</a>
</div>
<div class="space-y-3">
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card card-pad" id="submission-<?php echo e($s->id); ?>">
        <div class="flex flex-wrap items-center gap-2">
            <span class="badge <?php echo e(['PENDING' => 'bg-pend-bg text-pend-fg border-pend-bd', 'APPROVED' => 'bg-good-bg text-good-fg border-good-bd', 'REJECTED' => 'bg-crit-bg text-crit-fg border-crit-bd'][$s->status]); ?>"><?php echo e(['PENDING' => 'Menunggu tinjauan', 'APPROVED' => 'Disetujui', 'REJECTED' => 'Ditolak'][$s->status]); ?></span>
            <a href="<?php echo e(route('projects.show', $s->project)); ?>" class="font-medium text-accent-600"><?php echo e($s->project->project_code); ?> · <?php echo e($s->project->name); ?></a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s->followUp): ?><a href="<?php echo e(route('follow-ups.show', $s->followUp)); ?>" class="text-[12px] text-accent-600">follow-up #<?php echo e($s->follow_up_id); ?></a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span class="ml-auto text-[11px] text-ink-500">diajukan <?php echo e($s->created_at->format('d M Y H:i')); ?> oleh <?php echo e($s->submitter?->name ?? '—'); ?></span></div>
        <div class="mt-2 text-[14px] font-medium"><?php echo e($s->metricLabel()); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s->subject_key): ?> <span class="text-ink-500">(<?php echo e($s->subject_key); ?>)</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> = <?php echo e($s->observed_value); ?> <?php echo e($s->unit); ?></div>
        <dl class="grid md:grid-cols-3 gap-3 mt-2 text-[12px]"><div><dt class="label-sm">Waktu observasi</dt><dd><?php echo e($s->observed_at->format('d M Y H:i')); ?></dd></div><div><dt class="label-sm">Sistem asal</dt><dd><?php echo e($s->source_system); ?></dd></div><div><dt class="label-sm">Asal bukti</dt><dd class="whitespace-pre-line"><?php echo e($s->source_note); ?></dd></div></dl>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s->status !== 'PENDING'): ?><div class="mt-2 text-[12px] bg-canvas rounded p-2">Ditinjau <?php echo e($s->reviewed_at?->format('d M Y H:i')); ?> oleh <?php echo e($s->reviewer?->name); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s->review_note): ?> — <?php echo e($s->review_note); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isDir && $s->status === 'PENDING'): ?>
            <div class="mt-3 flex gap-2"><button type="button" class="btn-accent" x-data @click="$dispatch('open-modal', 'approve-<?php echo e($s->id); ?>')">Setujui</button><button type="button" class="btn-danger" x-data @click="$dispatch('open-modal', 'reject-<?php echo e($s->id); ?>')">Tolak</button></div>
            <?php if (isset($component)) { $__componentOriginal7ef94aa801410a663a471c55b223c943 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ef94aa801410a663a471c55b223c943 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal-confirm','data' => ['id' => 'approve-' . $s->id,'title' => 'Setujui bukti','action' => route('evidence.approve', $s),'confirm' => 'Setujui & hitung']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal-confirm'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('approve-' . $s->id),'title' => 'Setujui bukti','action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('evidence.approve', $s)),'confirm' => 'Setujui & hitung']); ?>Bukti ini akan dihitung sebagai evidence operasional. Analisis, attention, dan verifikasi follow-up terkait dijalankan ulang.<label class="label-sm block mt-3 mb-1">Catatan (opsional)</label><textarea name="review_note" rows="2" class="input"></textarea> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7ef94aa801410a663a471c55b223c943)): ?>
<?php $attributes = $__attributesOriginal7ef94aa801410a663a471c55b223c943; ?>
<?php unset($__attributesOriginal7ef94aa801410a663a471c55b223c943); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7ef94aa801410a663a471c55b223c943)): ?>
<?php $component = $__componentOriginal7ef94aa801410a663a471c55b223c943; ?>
<?php unset($__componentOriginal7ef94aa801410a663a471c55b223c943); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7ef94aa801410a663a471c55b223c943 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ef94aa801410a663a471c55b223c943 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal-confirm','data' => ['id' => 'reject-' . $s->id,'title' => 'Tolak bukti','action' => route('evidence.reject', $s),'confirm' => 'Tolak','danger' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal-confirm'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('reject-' . $s->id),'title' => 'Tolak bukti','action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('evidence.reject', $s)),'confirm' => 'Tolak','danger' => true]); ?>Bukti tidak akan dihitung sebagai evidence.<label class="label-sm block mt-3 mb-1">Alasan penolakan *</label><textarea name="review_note" rows="2" class="input" required></textarea> <?php echo $__env->renderComponent(); ?>
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
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="card"><?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['title' => 'Tidak ada pengajuan','text' => ''.e($isDir ? 'Tidak ada bukti dengan status ini.' : 'Anda belum mengajukan bukti. Gunakan tombol Kirim bukti di detail proyek atau follow-up.').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Tidak ada pengajuan','text' => ''.e($isDir ? 'Tidak ada bukti dengan status ini.' : 'Anda belum mengajukan bukti. Gunakan tombol Kirim bukti di detail proyek atau follow-up.').'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4f22a152e0729cd34293e65bd200d933)): ?>
<?php $attributes = $__attributesOriginal4f22a152e0729cd34293e65bd200d933; ?>
<?php unset($__attributesOriginal4f22a152e0729cd34293e65bd200d933); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4f22a152e0729cd34293e65bd200d933)): ?>
<?php $component = $__componentOriginal4f22a152e0729cd34293e65bd200d933; ?>
<?php unset($__componentOriginal4f22a152e0729cd34293e65bd200d933); ?>
<?php endif; ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<div class="mt-3"><?php echo e($items->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\program\laragon\www\ontrack-project-intelligence\resources\views/pages/evidence/index.blade.php ENDPATH**/ ?>