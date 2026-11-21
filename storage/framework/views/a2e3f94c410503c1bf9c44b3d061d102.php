<?php $__env->startSection('crumb', 'Follow-ups / #' . $f->id); ?>
<?php $__env->startSection('content'); ?>
<?php $fd = $f->finding; $labels = ['OPEN' => 'Buka kembali', 'IN_PROGRESS' => 'Mulai / lanjutkan pengerjaan', 'RESOLVED' => 'Tandai RESOLVED', 'CANCELLED' => 'Batalkan']; ?>
<?php if (isset($component)) { $__componentOriginal8b54caccbdedc8030792c13949386bbd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8b54caccbdedc8030792c13949386bbd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-title','data' => ['title' => $f->title,'sub' => $f->project->project_code . ' · ' . $f->project->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-title'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($f->title),'sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($f->project->project_code . ' · ' . $f->project->name)]); ?>
    <a href="<?php echo e(route('projects.show', $f->project)); ?>" class="btn-secondary">Detail proyek</a>
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

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($canModify)): ?><?php if (isset($component)) { $__componentOriginal5194778a3a7b899dcee5619d0610f5cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert','data' => ['type' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'info']); ?>Mode lihat saja: follow-up ini ditugaskan kepada <?php echo e($f->assigned_pic); ?>. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $attributes = $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $component = $__componentOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="grid xl:grid-cols-3 gap-4">
<div class="xl:col-span-2 space-y-4">
    <div class="card card-pad">
        <div class="flex flex-wrap items-center gap-2 mb-3"><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['value' => $f->status,'kind' => 'followup']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($f->status),'kind' => 'followup']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['value' => $f->priority,'kind' => 'priority']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($f->priority),'kind' => 'priority']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($f->isOverdue()): ?><span class="badge bg-crit-bg text-crit-fg border-crit-bd">Overdue</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-[13px]">
            <div><dt class="label-sm">PIC</dt><dd class="font-medium"><?php echo e($f->assigned_pic); ?></dd></div><div><dt class="label-sm">Due date</dt><dd class="font-medium"><?php echo e($f->due_date?->format('d M Y') ?? '—'); ?></dd></div>
            <div><dt class="label-sm">Dibuat oleh</dt><dd><?php echo e($f->creator?->name ?? '—'); ?><div class="text-[11px] text-ink-500"><?php echo e($f->created_at->format('d M Y H:i')); ?></div></dd></div>
            <div><dt class="label-sm">Diperbarui oleh</dt><dd><?php echo e($f->updater?->name ?? '—'); ?><div class="text-[11px] text-ink-500"><?php echo e($f->updated_at->format('d M Y H:i')); ?></div></dd></div>
        </dl>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($f->description): ?><div class="mt-3"><div class="label-sm">Deskripsi</div><p class="text-[13px] whitespace-pre-line"><?php echo e($f->description); ?></p></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($f->instructions): ?><div class="mt-3"><div class="label-sm">Instruksi tambahan</div><p class="text-[13px] whitespace-pre-line"><?php echo e($f->instructions); ?></p></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canModify): ?>
        <div class="mt-4 pt-3 border-t border-line flex flex-wrap gap-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $transitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button" class="<?php echo e($t === 'RESOLVED' ? 'btn-accent' : ($t === 'CANCELLED' ? 'btn-danger' : 'btn-secondary')); ?>" x-data @click="$dispatch('open-modal', 'status-<?php echo e($t); ?>')"><?php echo e($labels[$t]); ?></button>
                <?php if (isset($component)) { $__componentOriginal7ef94aa801410a663a471c55b223c943 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ef94aa801410a663a471c55b223c943 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal-confirm','data' => ['id' => 'status-'.e($t).'','title' => $labels[$t],'action' => route('follow-ups.status', $f),'danger' => $t === 'CANCELLED']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal-confirm'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'status-'.e($t).'','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($labels[$t]),'action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('follow-ups.status', $f)),'danger' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($t === 'CANCELLED')]); ?>
                    <input type="hidden" name="status" value="<?php echo e($t); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t === 'RESOLVED'): ?><p class="mb-2">Menandai RESOLVED hanya berarti <b>tindakan selesai</b>. Sistem akan mengevaluasi evidence terbaru; issue baru VERIFIED bila evidence operasional membuktikannya.</p><?php else: ?><p class="mb-2">Ubah status dari <b><?php echo e($f->status); ?></b> ke <b><?php echo e($t); ?></b>?</p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <label class="label-sm block mb-1">Catatan (opsional)</label><textarea name="note" rows="2" class="input"></textarea>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7ef94aa801410a663a471c55b223c943)): ?>
