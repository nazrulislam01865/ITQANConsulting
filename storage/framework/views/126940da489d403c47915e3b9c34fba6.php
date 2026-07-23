<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <section class="dashboard-hero">
        <div>
            <span class="content-eyebrow">Content overview</span>
            <h2>Manage every portfolio section from one focused workspace.</h2>
            <p>Each card opens only the fields used by that frontend section, keeping updates fast and the original visual design protected.</p>
        </div>
        <a href="<?php echo e(route('starpmaminul.portfolio')); ?>" target="_blank" rel="noopener" class="secondary-action">Open live portfolio ↗</a>
    </section>

    <section class="dashboard-stats" aria-label="Portfolio status">
        <article>
            <span><?php echo e(count($sections)); ?></span>
            <p>Editable sections</p>
        </article>
        <article>
            <span><?php echo e(collect($content)->filter(fn ($data) => filled($data))->count()); ?></span>
            <p>Sections configured</p>
        </article>
        <article>
            <span>1</span>
            <p>Public portfolio</p>
        </article>
    </section>

    <section class="section-card-grid">
        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('starpmaminul.admin.sections.edit', $key)); ?>" class="section-card">
                <div class="section-card-top">
                    <span><?php echo e(str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)); ?></span>
                    <i>↗</i>
                </div>
                <div>
                    <h3><?php echo e($section['label']); ?></h3>
                    <p><?php echo e($section['description']); ?></p>
                </div>
                <small>Manage section</small>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('starpmaminul.admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/starpmaminul/admin/dashboard.blade.php ENDPATH**/ ?>