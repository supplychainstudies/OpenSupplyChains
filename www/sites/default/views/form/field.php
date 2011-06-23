<?php if($field->field_type() === Sourcemap_Form_Field::TEXTAREA): ?>
    <?= Form::label($field->name(), $field->label()) ?>
    <div class="sourcemap-form-textarea">
        <?= Form::textarea($field->name(), $field->value(), array('class' => $field->css_class())) ?>
    </div>
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
<?php elseif($field->field_type() === Sourcemap_Form_Field::SUBMIT): ?>
    <div class="sourcemap-form-button-div">
        <?= Form::submit($field->name(), $field->value(), array('class' => $field->css_class())) ?>
    </div>
<?php else: ?>
    <?= Form::label($field->name(), $field->label()) ?>
    <div class="sourcemap-form-textbox">
        <?= Form::input($field->name(), $field->value(), array('type' => $field->field_type(), 'class' => $field->css_class() . " textbox")) ?>
    </div>
<?php endif; ?>