<?php $attributes = $__attributesOriginal7ef94aa801410a663a471c55b223c943; ?>
<?php unset($__attributesOriginal7ef94aa801410a663a471c55b223c943); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7ef94aa801410a663a471c55b223c943)): ?>
<?php $component = $__componentOriginal7ef94aa801410a663a471c55b223c943; ?>
<?php unset($__componentOriginal7ef94aa801410a663a471c55b223c943); ?>
<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button type="button" class="btn-ghost ml-auto" x-data @click="$dispatch('open-modal', 'edit')">Ubah detail</button>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="card card-pad" id="verification">
        <h2 class="mb-3">Verifikasi issue</h2>
        <div class="grid md:grid-cols-2 gap-3 mb-3">
            <div class="border border-line rounded-lg p-3"><div class="label-sm">Status follow-up (tindakan)</div><div class="mt-1"><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['value' => $f->status,'kind' => 'followup']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($f->status),'kind' => 'followup']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?></div><div class="text-[11px] text-ink-500 mt-1">Menjawab: apakah tindakan sudah dikerjakan?</div></div>
            <div class="border border-line rounded-lg p-3"><div class="label-sm">Status issue (verifikasi)</div><div class="mt-1"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fd): ?><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['value' => $fd->verification_status,'kind' => 'verification']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fd->verification_status),'kind' => 'verification']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?><?php else: ?><span class="text-ink-500">Tidak terkait temuan</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><div class="text-[11px] text-ink-500 mt-1">Menjawab: apakah evidence membuktikan masalah selesai?</div></div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fd): ?>
            <div class="mb-3"><div class="label-sm">Issue asal</div><div class="flex items-center gap-2 mt-1"><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['value' => $fd->severity,'kind' => 'severity']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fd->severity),'kind' => 'severity']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?><span class="font-medium"><?php echo e($fd->title); ?></span></div><p class="text-[13px] text-ink-700 mt-1"><?php echo e($fd->description); ?></p><div class="text-[11px] text-ink-500 mt-1">Baseline evidence: <?php echo e($fd->baseline_at?->format('d M Y H:i') ?? '—'); ?></div></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($verification): ?>
                <div class="grid md:grid-cols-2 gap-3 mb-3">
                    <div><div class="label-sm mb-1">Evidence sebelumnya</div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($verification->previousEvidence): ?><?php if (isset($component)) { $__componentOriginal17772fa927aa5591d313c9fd27059a4b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal17772fa927aa5591d313c9fd27059a4b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.evidence-card','data' => ['e' => $verification->previousEvidence]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('evidence-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['e' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($verification->previousEvidence)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal17772fa927aa5591d313c9fd27059a4b)): ?>
