@props(['field', 'value' => null, 'inputName', 'path'])

@php
    $type = $field['type'] ?? 'text';
    $errorKey = preg_replace('/\[([^\]]+)\]/', '.$1', $inputName);
    $errorKey = ltrim($errorKey, '.');
    $inputId = 'field-'.preg_replace('/[^a-zA-Z0-9_-]+/', '-', $inputName);
@endphp

@if($type === 'collection')
    @php
        $items = array_values(array_filter((array) $value, 'is_array'));
        $collectionHash = substr(md5($inputName), 0, 10);
        $collectionId = 'collection-'.$collectionHash;
        $indexToken = '__INDEX_'.$collectionHash.'__';
    @endphp

    <section
        class="field-collection"
        id="{{ $collectionId }}"
        data-collection
        data-index-token="{{ $indexToken }}"
        data-next-index="{{ count($items) }}"
    >
        <div class="field-collection-heading">
            <div>
                <h3>{{ $field['label'] }}</h3>
                @isset($field['help'])<p>{{ $field['help'] }}</p>@endisset
            </div>
            <div class="collection-heading-actions">
                <span data-collection-count>{{ count($items) }} {{ count($items) === 1 ? 'item' : 'items' }}</span>
                <button type="button" class="collection-add" data-add-collection>
                    <span>＋</span> Add item
                </button>
            </div>
        </div>

        <div class="collection-items" data-collection-items>
            @foreach($items as $index => $item)
                <article class="collection-item" data-collection-item data-row-index="{{ $index }}">
                    <div class="collection-item-tools">
                        <div class="collection-index" data-collection-index>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</div>
                        <button type="button" class="collection-remove" data-remove-collection aria-label="Remove item {{ $index + 1 }}">
                            Remove
                        </button>
                    </div>
                    <div class="collection-fields">
                        @foreach($field['fields'] as $child)
                            <x-starpmaminul.admin.field
                                :field="$child"
                                :value="data_get($item, $child['name'])"
                                :input-name="$inputName.'['.$index.']['.$child['name'].']'"
                                :path="$path.'.'.$index.'.'.$child['name']"
                            />
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>

        <div class="collection-empty" data-collection-empty @if(count($items) > 0) hidden @endif>
            <strong>No items yet</strong>
            <span>Use “Add item” to create the first entry.</span>
        </div>

        <template data-collection-template>
            <article class="collection-item" data-collection-item data-row-index="{{ $indexToken }}">
                <div class="collection-item-tools">
                    <div class="collection-index" data-collection-index>01</div>
                    <button type="button" class="collection-remove" data-remove-collection aria-label="Remove item">
                        Remove
                    </button>
                </div>
                <div class="collection-fields">
                    @foreach($field['fields'] as $child)
                        <x-starpmaminul.admin.field
                            :field="$child"
                            :value="null"
                            :input-name="$inputName.'['.$indexToken.']['.$child['name'].']'"
                            :path="$path.'.'.$indexToken.'.'.$child['name']"
                        />
                    @endforeach
                </div>
            </article>
        </template>

        @error($errorKey)<small class="field-error">{{ $message }}</small>@enderror
    </section>
@elseif($type === 'select')
    <label class="form-field" for="{{ $inputId }}">
        <span>{{ $field['label'] }}</span>
        <select id="{{ $inputId }}" name="{{ $inputName }}">
            @foreach(($field['options'] ?? []) as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>
        @isset($field['help'])<small class="field-help">{{ $field['help'] }}</small>@endisset
        @error($errorKey)<small class="field-error">{{ $message }}</small>@enderror
    </label>
@elseif($type === 'textarea')
    <label class="form-field form-field-block" for="{{ $inputId }}">
        <span>{{ $field['label'] }}</span>
        <textarea id="{{ $inputId }}" name="{{ $inputName }}" rows="{{ $field['rows'] ?? 4 }}" placeholder="{{ $field['placeholder'] ?? '' }}">{{ $value }}</textarea>
        @isset($field['help'])<small class="field-help">{{ $field['help'] }}</small>@endisset
        @error($errorKey)<small class="field-error">{{ $message }}</small>@enderror
    </label>
@elseif($type === 'image')
    @php
        $fileInputName = 'files['.str_replace('.', '][', $path).']';
        $removeInputName = 'remove_files['.str_replace('.', '][', $path).']';
    @endphp
    <div class="form-field form-field-block image-field">
        <span>{{ $field['label'] }}</span>
        <input type="hidden" name="{{ $inputName }}" value="{{ $value }}">
        <div class="image-upload-row">
            <div class="image-preview">
                @if($value)
                    <img src="{{ \App\Support\StarPmAminulMedia::url($value) }}" alt="Current {{ strtolower($field['label']) }}">
                @else
                    <span>No image uploaded</span>
                @endif
            </div>
            <label class="image-upload-control" for="{{ $inputId }}">
                <strong>{{ $value ? 'Replace image' : 'Upload image' }}</strong>
                <small>JPG, PNG or WebP · up to 5 MB</small>
                <input id="{{ $inputId }}" type="file" name="{{ $fileInputName }}" accept="image/jpeg,image/png,image/webp" data-image-input>
            </label>
        </div>
        @if($value)
            <label class="remove-image-control">
                <input type="checkbox" name="{{ $removeInputName }}" value="1">
                <span>{{ $field['remove_label'] ?? 'Remove this uploaded image and use the fallback mark' }}</span>
            </label>
        @endif
        @isset($field['help'])<small class="field-help">{{ $field['help'] }}</small>@endisset
        @error('files.'.$path)<small class="field-error">{{ $message }}</small>@enderror
    </div>
@else
    <label class="form-field" for="{{ $inputId }}">
        <span>{{ $field['label'] }}</span>
        <input
            id="{{ $inputId }}"
            type="{{ in_array($type, ['text', 'email', 'url', 'number', 'tel'], true) ? $type : 'text' }}"
            name="{{ $inputName }}"
            value="{{ $value }}"
            placeholder="{{ $field['placeholder'] ?? '' }}"
            @if($type === 'number') step="{{ $field['step'] ?? 'any' }}" @endif
        >
        @isset($field['help'])<small class="field-help">{{ $field['help'] }}</small>@endisset
        @error($errorKey)<small class="field-error">{{ $message }}</small>@enderror
    </label>
@endif
