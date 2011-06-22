<div class="sourcemap-form"> 
    <?= Form::open($form->action(), array(
        'enctype' => $form->enctype(),
        'method' => $form->method()
    )); ?>
    <?php foreach($form->get_fields() as $fnm => $field): ?>
    <?= $field ?>
    <?php endforeach; ?>
</div>
