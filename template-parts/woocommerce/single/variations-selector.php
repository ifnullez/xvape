<form id="variation-selector-form">
<?php $available_variations = $args->get_available_variations();
if(!empty( $available_variations )) { ?>
    <?php foreach ( $args->get_attributes() as $attribute_name => $options ){ ?>
        <select class="form-select form-select mb-3 mt-3" name="<?php echo $attribute_name; ?>">
            <option selected value=""><?php echo $options['name']; ?></option>
            <?php if(!empty($options['options'])){ ?>
                <?php foreach($options['options'] as $option){ ?>
                    <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
                <?php } ?>
            <?php } ?>
        </select>
    <?php } ?>
<?php } ?>
</form>
