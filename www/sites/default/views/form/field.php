<?php if($field->field_type() === Sourcemap_Form_Field::TEXTAREA): ?>
    <?= Form::label($field->name(), $field->label()) ?>
    <div class="sourcemap-form-textarea">
        <?= Form::textarea($field->name(), $field->value(), array('class' => $field->css_class())) ?>
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
                $field->options(), $field->selected(),
                array(
                    'class' => $field->css_class()
                )
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
        <?= Form::submit($field->name(), $field->value(), array('class' => $field->css_class() . " buttons")) ?>
    </div>
<?php elseif($field->field_type() === Sourcemap_Form_Field::CHECKBOX): ?>
    <?= Form::label($field->name(), $field->label()) ?>
    <div class="sourcemap-form-checkbox">
        <?= Form::checkbox($field->name(), null, $field->value(), array('type' => $field->field_type(), 'class' => $field->css_class() . " textbox")) ?>
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
        <?= Form::input($field->name(), $field->value(), array('type' => $field->field_type(), 'class' => $field->css_class() . " textbox")) ?>
    </div>
    <?php $errors = $field->errors(); ?>
    <?php if (!empty($errors)) : ?>
        <div class="sourcemap-form-error">
        <?= $errors ?> 
        </div>
    <?php endif; ?>
<?php endif; ?>