<?php $attributes = $__attributesOriginal17772fa927aa5591d313c9fd27059a4b; ?>
<?php unset($__attributesOriginal17772fa927aa5591d313c9fd27059a4b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal17772fa927aa5591d313c9fd27059a4b)): ?>
<?php $component = $__componentOriginal17772fa927aa5591d313c9fd27059a4b; ?>
<?php unset($__componentOriginal17772fa927aa5591d313c9fd27059a4b); ?>
<?php endif; ?><?php else: ?><div class="text-[12px] text-ink-500">Tidak ada.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div><div class="label-sm mb-1">Evidence terbaru</div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($verification->latestEvidence): ?><?php if (isset($component)) { $__componentOriginal17772fa927aa5591d313c9fd27059a4b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal17772fa927aa5591d313c9fd27059a4b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.evidence-card','data' => ['e' => $verification->latestEvidence]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('evidence-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['e' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($verification->latestEvidence)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal17772fa927aa5591d313c9fd27059a4b)): ?>
<?php $attributes = $__attributesOriginal17772fa927aa5591d313c9fd27059a4b; ?>
<?php unset($__attributesOriginal17772fa927aa5591d313c9fd27059a4b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal17772fa927aa5591d313c9fd27059a4b)): ?>
<?php $component = $__componentOriginal17772fa927aa5591d313c9fd27059a4b; ?>
<?php unset($__componentOriginal17772fa927aa5591d313c9fd27059a4b); ?>
<?php endif; ?><?php else: ?><div class="border border-dashed border-unv-bd bg-unv-bg rounded-lg p-3 text-[12px] text-unv-fg">Belum ada evidence operasional yang lebih baru dari baseline.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                </div>
                <div class="border border-line rounded-lg p-3 bg-canvas"><div class="flex items-center gap-2"><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['value' => $verification->result,'kind' => 'verification']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($verification->result),'kind' => 'verification']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?><span class="text-[11px] text-ink-500"><?php echo e($verification->created_at->format('d M Y H:i')); ?> · pemicu <?php echo e(str_replace('_', ' ', strtolower($verification->trigger))); ?> · as-of <?php echo e($verification->as_of->format('d M Y H:i')); ?></span></div>
                    <p class="text-[13px] mt-1.5"><?php echo e($verification->explanation); ?></p></div>
            <?php else: ?>
                <div class="border border-dashed border-line rounded-lg p-3 text-[13px] text-ink-500">Belum pernah diverifikasi. Verifikasi berjalan otomatis saat follow-up ditandai RESOLVED atau data di-refresh.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canModify): ?><div class="mt-3 flex flex-wrap gap-2"><button type="button" class="btn-secondary" x-data @click="$dispatch('open-modal', 'verify')">↻ Verify again</button><a href="<?php echo e(route('evidence.create', ['project' => $f->project_id, 'follow_up' => $f->id])); ?>" class="btn-accent">Kirim bukti operasional</a></div>
                <p class="text-[11px] text-ink-500 mt-1.5">Untuk memverifikasi, perlu evidence operasional yang lebih baru. Bukti yang Anda kirim ditinjau Director; setelah disetujui, verifikasi ulang berjalan otomatis.</p>
                <?php if (isset($component)) { $__componentOriginal7ef94aa801410a663a471c55b223c943 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ef94aa801410a663a471c55b223c943 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal-confirm','data' => ['id' => 'verify','title' => 'Verifikasi ulang issue','action' => route('follow-ups.verify', $f),'confirm' => 'Verifikasi sekarang']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal-confirm'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'verify','title' => 'Verifikasi ulang issue','action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('follow-ups.verify', $f)),'confirm' => 'Verifikasi sekarang']); ?>Sistem akan mengevaluasi ulang evidence operasional terbaru terhadap temuan asal. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7ef94aa801410a663a471c55b223c943)): ?>
<?php $attributes = $__attributesOriginal7ef94aa801410a663a471c55b223c943; ?>
<?php unset($__attributesOriginal7ef94aa801410a663a471c55b223c943); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7ef94aa801410a663a471c55b223c943)): ?>
<?php $component = $__componentOriginal7ef94aa801410a663a471c55b223c943; ?>
<?php unset($__componentOriginal7ef94aa801410a663a471c55b223c943); ?>
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($f->evidenceSubmissions->isNotEmpty()): ?>
    <div class="card card-pad"><h2 class="mb-2">Bukti yang diajukan untuk follow-up ini</h2>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $f->evidenceSubmissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="py-2 border-b border-well last:border-0 text-[13px]"><span class="badge <?php echo e(['PENDING' => 'bg-pend-bg text-pend-fg border-pend-bd', 'APPROVED' => 'bg-good-bg text-good-fg border-good-bd', 'REJECTED' => 'bg-crit-bg text-crit-fg border-crit-bd'][$sb->status]); ?>"><?php echo e(['PENDING' => 'Menunggu tinjauan', 'APPROVED' => 'Disetujui', 'REJECTED' => 'Ditolak'][$sb->status]); ?></span> <?php echo e($sb->metricLabel()); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sb->subject_key): ?> (<?php echo e($sb->subject_key); ?>)<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> = <b><?php echo e($sb->observed_value); ?></b> <span class="text-[11px] text-ink-500">· <?php echo e($sb->observed_at->format('d M Y H:i')); ?> · <?php echo e($sb->source_system); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sb->review_note): ?><div class="text-[12px] text-ink-700 mt-0.5">Tinjauan: <?php echo e($sb->review_note); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fd && $fd->verifications->count() > 1): ?>
    <div class="card card-pad"><h2 class="mb-2">Riwayat verifikasi</h2>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $fd->verifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="py-2 border-b border-well last:border-0 text-[13px]"><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['value' => $v->result,'kind' => 'verification']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($v->result),'kind' => 'verification']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?> <span class="text-[11px] text-ink-500"><?php echo e($v->created_at->format('d M Y H:i')); ?> · <?php echo e($v->trigger); ?></span><div class="text-ink-700 mt-0.5"><?php echo e($v->explanation); ?></div></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<div class="space-y-4">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canModify): ?>
    <form method="POST" action="<?php echo e(route('follow-ups.note', $f)); ?>" class="card card-pad"><?php echo csrf_field(); ?>
        <h2 class="mb-2">Tambah catatan progres</h2><textarea name="note" rows="3" class="input" placeholder="Apa yang sudah dikerjakan?" required><?php echo e(old('note')); ?></textarea><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <label class="flex items-start gap-2 mt-2 text-[12px] text-ink-700"><input type="checkbox" name="report_to_timeline" value="1" class="mt-0.5" <?php if(old('report_to_timeline')): echo 'checked'; endif; ?>><span>Laporkan juga ke timeline proyek <span class="text-ink-500">(tercatat sebagai informasi yang dilaporkan, bukan bukti operasional)</span></span></label>
        <div class="mt-2 text-right"><button class="btn-primary">Simpan catatan</button></div></form>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="card card-pad"><h2 class="mb-3">Riwayat aktivitas</h2>
        <ol class="relative border-l border-line ml-2 space-y-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $f->histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="pl-4 relative"><span class="absolute -left-[5px] top-1.5 w-2.5 h-2.5 rounded-full bg-accent-600"></span>
                    <div class="text-[11px] text-ink-500"><?php echo e($h->created_at->format('d M Y H:i')); ?> · <?php echo e($h->actor_name ?? 'System'); ?></div>
                    <div class="text-[13px] font-medium"><?php echo e(str_replace('_', ' ', ucfirst(strtolower($h->event)))); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($h->from_value || $h->to_value): ?> <span class="text-ink-500 font-normal"><?php echo e($h->from_value); ?><?php echo e($h->from_value && $h->to_value ? ' → ' : ''); ?><?php echo e($h->to_value); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($h->note): ?><div class="text-[12px] text-ink-700 whitespace-pre-line"><?php echo e($h->note); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ol></div>
