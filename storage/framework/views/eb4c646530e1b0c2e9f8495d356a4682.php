<div class="card">
    <div class="p-3 border-b border-line flex flex-wrap items-center gap-2">
        <input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari ID, nama, atau customer…" class="input !w-64" aria-label="Cari proyek">
        <select wire:model.live="level" class="input !w-auto" aria-label="Filter attention"><option value="">Semua attention</option><option value="HIGH">High</option><option value="MEDIUM">Medium</option><option value="LOW">Low</option></select>
        <select wire:model.live="status" class="input !w-auto" aria-label="Filter status"><option value="">Semua status</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>"><?php echo e(ucfirst(strtolower($s))); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></select>
        <select wire:model.live="pic" class="input !w-auto" aria-label="Filter PIC"><option value="">Semua PIC</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option><?php echo e($p); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></select>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $level || $status || $pic): ?><button wire:click="clear" class="btn-ghost">Reset filter</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div wire:loading class="text-[12px] text-ink-500">Memuat…</div>
    </div>
    <div class="overflow-x-auto">
        <table class="tbl">
            <thead><tr>
                <?php $cols = ['project_code' => 'ID', 'name' => 'Proyek', 'pic' => 'PM / PIC', 'reported_progress_pct' => 'Progress', 'deadline' => 'Deadline', 'att_score' => 'Attention', 'open_findings' => 'Temuan', 'latest_update' => 'Update terakhir']; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th><button wire:click="sortBy('<?php echo e($key); ?>')" class="inline-flex items-center gap-1 hover:text-ink"><?php echo e($label); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sort === $key): ?><span><?php echo e($dir === 'asc' ? '▲' : '▼'); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></button></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <th>Verifikasi issue</th>
            </tr></thead>
            <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $o = $ctx[$p->id]->observedProgress(); ?>
                <tr class="cursor-pointer" onclick="window.location='<?php echo e(route('projects.show', $p)); ?>'">
                    <td class="font-mono text-[12px]"><a href="<?php echo e(route('projects.show', $p)); ?>" class="text-accent-600 font-medium"><?php echo e($p->project_code); ?></a></td>
                    <td><div class="font-medium"><?php echo e($p->name); ?></div><div class="text-[12px] text-ink-500"><?php echo e($p->customer); ?></div></td>
                    <td><?php echo e($p->pic ?? '—'); ?></td>
                    <td class="min-w-[170px]">
                        <div class="flex items-center gap-2"><span class="badge bg-well text-ink-700 border-dashed border-slate-400" title="Dilaporkan">👤 <?php echo e($p->reported_progress_pct !== null ? rtrim(rtrim(number_format($p->reported_progress_pct, 1), '0'), '.') . '%' : 'n/a'); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($o): ?><span class="badge bg-good-bg text-good-fg border-good-bd" title="Observed: <?php echo e($o['label']); ?>">🛡 <?php echo e(rtrim(rtrim(number_format($o['pct'], 1), '0'), '.')); ?>%</span><?php else: ?><span class="text-[11px] text-ink-500">tanpa observed</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        <div class="h-1 bg-well rounded mt-1.5"><div class="h-1 bg-accent-600 rounded" style="width: <?php echo e(min(100, $p->reported_progress_pct ?? 0)); ?>%"></div></div>
                    </td>
                    <td><?php echo e($p->deadline?->format('d M Y') ?? '—'); ?></td>
                    <td><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->att_level): ?><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['value' => $p->att_level]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($p->att_level)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?> <span class="text-[12px] text-ink-500 tabular-nums"><?php echo e(number_format($p->att_score, 1)); ?></span><?php else: ?><span class="text-ink-500">—</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                    <td class="tabular-nums"><?php echo e($p->open_findings); ?></td>
                    <td class="text-[12px] text-ink-700 whitespace-nowrap"><?php echo e(\Carbon\Carbon::parse($p->latest_update)->format('d M Y H:i')); ?></td>
                    <td><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->verif_needed > 0): ?><span class="badge bg-pend-bg text-pend-fg border-pend-bd"><?php echo e($p->verif_needed); ?> perlu verifikasi</span><?php elseif($p->verif_ok > 0): ?><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['value' => 'VERIFIED','kind' => 'verification']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'VERIFIED','kind' => 'verification']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?><?php else: ?><span class="text-ink-500 text-[12px]">—</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="9"><?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['title' => 'Tidak ada proyek yang cocok','text' => 'Ubah filter atau pencarian, atau impor data di Data Management.','href' => route('data.index'),'action' => 'Buka Data Management']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Tidak ada proyek yang cocok','text' => 'Ubah filter atau pencarian, atau impor data di Data Management.','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('data.index')),'action' => 'Buka Data Management']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4f22a152e0729cd34293e65bd200d933)): ?>
<?php $attributes = $__attributesOriginal4f22a152e0729cd34293e65bd200d933; ?>
<?php unset($__attributesOriginal4f22a152e0729cd34293e65bd200d933); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4f22a152e0729cd34293e65bd200d933)): ?>
<?php $component = $__componentOriginal4f22a152e0729cd34293e65bd200d933; ?>
<?php unset($__componentOriginal4f22a152e0729cd34293e65bd200d933); ?>
<?php endif; ?></td></tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="p-3 border-t border-line flex items-center justify-between text-[12px] text-ink-500">
        <div><?php echo e($projects->total()); ?> proyek</div>
        <div><?php echo e($projects->links(data: ['scrollTo' => false])); ?></div>
    </div>
</div>
<?php /**PATH D:\program\laragon\www\ontrack-project-intelligence\resources\views/livewire/portfolio-table.blade.php ENDPATH**/ ?>