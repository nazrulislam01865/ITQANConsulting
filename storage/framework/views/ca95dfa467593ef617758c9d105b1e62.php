<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['field', 'value' => null, 'inputName', 'path']));

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

foreach (array_filter((['field', 'value' => null, 'inputName', 'path']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $type = $field['type'] ?? 'text';
    $errorKey = preg_replace('/\[([^\]]+)\]/', '.$1', $inputName);
    $errorKey = ltrim($errorKey, '.');
    $inputId = 'field-'.preg_replace('/[^a-zA-Z0-9_-]+/', '-', $inputName);
?>

<?php if($type === 'collection'): ?>
    <?php
        $items = array_values(array_filter((array) $value, 'is_array'));
        $collectionHash = substr(md5($inputName), 0, 10);
        $collectionId = 'collection-'.$collectionHash;
        $indexToken = '__INDEX_'.$collectionHash.'__';
    ?>

    <section
        class="field-collection"
        id="<?php echo e($collectionId); ?>"
        data-collection
        data-index-token="<?php echo e($indexToken); ?>"
        data-next-index="<?php echo e(count($items)); ?>"
    >
        <div class="field-collection-heading">
            <div>
                <h3><?php echo e($field['label']); ?></h3>
                <?php if(isset($field['help'])): ?><p><?php echo e($field['help']); ?></p><?php endif; ?>
            </div>
            <div class="collection-heading-actions">
                <span data-collection-count><?php echo e(count($items)); ?> <?php echo e(count($items) === 1 ? 'item' : 'items'); ?></span>
                <button type="button" class="collection-add" data-add-collection>
                    <span>＋</span> Add item
                </button>
            </div>
        </div>

        <div class="collection-items" data-collection-items>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="collection-item" data-collection-item data-row-index="<?php echo e($index); ?>">
                    <div class="collection-item-tools">
                        <div class="collection-index" data-collection-index><?php echo e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></div>
                        <button type="button" class="collection-remove" data-remove-collection aria-label="Remove item <?php echo e($index + 1); ?>">
                            Remove
                        </button>
                    </div>
                    <div class="collection-fields">
                        <?php $__currentLoopData = $field['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if (isset($component)) { $__componentOriginal514bbb2f3a0bab007cc9f3cd4e63f162 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal514bbb2f3a0bab007cc9f3cd4e63f162 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.starpmaminul.admin.field','data' => ['field' => $child,'value' => data_get($item, $child['name']),'inputName' => $inputName.'['.$index.']['.$child['name'].']','path' => $path.'.'.$index.'.'.$child['name']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('starpmaminul.admin.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($child),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(data_get($item, $child['name'])),'input-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inputName.'['.$index.']['.$child['name'].']'),'path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($path.'.'.$index.'.'.$child['name'])]); ?>
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
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="collection-empty" data-collection-empty <?php if(count($items) > 0): ?> hidden <?php endif; ?>>
            <strong>No items yet</strong>
            <span>Use “Add item” to create the first entry.</span>
        </div>

        <template data-collection-template>
            <article class="collection-item" data-collection-item data-row-index="<?php echo e($indexToken); ?>">
                <div class="collection-item-tools">
                    <div class="collection-index" data-collection-index>01</div>
                    <button type="button" class="collection-remove" data-remove-collection aria-label="Remove item">
                        Remove
                    </button>
                </div>
                <div class="collection-fields">
                    <?php $__currentLoopData = $field['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if (isset($component)) { $__componentOriginal514bbb2f3a0bab007cc9f3cd4e63f162 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal514bbb2f3a0bab007cc9f3cd4e63f162 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.starpmaminul.admin.field','data' => ['field' => $child,'value' => null,'inputName' => $inputName.'['.$indexToken.']['.$child['name'].']','path' => $path.'.'.$indexToken.'.'.$child['name']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('starpmaminul.admin.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($child),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(null),'input-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inputName.'['.$indexToken.']['.$child['name'].']'),'path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($path.'.'.$indexToken.'.'.$child['name'])]); ?>
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
            </article>
        </template>

        <?php $__errorArgs = [$errorKey];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </section>
<?php elseif($type === 'select'): ?>
    <label class="form-field" for="<?php echo e($inputId); ?>">
        <span><?php echo e($field['label']); ?></span>
        <select id="<?php echo e($inputId); ?>" name="<?php echo e($inputName); ?>">
            <?php $__currentLoopData = ($field['options'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionValue => $optionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($optionValue); ?>" <?php if((string) $value === (string) $optionValue): echo 'selected'; endif; ?>><?php echo e($optionLabel); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php if(isset($field['help'])): ?><small class="field-help"><?php echo e($field['help']); ?></small><?php endif; ?>
        <?php $__errorArgs = [$errorKey];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </label>
<?php elseif($type === 'textarea'): ?>
    <label class="form-field form-field-block" for="<?php echo e($inputId); ?>">
        <span><?php echo e($field['label']); ?></span>
        <textarea id="<?php echo e($inputId); ?>" name="<?php echo e($inputName); ?>" rows="<?php echo e($field['rows'] ?? 4); ?>" placeholder="<?php echo e($field['placeholder'] ?? ''); ?>"><?php echo e($value); ?></textarea>
        <?php if(isset($field['help'])): ?><small class="field-help"><?php echo e($field['help']); ?></small><?php endif; ?>
        <?php $__errorArgs = [$errorKey];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </label>
<?php elseif($type === 'image'): ?>
    <?php
        $fileInputName = 'files['.str_replace('.', '][', $path).']';
        $removeInputName = 'remove_files['.str_replace('.', '][', $path).']';
    ?>
    <div class="form-field form-field-block image-field">
        <span><?php echo e($field['label']); ?></span>
        <input type="hidden" name="<?php echo e($inputName); ?>" value="<?php echo e($value); ?>">
        <div class="image-upload-row">
            <div class="image-preview">
                <?php if($value): ?>
                    <img src="<?php echo e(\App\Support\StarPmAminulMedia::url($value)); ?>" alt="Current <?php echo e(strtolower($field['label'])); ?>">
                <?php else: ?>
                    <span>No image uploaded</span>
                <?php endif; ?>
            </div>
            <label class="image-upload-control" for="<?php echo e($inputId); ?>">
                <strong><?php echo e($value ? 'Replace image' : 'Upload image'); ?></strong>
                <small>JPG, PNG or WebP · up to 5 MB</small>
                <input id="<?php echo e($inputId); ?>" type="file" name="<?php echo e($fileInputName); ?>" accept="image/jpeg,image/png,image/webp" data-image-input>
            </label>
        </div>
        <?php if($value): ?>
            <label class="remove-image-control">
                <input type="checkbox" name="<?php echo e($removeInputName); ?>" value="1">
                <span><?php echo e($field['remove_label'] ?? 'Remove this uploaded image and use the fallback mark'); ?></span>
            </label>
        <?php endif; ?>
        <?php if(isset($field['help'])): ?><small class="field-help"><?php echo e($field['help']); ?></small><?php endif; ?>
        <?php $__errorArgs = ['files.'.$path];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
<?php elseif($type === 'file'): ?>
    <?php
        $fileInputName = 'files['.str_replace('.', '][', $path).']';
        $removeInputName = 'remove_files['.str_replace('.', '][', $path).']';
        $accept = $field['accept'] ?? 'application/pdf,.pdf,.doc,.docx';
        $maxMb = max(1, (int) ceil(($field['max_kb'] ?? 10240) / 1024));
    ?>
    <div class="form-field form-field-block document-field">
        <span><?php echo e($field['label']); ?></span>
        <input type="hidden" name="<?php echo e($inputName); ?>" value="<?php echo e($value); ?>">
        <div class="image-upload-row document-upload-row">
            <div class="image-preview document-preview">
                <?php if($value): ?>
                    <div class="document-preview-copy">
                        <strong>Resume uploaded</strong>
                        <span><?php echo e(basename($value)); ?></span>
                        <a href="<?php echo e(\App\Support\StarPmAminulMedia::url($value)); ?>" target="_blank" rel="noopener">Open current file ↗</a>
                    </div>
                <?php else: ?>
                    <div class="document-preview-copy">
                        <strong>No resume uploaded</strong>
                        <span>The download button stays hidden until a file is added.</span>
                    </div>
                <?php endif; ?>
            </div>
            <label class="image-upload-control document-upload-control" for="<?php echo e($inputId); ?>">
                <strong><?php echo e($value ? 'Replace resume' : 'Upload resume'); ?></strong>
                <small>PDF, DOC or DOCX · up to <?php echo e($maxMb); ?> MB</small>
                <input id="<?php echo e($inputId); ?>" type="file" name="<?php echo e($fileInputName); ?>" accept="<?php echo e($accept); ?>">
            </label>
        </div>
        <?php if($value): ?>
            <label class="remove-image-control">
                <input type="checkbox" name="<?php echo e($removeInputName); ?>" value="1">
                <span><?php echo e($field['remove_label'] ?? 'Remove this uploaded document'); ?></span>
            </label>
        <?php endif; ?>
        <?php if(isset($field['help'])): ?><small class="field-help"><?php echo e($field['help']); ?></small><?php endif; ?>
        <?php $__errorArgs = ['files.'.$path];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
<?php else: ?>
    <label class="form-field" for="<?php echo e($inputId); ?>">
        <span><?php echo e($field['label']); ?></span>
        <input
            id="<?php echo e($inputId); ?>"
            type="<?php echo e(in_array($type, ['text', 'email', 'url', 'number', 'tel'], true) ? $type : 'text'); ?>"
            name="<?php echo e($inputName); ?>"
            value="<?php echo e($value); ?>"
            placeholder="<?php echo e($field['placeholder'] ?? ''); ?>"
            <?php if($type === 'number'): ?> step="<?php echo e($field['step'] ?? 'any'); ?>" <?php endif; ?>
        >
        <?php if(isset($field['help'])): ?><small class="field-help"><?php echo e($field['help']); ?></small><?php endif; ?>
        <?php $__errorArgs = [$errorKey];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </label>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/components/starpmaminul/admin/field.blade.php ENDPATH**/ ?>