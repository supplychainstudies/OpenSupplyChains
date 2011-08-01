<?php $attr = $field->html_attrs(); $attr['class'] = $field->css_class(); ?>
<?php if($field->field_type() === Sourcemap_Form_Field::TEXTAREA): ?>
    <?= Form::label($field->name(), $field->label()) ?>
    <div class="sourcemap-form-textarea">
        <?= Form::textarea($field->name(), $field->value(), $attr) ?>
    </div>
    <?php $errors = $field->errors(); ?>
    <?php if (!empty($errors)) : ?>
        <div class="sourcemap-form-error">
        <?= $errors ?> 
        </div>
    <?php endif; ?>
<?php elseif($field->field_type() === Sourcemap_Form_Field::SELECT): ?>
    <?= Form::label($field->name(), $field->label()) ?>
    <div class="sourcemap-form-select">
            <?= Form::select($field->name(), 
                $field->options(), $field->selected(), $attr
            );
            ?>
    </div>
    <?php $errors = $field->errors(); ?>
    <?php if (!empty($errors)) : ?>
        <div class="sourcemap-form-error">
        <?= $errors ?> 
        </div>
    <?php endif; ?>
<?php elseif($field->field_type() === Sourcemap_Form_Field::SUBMIT): ?>
    <div class="sourcemap-form-button">
        <?php $attr['class'] = $field->css_class()." buttons"; ?>
        <?= Form::submit($field->name(), $field->value(), $attr) ?>
    </div>
<?php elseif($field->field_type() === Sourcemap_Form_Field::CHECKBOX): ?>
    <?= Form::label($field->name(), $field->label()) ?>
    <div class="sourcemap-form-checkbox">
        <?php $attr['type'] = $field->field_type(); ?>
        <?php $attr['class'] = $field->css_class()." textbox"; ?>
        <?= Form::checkbox($field->name(), null, $field->value(), $attr) ?>
    </div>
    <?php $errors = $field->errors(); ?>
    <?php if (!empty($errors)) : ?>
        <div class="sourcemap-form-error">
        <?= $errors ?> 
        </div>
    <?php endif; ?>
<?php else: ?>
    <?= Form::label($field->name(), $field->label()) ?>
    <div class="sourcemap-form-textbox">
        <?php $attr['type'] = $field->field_type(); ?>
        <?php $attr['class'] = $field->css_class()." textbox"; ?>
        <?= Form::input($field->name(), $field->value(), $attr ) ?>
    </div>
    <?php $errors = $field->errors(); ?>
    <?php if (!empty($errors)) : ?>
        <div class="sourcemap-form-error">
        <?= $errors ?> 
        </div>
    <?php endif; ?>
<?php endif; ?>
