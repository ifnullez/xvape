<?php $icons = carbon_get_theme_option('social_icons'); 
if(!empty($icons)){ ?>
    <div class="social">
    <?php foreach($icons as $icon){ ?>
        <a href="<?php echo !empty($icon['link']) ? $icon['link'] : '#'; ?>" target="_blank" class="btn social_item">
            <i class="<?php echo strtolower($icon['icon']['class']); ?>"></i>
        </a>
    <?php } ?>
    </div>
<?php }