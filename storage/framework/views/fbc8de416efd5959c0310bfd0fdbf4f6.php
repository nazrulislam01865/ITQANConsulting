<?php $__env->startSection('title', $definition['label']); ?>
<?php $__env->startSection('page-title', $definition['label']); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $formData = old('data', $data);
    ?>

    <div class="editor-heading">
        <div>
            <span class="content-eyebrow">Section editor</span>
            <h2><?php echo e($definition['label']); ?></h2>
            <p><?php echo e($definition['description']); ?></p>
        </div>
        <a href="<?php echo e(route('starpmaminul.portfolio')); ?>" target="_blank" rel="noopener" class="secondary-action">Preview site ↗</a>
    </div>

    <?php if($errors->any()): ?>
        <div class="alert alert-error" role="alert">
            <span>!</span>
            <div>
                <strong>Please review the highlighted fields.</strong>
                <p><?php echo e($errors->first()); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('starpmaminul.admin.sections.update', $sectionKey)); ?>" enctype="multipart/form-data" class="section-form" data-dirty-form>
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="form-panel">
            <?php $__currentLoopData = $definition['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if (isset($component)) { $__componentOriginal514bbb2f3a0bab007cc9f3cd4e63f162 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal514bbb2f3a0bab007cc9f3cd4e63f162 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.starpmaminul.admin.field','data' => ['field' => $field,'value' => data_get($formData, $field['name']),'inputName' => 'data['.$field['name'].']','path' => $field['name']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('starpmaminul.admin.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(data_get($formData, $field['name'])),'input-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('data['.$field['name'].']'),'path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field['name'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal514bbb2f3a0bab007cc9f3cd4e63f162)): ?>
<?php $attributes = $__attributesOriginal514bbb2f3a0bab007cc9f3cd4e63f162; ?>
<?php unset($__attributesOriginal514bbb2f3a0bab007cc9f3cd4e63f162); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal514bbb2f3a0bab007cc9f3cd4e63f162)): ?>
<?php $component = $__componentOriginal514bbb2f3a0bab007cc9f3cd4e63f162; ?>
<?php unset($__componentOriginal514bbb2f3a0bab007cc9f3cd4e63f162); ?>
<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="save-bar">
            <div>
                <strong>Save <?php echo e($definition['label']); ?></strong>
                <span>Changes appear on the public site immediately.</span>
            </div>
            <button type="submit" class="primary-action">Save changes <span>✓</span></button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('starpmaminul.admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/starpmaminul/admin/sections/edit.blade.php ENDPATH**/ ?>