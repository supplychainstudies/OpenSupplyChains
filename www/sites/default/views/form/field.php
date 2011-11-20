<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/ 
?>

<?php $attr = $field->html_attrs(); $attr['class'] = $field->css_class(); ?>

<?php if($field->field_type() === Sourcemap_Form_Field::TEXTAREA): ?>
    <?= Form::label($field->name(), $field->label()) ?>
    <?= Form::textarea($field->name(), $field->value(), $attr) ?>

    <?php $errors = $field->errors(); ?>
    <?php if (!empty($errors)) : ?>
        <div class="sourcemap-form-error">
        <?= $errors ?> 
        </div>
    <?php endif; ?>

<?php elseif($field->field_type() === Sourcemap_Form_Field::SELECT): ?>
    <?= Form::label($field->name(), $field->label()) ?>
            <?= Form::select($field->name(), 
                $field->options(), $field->selected(), $attr
            );
            ?>
    <?php $errors = $field->errors(); ?>
    <?php if (!empty($errors)) : ?>
        <div class="sourcemap-form-error">
        <?= $errors ?> 
        </div>
    <?php endif; ?>

<?php elseif($field->field_type() === Sourcemap_Form_Field::SUBMIT): ?>
    <div class="clear"></div>
    <div class="submit-status hidden"></div>
    <?php $attr['class'] = $field->css_class()."button form-button"; ?>
    <?= Form::submit($field->name(), $field->value(), $attr) ?>

<?php elseif($field->field_type() === Sourcemap_Form_Field::CHECKBOX): ?>
    <?= Form::label($field->name(), $field->label()) ?>
    <?php $attr['type'] = $field->field_type(); ?>
    <?php $attr['class'] = $field->css_class()."textbox"; ?>
    <?= Form::checkbox($field->name(), null, (bool)$field->value(), $attr) ?>
    <?php $errors = $field->errors(); ?>
    <?php if (!empty($errors)) : ?>
        <div class="sourcemap-form-error">
        <?= $errors ?> 
        </div>
    <?php endif; ?>

<?php elseif($field->field_type() === Sourcemap_Form_Field::TEXT): ?>
    <?php if($field->css_class()): ?>
    <div class="<?= $field->css_class() ?>">
    <?php endif; ?>
    <?= Form::label($field->name(), $field->label()) ?>
        <?php $attr['type'] = $field->field_type(); ?>
        <?php $attr['class'] = "textbox"; ?>
        <?= Form::input($field->name(), $field->value(), $attr ) ?>
    <?php $errors = $field->errors(); ?>
    <?php if (!empty($errors)) : ?>
        <div class="sourcemap-form-error">
        <?= $errors ?> 
        </div>
    <?php endif; ?>
    <?php if($field->css_class()): ?>
    </div>
    <?php endif; ?>

<?php elseif($field->field_type() === Sourcemap_Form_Field::PASSWORD): ?>
    <?= Form::label($field->name(), $field->label()) ?>
        <?php $attr['type'] = $field->field_type(); ?>
        <?php $attr['class'] = $field->css_class()."textbox"; ?>
        <?= Form::input($field->name(), $field->value(), $attr ) ?>
    <?php $errors = $field->errors(); ?>
    <?php if (!empty($errors)) : ?>
        <div class="sourcemap-form-error">
        <?= $errors ?> 
        </div>
    <?php endif; ?>

<?php else: ?>    
    <?= Form::label($field->name(), $field->label()) ?>
        <?php $attr['type'] = $field->field_type(); ?>
        <?php $attr['class'] = $field->css_class()." textbox"; ?>
        <?= Form::input($field->name(), $field->value(), $attr ) ?>
    <?php $errors = $field->errors(); ?>
    <?php if (!empty($errors)) : ?>
        <div class="sourcemap-form-error">
        <?= $errors ?> 
        </div>
    <?php endif; ?>
<?php endif; ?>