</div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canModify): ?>
<div x-data="{ open: false }" x-on:open-modal.window="if ($event.detail === 'edit') open = true" x-on:keydown.escape.window="open = false" x-show="open" x-cloak class="fixed inset-0 z-50 grid place-items-center p-4" style="display:none">
    <div class="absolute inset-0 bg-navy-900/50" @click="open = false"></div>
    <form method="POST" action="<?php echo e(route('follow-ups.update', $f)); ?>" class="relative bg-white rounded-lg border border-line shadow-float w-full max-w-lg p-5 space-y-3 max-h-[90vh] overflow-y-auto"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <h2>Ubah detail follow-up</h2>
        <div><label class="label-sm block mb-1">Judul *</label><input name="title" value="<?php echo e(old('title', $f->title)); ?>" class="input" required></div>
        <div><label class="label-sm block mb-1">Deskripsi</label><textarea name="description" rows="3" class="input"><?php echo e(old('description', $f->description)); ?></textarea></div>
        <div><label class="label-sm block mb-1">Instruksi tambahan</label><textarea name="instructions" rows="2" class="input"><?php echo e(old('instructions', $f->instructions)); ?></textarea></div>
        <div class="grid grid-cols-3 gap-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Support\CurrentUser::get()?->isDirector()): ?><div><label class="label-sm block mb-1">PIC *</label><input name="assigned_pic" list="pics2" value="<?php echo e(old('assigned_pic', $f->assigned_pic)); ?>" class="input" required><datalist id="pics2"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></datalist></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div><label class="label-sm block mb-1">Prioritas</label><select name="priority" class="input"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\FollowUp::PRIORITIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p); ?>" <?php if($f->priority === $p): echo 'selected'; endif; ?>><?php echo e(ucfirst(strtolower($p))); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></select></div>
            <div><label class="label-sm block mb-1">Due date</label><input type="date" name="due_date" value="<?php echo e(old('due_date', $f->due_date?->toDateString())); ?>" class="input"></div></div>
        <div class="flex justify-end gap-2"><button type="button" class="btn-secondary" @click="open = false">Batal</button><button class="btn-accent">Simpan</button></div>
    </form>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\program\laragon\www\ontrack-project-intelligence\resources\views/pages/follow-ups/show.blade.php ENDPATH**/ ?>